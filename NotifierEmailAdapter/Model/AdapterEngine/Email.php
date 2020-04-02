<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEmailAdapter\Model\AdapterEngine;

use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MimeInterface;
use Magento\Framework\Mail\MimeMessageInterface;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\TransportInterfaceFactory;
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
     * @var EmailMessageInterfaceFactory
     */
    private $emailMessageFactory;

    /**
     * @var TransportInterfaceFactory
     */
    private $transportFactory;

    /**
     * @var MimePartInterfaceFactory
     */
    private $mimePartInterfaceFactory;

    /**
     * @var MimeMessageInterfaceFactory
     */
    private $mimeMessageFactory;

    /**
     * @var AddressConverter
     */
    private $addressConverter;

    /**
     * @param EmailMessageInterfaceFactory $emailMessageFactory
     * @param MimePartInterfaceFactory $mimePartInterfaceFactory
     * @param MimeMessageInterfaceFactory $mimeMessageFactory
     * @param AddressConverter $addressConverter
     * @param TransportInterfaceFactory $transportFactory
     * @SuppressWarnings(PHPMD.LongVariables)
     */
    public function __construct(
        EmailMessageInterfaceFactory $emailMessageFactory,
        MimePartInterfaceFactory $mimePartInterfaceFactory,
        MimeMessageInterfaceFactory $mimeMessageFactory,
        AddressConverter $addressConverter,
        TransportInterfaceFactory $transportFactory
    ) {
        $this->emailMessageFactory = $emailMessageFactory;
        $this->transportFactory = $transportFactory;
        $this->mimePartInterfaceFactory = $mimePartInterfaceFactory;
        $this->mimeMessageFactory = $mimeMessageFactory;
        $this->addressConverter = $addressConverter;
    }

    /**
     * Execute engine and return true on success. Throw exception on failure.
     * @param string $message
     * @param array $configParams
     * @param array $params
     * @return bool
     * @throws MailException
     */
    public function execute(string $message, array $configParams = [], array $params = []): bool
    {
        $lines = explode("\n", $message);
        $to = preg_split('/\s+/', $configParams[self::ADAPTER_TO]);

        $emailMessage = $this->emailMessageFactory->create(
            [
                'body' => $this->createMimeMessage($message),
                'to' => $this->addressConverter->convertMany($to),
            ]
        );

        $emailMessage->setFromAddress($configParams[self::ADAPTER_FROM], $configParams[self::ADAPTER_FROM_NAME]);
        $emailMessage->setSubject($lines[0]);

        $transport = $this->transportFactory->create(['message' => $emailMessage]);
        $transport->sendMessage();

        return true;
    }

    /**
     * Create mime message.
     *
     * @param string $message
     * @return MimeMessageInterface
     */
    private function createMimeMessage(string $message): MimeMessageInterface
    {
        $mimePart = $this->mimePartInterfaceFactory->create(
            [
                'content' => $message,
                'type' => MimeInterface::TYPE_TEXT
            ]
        );

        return $this->mimeMessageFactory->create(
            [
                'parts' => [$mimePart]
            ]
        );
    }
}
