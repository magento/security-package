<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Trusted platform entity interface
 */
interface TrustedInterface extends ExtensibleDataInterface
{
    /**
     * Entity ID field name
     */
    public const ID = 'trusted_id';

    /**
     * Date and time field name
     */
    public const DATE_TIME = 'date_time';

    /**
     * Use ID field name
     */
    public const USER_ID = 'user_id';

    /**
     * Device description field name
     */
    public const DEVICE_NAME = 'device_name';

    /**
     * Token field name
     */
    public const TOKEN = 'token';

    /**
     * Last IP field name
     */
    public const LAST_IP = 'last_ip';

    /**
     * User agent field name
     */
    public const USER_AGENT = 'user_agent';

    /**
     * Get value for tfa_trusted_id
     * @return int
     */
    public function getId(): int;

    /**
     * Set value for tfa_trusted_id
     * @param int $value
     * @return void
     */
    public function setId(int $value): void;

    /**
     * Get value for date_time
     * @return string
     */
    public function getDateTime(): string;

    /**
     * Set value for date_time
     * @param string $value
     * @return void
     */
    public function setDateTime(string $value): void;

    /**
     * Get value for user_id
     * @return int
     */
    public function getUserId(): int;

    /**
     * Set value for user_id
     * @param int $value
     * @return void
     */
    public function setUserId(int $value): void;

    /**
     * Get value for device_name
     * @return string
     */
    public function getDeviceName(): string;

    /**
     * Set value for device_name
     * @param string $value
     * @return void
     */
    public function setDeviceName(string $value): void;

    /**
     * Get value for last_ip
     * @return string
     */
    public function getLastIp(): string;

    /**
     * Set value for last_ip
     * @param string $value
     * @return void
     */
    public function setLastIp(string $value): void;

    /**
     * Get value for user_agent
     * @return string
     */
    public function getUserAgent(): string;

    /**
     * Set value for user_agent
     * @param string $value
     * @return void
     */
    public function setUserAgent(string $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     * @return TrustedExtensionInterface|null
     */
    public function getExtensionAttributes(): ?TrustedExtensionInterface;

    /**
     * Set an extension attributes object
     * @param TrustedExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(TrustedExtensionInterface $extensionAttributes): void;
}
