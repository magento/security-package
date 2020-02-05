<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEmailAdapter\Model\AdapterEngine;

use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\MailMessageInterface;
use Magento\Framework\Mail\MailMessageInterfaceFactory;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Notifier\Model\GetChannelConfiguration;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;

/**
 * Email adapter engine for sending notifier messages.
 */
class Email implements AdapterEngineInterface
{
    /**
     * Adapter code for email adapter
     */
    public const ADAPTER_CODE = 'email';

    /**
     * Adapter form field
     */
    private const ADAPTER_FROM = 'from';

    /**
     * Adapter from name field
     */
    private const ADAPTER_FROM_NAME = 'from_name';

    /**
     * To field name
     */
    private const ADAPTER_TO = 'to';

    /**
     * @var MailMessageInterfaceFactory
     */
    private $mailMessageFactory;

    /**
     * @var TransportInterfaceFactory
     */
    private $transportFactory;

    /**
     * @var GetChannelConfiguration
     */
    private $getChannelConfiguration;

    /**
     * @param MailMessageInterfaceFactory $messageFactory
     * @param TransportInterfaceFactory $transportFactory
     * @param GetChannelConfiguration $getChannelConfiguration
     * @SuppressWarnings(PHPMD.LongVariables)
     */
    public function __construct(
        MailMessageInterfaceFactory $messageFactory,
        TransportInterfaceFactory $transportFactory,
        GetChannelConfiguration $getChannelConfiguration
    ) {
        $this->mailMessageFactory = $messageFactory;
        $this->transportFactory = $transportFactory;
        $this->getChannelConfiguration = $getChannelConfiguration;
    }

    /**
     * @inheritdoc
     */
    public function execute(ChannelInterface $channel, MessageInterface $message): void
    {
        $messageText = $message->getMessage();
        $configParams = $this->getChannelConfiguration->execute($channel);

        $lines = explode("\n", $messageText);

        /** @var MailMessageInterface $emailMessage */
        $emailMessage = $this->mailMessageFactory->create();

        $emailMessage->setFromAddress($configParams[self::ADAPTER_FROM], $configParams[self::ADAPTER_FROM_NAME]);
        $emailMessage->addTo($configParams[self::ADAPTER_TO]);
        $emailMessage->setBodyText($messageText);
        $emailMessage->setSubject($lines[0]);

        $transport = $this->transportFactory->create(['message' => $emailMessage]);
        $transport->sendMessage();
    }
}
