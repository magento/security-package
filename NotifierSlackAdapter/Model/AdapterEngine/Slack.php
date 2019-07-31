<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierSlackAdapter\Model\AdapterEngine;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Maknz\Slack\Client;
use MSP\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;
use MSP\NotifierSlackAdapter\Model\AdapterEngine\Slack\ClientFactory;

class Slack implements AdapterEngineInterface
{
    /**
     * Adapter code
     */
    public const ADAPTER_CODE = 'slack';

    /**
     * Parameter name for color settings
     */
    private const PARAM_COLOR = 'color';

    /**
     * Parameter name for emoji
     */
    private const PARAM_EMOJI  = 'emoji';

    /**
     * Parameter name for channel name
     */
    private const PARAM_CHANNEL = 'channel';

    /**
     * Parameter for webhook name
     */
    private const PARAM_WEBHOOK = 'webhook';

    /**
     * Default emoji
     */
    private const DEFAULT_EMOJI = ':bear:';

    /**
     * Default color
     */
    private const DEFAULT_COLOR = '#333333';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ClientFactory $clientFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ClientFactory $clientFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->clientFactory = $clientFactory;
    }

    /**
     * Execute engine and return true on success. Throw exception on failure.
     * @param string $message
     * @param array $params
     * @return bool
     */
    public function execute(string $message, array $params = []): bool
    {
        $client = $this->getClient($params);

        $client->attach([
            'fallback' => $message,
            'text' => $message,
            'color' => $params[static::PARAM_COLOR] ?: static::DEFAULT_COLOR
        ])->send();

        return true;
    }

    /**
     * @param array $params
     * @return array
     */
    private function paramsToSettings(array $params): array
    {
        return [
            'userName' => $this->scopeConfig->getValue('general/store_information/name'),
            'channel' => $params[static::PARAM_CHANNEL],
            'icon' => $params[static::PARAM_EMOJI] ?: static::DEFAULT_EMOJI
        ];
    }

    /**
     * @param array $params
     * @return Client
     */
    private function getClient(array $params): Client
    {
        $settings = $this->paramsToSettings($params);
        return $this->clientFactory->create($params[static::PARAM_WEBHOOK], $settings);
    }
}
