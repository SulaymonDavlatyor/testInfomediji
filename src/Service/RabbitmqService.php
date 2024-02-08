<?php

namespace App\Service;

use App\Repository\ContentRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Predis\Client;

class RabbitmqService
{
    private $connection;
    private $channel;

    public function __construct(
        private ContentRepository $contentRepository,
        private SubscriptionRepository $subscriptionRepository,
        private Client $redis,
        private NotificationService $notificationService
    )
    {
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'user', 'password');
        $this->channel = $this->connection->channel();
    }

    public function sendScheduledContent(int $id, int $version, DateTimeInterface $releaseDate){
        //Here i just make delayed message, If I had more time, I would probably find a better tool for this task.
        $diffInMiliseconds = ($releaseDate->getTimestamp() - time()) * 1000;

        $this->channel->queue_declare('schedule_content_queue', false, true, false, false);
        $this->channel->queue_bind('schedule_content_queue', 'delayed_exchange', 'schedule_content_queue');


        $body = json_encode(['id' => $id, 'version' => $version]);

        $message = new AMQPMessage($body, array(
            'delivery_mode' => 2,
            'application_headers' => new AMQPTable([
                'x-delay' => $diffInMiliseconds
            ])
        ));

        $this->channel->basic_publish($message, 'delayed_exchange', 'schedule_content_queue');
    }

    protected function sendNotificationEvent(int $subscriberId, int $contentId){
        $this->channel->queue_declare('notification_queue', false, true, false, false);

        $body = json_encode(['subscriberId' => $subscriberId, 'contentId' => $contentId]);
        $message = new AMQPMessage($body, ['delivery_mode' => 2]);

        $this->channel->basic_publish($message, '', 'notification_queue');
    }

    public function processScheduledContentMessages(string $queue)
    {
        $callback = function ($msg) {
            $body = json_decode($msg->body,1);
            //I really didnt want to make this request to the database, but didnt find a better way to make rescheduling work
            $content = $this->contentRepository->findOneBy(['id' => $body['id'], 'version' => $body['version']]);
            if($content){
                $subscribers = $this->subscriptionRepository->findBy(['subscribedToId' => $content->getUserId()]);
                foreach($subscribers as $subscriber){
                    $this->sendNotificationEvent($subscriber->getSubscriberId(), $body['id']);
                }
            }

            $msg->ack();
        };

        $this->channel->basic_consume($queue, '', false, false, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function processNotificationCreateMessages()
    {
        $callback = function ($msg) {
            $body = json_decode($msg->body);
            $content = $this->redis->get($body->contentId);

            if(!$content){
                $contentEntity = $this->contentRepository->findOneBy(['id' => $body->contentId]);
                //With more time provided I would do it in a better way
                $content = [
                    'title' => $contentEntity->getTitle(),
                    'description' => $contentEntity->getDescription(),
                    'userId' => $contentEntity->getUserId()
                ];

                $this->redis->set($content->getId(), json_encode($content));
            }

            $this->notificationService->NotifyUser($body->subscriberId, $content);

            $msg->ack();
        };

        $this->channel->basic_consume('notification_queue', '', false, false, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}