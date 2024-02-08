docker compose up -d\
symfony serve\
php bin/console do:mi:mi\
php bin/console doctrine:fixtures:load\
php bin/console RabbitMqNotificationWorkerCommand // worker\
php bin/console RabbitMQScheduledContentWorkerCommand // worker\
\
I added jwt, so  with postman localhost/api/login_check\
data to get token - [user1@example.com, root]\
after create content\
localhost/api/content   POST/PUT\
you need to pass title,description and release_date 2023-12-20\


## Architectural Decisions and Choice of Tools/Libraries

1) First of all, I want to note that I made a mistake. Being sick while completing a task, I slightly misread the assignment and added features for rescheduling the content release, although it was not mentioned.
   The entire architecture I made revolves around queues. When a user uploads/edits content in a delayed queue, an event is sent to RabbitMQ, which will be processed at the user-specified time (release_date). When this event is processed, it creates a bunch of notification sending events in a separate queue, allowing the system to be easily scalable.
   I used Symfony, mostly because I worked on it last year and it was simply more convenient, and generally, for large projects, I prefer Symfony over Laravel. I'm not a big fan of Eloquent and a lot of "Magic."
   I used Redis for caching, it was needed so that each notification event did not make a database query thereby greatly reducing the load.
   I used RabbitMQ for queues because I consider this tool extremely flexible and customizable for most goals. + I have not worked with Kafka :)

## Tradeoffs in Design and Decision Making

2) My main problem was the delayed message and changing the content release date, as nothing can be extracted from the queue anymore. I solved the problem of changing the release date by adding versions to the content, that is, when editing the content, the version changes and a new event is sent. When the old event reaches processing, there will be a version check, so the old message will not be processed and everything will be fine. I really wanted to make a more elegant solution, but comparing release_date with the current time +/- looked incredibly unreliable, and nothing better than versions came to mind. Ideally, study the topic better and find an option without a request to the DB, as the request occurs only for the sake of version comparison, that is, only for the possibility of editing the release time. I think there is some option where the message can be deleted, but I did not find it.
   Also, there is a problem with the delayed message, as the script takes time to execute, TTL is not absolutely accurate, so I think it can lag for a conditional second, depending on the capabilities of the server. But I still decided to use precisely delayed messages, as I did not want the cron to non-stop check for a new release, as I consider it to be a much larger and most importantly unnecessary load. I read that there are more advanced schedulers that can be set to just release content at a specified time, but I simply have not worked with them and therefore did not have enough expertise.

## Improvements for a Real Project

3) Regarding this architecture, I would look at alternatives to delayed messages, in which you can directly set the time and calmly interact, while not being too resource-intensive, but at the moment I simply do not know about such. That is, I would not like to hang a ton of tasks in cron and also from the PHP code.
   As for the processing of messages, I believe that a queue for notification events in conjunction with caching is a good solution, easily scalable.
   I would definitely improve the quality of the code, a lot needs to be put into constants, DTOs need to be placed in many places, part can be scattered. And in general, a conditional hexagon for architecture I now like to put, so I would have thrown it in there too.
