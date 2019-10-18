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

class AdminForgotPassword implements NotifierInterface
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
     * @var SendMessageInterface
     */
    private $sendMessage;

    /**
     * @param SendMessageInterface $sendMessage
     * @param ScopeConfigInterface $scopeConfig
     * @param string $channelConfigPath
     * @param string $template
     */
    public function __construct(
        SendMessageInterface $sendMessage,
        ScopeConfigInterface $scopeConfig,
        string $channelConfigPath,
        string $template
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->channelConfigPath = $channelConfigPath;
        $this->template = $template;
        $this->sendMessage = $sendMessage;
    }

    /**
     * @param string $eventName
     * @param array $eventData
     * @return void
     */
    public function execute(string $eventName, array $eventData): void
    {
        $channelCode = (string) $this->scopeConfig->getValue($this->channelConfigPath);
        if (!empty($channelCode) && !empty($eventData['request']->getParam('email'))) {
            $this->sendMessage->execute($channelCode, $this->template, $eventData);
        }
    }
}
