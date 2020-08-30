<?php

namespace App\Command;

use App\MessageBroker;
use Interop\Amqp\AmqpConsumer;
use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class JobsFileValidationConsumerCommand extends Command
{
    protected static string $defaultName = 'run';

    const QUEUE = 'ac_crm_jobs_file_validation';

    private AmqpContext $context;

    /**
     * Setup the command.
     */
    protected function configure()
    {
        $this->setDescription(
            'Validate a jobs file from the queue in message broker.'
        );

        $this->context = MessageBroker::context();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $queue = $this->context->createQueue(self::QUEUE);
        $consumer = $this->context->createConsumer($queue);
        $message = $consumer->receive();

        try {
            $this->process($consumer, $message, $io);
        } catch (\Exception $e) {
            $io->error('Caught ' . get_class($e) . ': ' . $e->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

    /**
     * "Validate" the file from message content.
     *
     * @param AmqpConsumer $consumer
     * @param AmqpMessage|null $message
     * @param SymfonyStyle $io
     */
    private function process(AmqpConsumer $consumer, ?AmqpMessage $message, SymfonyStyle $io)
    {
        $messageBody = json_decode($message->getBody(), true);

        // TODO: fetch file from S3/whatever and validate its content, etc.

        $io->comment('Message content:');
        $io->table(array_keys($messageBody), [array_values($messageBody)]);

        $wait = rand(2, 5);

        // Simulate validation
        sleep($wait);

        $success = rand(0, 100) > 3;

        if ($success) {
            $io->success('Sending acknowledgement!');
            $consumer->acknowledge($message);
        } else {
            $io->error('Sending rejection!');
            $consumer->reject($message, false);
        }
    }

}