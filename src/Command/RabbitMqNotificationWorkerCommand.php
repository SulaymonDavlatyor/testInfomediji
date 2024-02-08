<?php

namespace App\Command;

use App\Service\RabbitmqService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'RabbitMqNotificationWorkerCommand',
    description: 'Add a short description for your command',
)]
class RabbitMqNotificationWorkerCommand extends Command
{
    public function __construct(private RabbitmqService $rabbitmqService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->rabbitmqService->processNotificationCreateMessages();
        return Command::SUCCESS;
    }
}
