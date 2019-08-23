<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Command;

use Magento\Framework\ObjectManagerInterface;
use Magento\NotifierApi\Api\SendMessageInterface\Proxy as SendMessageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendMessage extends Command
{
    /**
     * @var SendMessageInterface
     */
    private $sendMessage;

    /**
     * SendMessage constructor.
     * @param ObjectManagerInterface $sendMessage
     */
    public function __construct(
        SendMessageInterface $sendMessage
    ) {
        parent::__construct();
        $this->sendMessage = $sendMessage;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('magento:notifier:send');
        $this->setDescription('Send a notification message');

        $this->addArgument('channel', InputArgument::REQUIRED, 'Channel');
        $this->addArgument('message', InputArgument::REQUIRED, 'Message');

        parent::configure();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channel = $input->getArgument('channel');
        $message = $input->getArgument('message');

        if ($this->sendMessage->execute($channel, $message)) {
            $output->writeln('Message sent');
        } else {
            $output->writeln('Could not send message');
        }
    }
}
