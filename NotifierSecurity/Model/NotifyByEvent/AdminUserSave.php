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

class AdminUserSave implements NotifierInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var BuildMessageFromTemplateInterface
     */
    private $buildMessageFromTemplate;

    /**
     * @var string
     */
    private $channelConfigPathNew;

    /**
     * @var string
     */
    private $channelConfigPathExisting;

    /**
     * @var string
     */
    private $templateNew;

    /**
     * @var string
     */
    private $templateExisting;
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
     * @param string $channelConfigPathNew
     * @param string $channelConfigPathExisting
     * @param string $templateNew
     * @param string $templateExisting
     */
    public function __construct(
        BuildMessageFromTemplateInterface $buildMessageFromTemplate,
        ChannelRepository $channelRepository,
        SendMessage $sendMessage,
        ScopeConfigInterface $scopeConfig,
        string $channelConfigPathNew,
        string $channelConfigPathExisting,
        string $templateNew,
        string $templateExisting
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->buildMessageFromTemplate = $buildMessageFromTemplate;
        $this->channelRepository = $channelRepository;
        $this->sendMessage = $sendMessage;
        $this->channelConfigPathNew = $channelConfigPathNew;
        $this->channelConfigPathExisting = $channelConfigPathExisting;
        $this->templateNew = $templateNew;
        $this->templateExisting = $templateExisting;
    }

    /**
     * @param string $eventName
     * @param array $eventData
     * @return void
     */
    public function execute(string $eventName, array $eventData): void
    {
        if ($eventData['object']->isObjectNew()) {
            $channelCode = (string)$this->scopeConfig->getValue($this->channelConfigPathNew);
            $template = $this->templateNew;
        } else {
            $channelCode = (string)$this->scopeConfig->getValue($this->channelConfigPathExisting);
            $template = $this->templateExisting;
        }

        if (!empty($channelCode)) {
            $channel = $this->channelRepository->getByCode($channelCode);
            $message = $this->buildMessageFromTemplate->execute($channelCode, $template, $eventData);

            $this->sendMessage->execute($channel, $message);
        }
    }
}
