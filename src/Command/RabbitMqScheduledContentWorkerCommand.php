<?php

namespace App\Command;

use App\Service\RabbitmqService;
use App\Worker\Worker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'RabbitMqScheduledContentWorkerCommand'
)]
class RabbitMqScheduledContentWorkerCommand extends Command
{

    public function __construct(private RabbitmqService $rabbitMQService)
    {
        parent::__construct();

    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $this->rabbitMQService->processScheduledContentMessages('schedule_content_queue');

        return Command::SUCCESS;
    }
}
