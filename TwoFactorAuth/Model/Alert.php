<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Event\ManagerInterface;

/**
 * @inheritDoc
 */
class Alert implements AlertInterface
{
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        ManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * Trigger a security suite event
     *
     * @param string $module
     * @param string $message
     * @param string $level
     * @param string $username
     * @param array|string $payload
     * @return void
     */
    public function event(
        string $module,
        string $message,
        ?string $level = null,
        ?string $username = null,
        $payload = null
    ): void {
        if ($level === null) {
            $level = self::LEVEL_INFO;
        }

        $params = [
            AlertInterface::ALERT_PARAM_LEVEL => $level,
            AlertInterface::ALERT_PARAM_MODULE => $module,
            AlertInterface::ALERT_PARAM_MESSAGE => $message,
            AlertInterface::ALERT_PARAM_USERNAME => $username,
            AlertInterface::ALERT_PARAM_PAYLOAD => $payload,
        ];

        $genericEvent = AlertInterface::EVENT_PREFIX . '_event';
        $moduleEvent = AlertInterface::EVENT_PREFIX . '_event_' . strtolower($module);
        $severityEvent = AlertInterface::EVENT_PREFIX . '_level_' . strtolower($level);

        $this->eventManager->dispatch($genericEvent, $params);
        $this->eventManager->dispatch($moduleEvent, $params);
        $this->eventManager->dispatch($severityEvent, $params);
    }
}
