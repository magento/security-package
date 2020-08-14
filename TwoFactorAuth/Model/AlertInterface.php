<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

/**
 * Alert interface triggered when a security event occurs in 2FA module
 */
interface AlertInterface
{
    /**
     * Event prefix for alert
     */
    public const EVENT_PREFIX = 'twofactor';

    /**
     * Alert level "info"
     */
    public const LEVEL_INFO = 'info';

    /**
     * Alert level "warning"
     */
    public const LEVEL_WARNING = 'warn';

    /**
     * Alert level "error"
     */
    public const LEVEL_ERROR = 'error';

    /**
     * Parameter name for "level"
     */
    public const ALERT_PARAM_LEVEL = 'level';

    /**
     * Parameter name for "module"
     */
    public const ALERT_PARAM_MODULE = 'module';

    /**
     * Parameter name for "message"
     */
    public const ALERT_PARAM_MESSAGE = 'message';

    /**
     * Parameter name for "username"
     */
    public const ALERT_PARAM_USERNAME = 'username';

    /**
     * Parameter name for "payload"
     */
    public const ALERT_PARAM_PAYLOAD = 'payload';

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
    ): void;
}
