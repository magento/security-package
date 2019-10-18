<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierSecurity\Model\NotifyByEvent;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\NotifierSecurity\Model\NotifierInterface;
use Magento\NotifierTemplateApi\Api\SendMessageInterface;

class AdminUserSave implements NotifierInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SendMessageInterface
     */
    private $sendMessage;

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
     * @param SendMessageInterface $sendMessage
     * @param ScopeConfigInterface $scopeConfig
     * @param string $channelConfigPathNew
     * @param string $channelConfigPathExisting
     * @param string $templateNew
     * @param string $templateExisting
     */
    public function __construct(
        SendMessageInterface $sendMessage,
        ScopeConfigInterface $scopeConfig,
        string $channelConfigPathNew,
        string $channelConfigPathExisting,
        string $templateNew,
        string $templateExisting
    ) {
        $this->scopeConfig = $scopeConfig;
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
            $channelCode = (string) $this->scopeConfig->getValue($this->channelConfigPathNew);
            $template = $this->templateNew;
        } else {
            $channelCode = (string) $this->scopeConfig->getValue($this->channelConfigPathExisting);
            $template = $this->templateExisting;
        }

        if (!empty($channelCode)) {
            $this->sendMessage->execute($channelCode, $template, $eventData);
        }
    }
}
