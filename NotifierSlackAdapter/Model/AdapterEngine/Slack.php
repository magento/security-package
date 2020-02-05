<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierSlackAdapter\Model\AdapterEngine;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierApi\Api\GetChannelConfigurationInterface;
use Maknz\Slack\Client;
use Magento\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;
use Magento\NotifierSlackAdapter\Model\AdapterEngine\Slack\ClientFactory;

/**
 * Slack adapter engine for sending notifier messages.
 */
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
    private const PARAM_EMOJI = 'emoji';

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
     * @var GetChannelConfigurationInterface
     */
    private $getChannelConfiguration;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ClientFactory $clientFactory
     * @param GetChannelConfigurationInterface $getChannelConfiguration
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ClientFactory $clientFactory,
        GetChannelConfigurationInterface $getChannelConfiguration
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->clientFactory = $clientFactory;
        $this->getChannelConfiguration = $getChannelConfiguration;
    }

    /**
     * @inheritdoc
     */
    public function execute(ChannelInterface $channel, MessageInterface $message): void
    {
        $messageText = $message->getMessage();
        $configParams = $this->getChannelConfiguration->execute($channel);
        $client = $this->getClient($configParams);

        $client->attach([
            'fallback' => $messageText,
            'text' => $messageText,
            'color' => $configParams[static::PARAM_COLOR] ?: static::DEFAULT_COLOR
        ])->send();
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
