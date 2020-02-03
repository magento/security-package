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
use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;

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
     * @param MailMessageInterfaceFactory $messageFactory
     * @param TransportInterfaceFactory $transportFactory
     * @SuppressWarnings(PHPMD.LongVariables)
     */
    public function __construct(
        MailMessageInterfaceFactory $messageFactory,
        TransportInterfaceFactory $transportFactory
    ) {
        $this->mailMessageFactory = $messageFactory;
        $this->transportFactory = $transportFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(MessageInterface $message): void
    {
        $messageText= $message->getMessage();
        $configParams = $message->getParams();

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
