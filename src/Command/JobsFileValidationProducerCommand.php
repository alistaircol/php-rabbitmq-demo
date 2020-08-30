<?php

namespace App\Command;

use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpTopic;
use Interop\Queue\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\MessageBroker;

class JobsFileValidationProducerCommand extends Command
{
    protected static string $defaultName = 'run';

    const EXCHANGE = 'ac_crm';
    const ROUTING_KEY = 'ac.crm.jobs.file.validation';

    private AmqpContext $context;
    private AmqpTopic $exchange;

    /**
     * Setup the command.
     */
    protected function configure()
    {
        $this->setDescription(
            'Publish a jobs file to message broker for deferred validation.'
        );

        $this->context = MessageBroker::context();
        $this->exchange = $this->context->createTopic(self::EXCHANGE);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $fileId = uniqid();
        $io->writeln('Publishing message for file: ' . $fileId);

        $message = $this->context->createMessage(
            json_encode([
                'file_id' => $fileId,
            ])
        );
        $message->setRoutingKey(self::ROUTING_KEY);

        try {
            // publish message to the exchange
            // message has a routing key attached
            // so message broker knows which queue to push to
            $this->context
                ->createProducer()
                ->send(
                    $this->exchange,
                    $message
                );
        } catch (Exception $e) {
            $io->error('Caught ' . get_class($e) . ': ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}