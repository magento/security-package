<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEmailAdapter\Model\AdapterEngine;

use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\MailMessageInterface;
use Magento\Framework\Mail\MailMessageInterfaceFactory;
use Magento\Framework\Mail\TransportInterfaceFactory;
use MSP\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;

class Email implements AdapterEngineInterface
{
    /**
     * Adapter code for email adapter
     */
    public const ADAPTER_CODE = 'email';

    /**
     * Adapter form field
     */
    public const ADAPTER_FROM = 'from';

    /**
     * Adapter from name field
     */
    public const ADAPTER_FROM_NAME = 'from_name';

    /**
     * To field name
     */
    public const ADAPTER_TO = 'to';

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
     * Execute engine and return true on success. Throw exception on failure.
     * @param string $message
     * @param array $params
     * @return bool
     * @throws MailException
     */
    public function execute(string $message, array $params = []): bool
    {
        $lines = explode("\n", $message);

        /** @var MailMessageInterface $emailMessage */
        $emailMessage = $this->mailMessageFactory->create();

        $emailMessage->setFromAddress($params[self::ADAPTER_FROM], $params[self::ADAPTER_FROM_NAME]);
        $emailMessage->addTo($params[self::ADAPTER_TO]);
        $emailMessage->setBodyText($message);
        $emailMessage->setSubject($lines[0]);

        $transport = $this->transportFactory->create(['message' => $emailMessage]);
        $transport->sendMessage();

        return true;
    }
}
