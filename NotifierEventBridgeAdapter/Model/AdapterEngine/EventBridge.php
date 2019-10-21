<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventBridgeAdapter\Model\AdapterEngine;

use Aws\EventBridge\EventBridgeClient;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;
use Magento\NotifierEventBridgeAdapter\Model\AdapterEngine\EventBridge\ClientRepository;
use Magento\Store\Model\StoreManagerInterface;

class EventBridge implements AdapterEngineInterface
{
    /**
     * Adapter code parameter name
     */
    public const ADAPTER_CODE = 'event_bridge';

    /**
     * Version parameter name
     */
    private const PARAM_VERSION = 'api_version';

    /**
     * API key parameter name
     */
    private const PARAM_API_KEY = 'api_key';

    /**
     * API secret parameter name
     */
    private const PARAM_API_SECRET = 'api_secret';

    /**
     * Region parameter name
     */
    private const PARAM_REGION = 'region';

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EventBridge\ClientRepository
     */
    private $clientRepository;

    /**
     * @param EventBridge\ClientRepository $clientRepository
     * @param StoreManagerInterface $storeManager
     * @param SerializerInterface $serializer
     * @param DateTime $dateTime
     */
    public function __construct(
        ClientRepository $clientRepository,
        StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function execute(string $message, array $configParams = [], array $params = []): bool
    {
        $client = $this->getClient($configParams);

        $params['message'] = $message;
        $response = $client->putEvents(['Entries' => [[
            'Detail' => $this->serializer->serialize($params),
            'DetailType' => $params['name'] ?? 'magento-notifier',
            'Source' => $this->storeManager->getStore()->getBaseUrl(),
            'Time' => $this->dateTime->gmtDate()
        ]]]);

        if ($response->get('FailedEntryCount') > 0) {
            foreach ($response->get('Entries') as $error) {
                throw new LocalizedException(__(
                    'An error occurred while trying to send the message: %1',
                    $error['ErrorMessage']
                ));
            }
        }

        return true;
    }

    /**
     * @param array $params
     * @return EventBridgeClient
     */
    private function getClient(array $params): EventBridgeClient
    {
        return $this->clientRepository->get([
            'version' => $params[self::PARAM_VERSION],
            'region' =>  $params[self::PARAM_REGION],
            'credentials' => [
                'key' => $params[self::PARAM_API_KEY],
                'secret' => $params[self::PARAM_API_SECRET],
            ]
        ]);
    }
}
