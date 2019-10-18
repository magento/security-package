<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTelegramAdapter\Model\AdapterEngine;

use Magento\Framework\Exception\LocalizedException;
use Magento\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;
use Magento\NotifierTelegramAdapter\Model\AdapterEngine\Telegram\ClientRepository;
use Psr\Log\LoggerInterface;

class Telegram implements AdapterEngineInterface
{
    /**
     * Adapter code parameter name
     */
    public const ADAPTER_CODE = 'telegram';

    /**
     * Token parameter name
     */
    private const PARAM_TOKEN = 'token';

    /**
     * Chat id parameter name
     */
    private const PARAM_CHAT_ID = 'chat_id';

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ClientRepository $clientRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        ClientRepository $clientRepository,
        LoggerInterface $logger
    ) {
        $this->clientRepository = $clientRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function execute(string $message, array $params = []): bool
    {
        $client = $this->clientRepository->get($params[self::PARAM_TOKEN]);

        try {
            $client->sendMessage(
                $params[self::PARAM_CHAT_ID],
                $message,
                'HTML'
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw new LocalizedException(__('Unable to send notifier message'));
        }

        return true;
    }
}
