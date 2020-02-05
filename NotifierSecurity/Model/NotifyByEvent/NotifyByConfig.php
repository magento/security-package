<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierSecurity\Model\NotifyByEvent;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Notifier\Model\ChannelRepository;
use Magento\Notifier\Model\SendMessage;
use Magento\NotifierSecurity\Model\NotifierInterface;
use Magento\NotifierTemplateApi\Model\BuildMessageFromTemplateInterface;

class NotifyByConfig implements NotifierInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    private $channelConfigPath;

    /**
     * @var string
     */
    private $template;

    /**
     * @var BuildMessageFromTemplateInterface
     */
    private $buildMessageFromTemplate;
    /**
     * @var ChannelRepository
     */
    private $channelRepository;
    /**
     * @var SendMessage
     */
    private $sendMessage;

    /**
     * @param BuildMessageFromTemplateInterface $buildMessageFromTemplate
     * @param ChannelRepository $channelRepository
     * @param SendMessage $sendMessage
     * @param ScopeConfigInterface $scopeConfig
     * @param string $channelConfigPath
     * @param string $template
     */
    public function __construct(
        BuildMessageFromTemplateInterface $buildMessageFromTemplate,
        ChannelRepository $channelRepository,
        SendMessage $sendMessage,
        ScopeConfigInterface $scopeConfig,
        string $channelConfigPath,
        string $template
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->channelRepository = $channelRepository;
        $this->sendMessage = $sendMessage;
        $this->channelConfigPath = $channelConfigPath;
        $this->template = $template;
        $this->buildMessageFromTemplate = $buildMessageFromTemplate;
    }

    /**
     * @param string $eventName
     * @param array $eventData
     * @return void
     */
    public function execute(string $eventName, array $eventData): void
    {
        $channelCode = (string) $this->scopeConfig->getValue($this->channelConfigPath);
        if (!empty($channelCode)) {
            $channel = $this->channelRepository->getByCode($channelCode);
            $message = $this->buildMessageFromTemplate->execute($channelCode, $this->template, $eventData);

            $this->sendMessage->execute($channel, $message);
        }
    }
}
