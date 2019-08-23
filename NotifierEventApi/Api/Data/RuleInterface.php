<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface RuleInterface extends ExtensibleDataInterface
{
    /**
     * ID field name
     */
    public const ID = 'rule_id';

    /**
     * Field name
     */
    public const NAME = 'name';

    /**
     * Events field name
     */
    public const EVENTS = 'events';

    /**
     * Channel codes field name
     */
    public const CHANNELS_CODES = 'channels_codes';

    /**
     * Template field name
     */
    public const TEMPLATE_ID = 'template_id';

    /**
     * Throttle limit field name
     */
    public const THROTTLE_LIMIT = 'throttle_limit';

    /**
     * Throttle interval field name
     */
    public const THROTTLE_INTERVAL = 'throttle_interval';

    /**
     * Last fired at field name
     */
    public const LAST_FIRED_AT = 'last_fired_at';

    /**
     * Fire count field name
     */
    public const FIRE_COUNT = 'fire_count';

    /**
     * Enabled field name
     */
    public const ENABLED = 'enabled';

    /**
     * Get value for rule_id
     * @return int
     */
    public function getId();

    /**
     * Set value for rule_id
     * @param int $value
     * @return void
     */
    public function setId($value);

    /**
     * Get value for name
     * @return string
     */
    public function getName(): string;

    /**
     * Set value for name
     * @param string $value
     * @return void
     */
    public function setName(string $value): void;

    /**
     * Get value for events
     * @return string
     */
    public function getEvents(): string;

    /**
     * Set value for events
     * @param string $value
     * @return void
     */
    public function setEvents(string $value): void;

    /**
     * Get value for channels_codes
     * @return string
     */
    public function getChannelsCodes(): string;

    /**
     * Set value for channels_codes
     * @param string $value
     * @return void
     */
    public function setChannelsCodes(string $value): void;

    /**
     * Get value for template_id
     * @return string
     */
    public function getTemplateId(): string;

    /**
     * Set value for template_id
     * @param string $value
     * @return void
     */
    public function setTemplateId(string $value): void;

    /**
     * Get value for throttle_limit
     * @return int
     */
    public function getThrottleLimit(): int;

    /**
     * Set value for template_id
     * @param int $value
     * @return void
     */
    public function setThrottleLimit(int $value): void;

    /**
     * Get value for throttle_interval
     * @return int
     */
    public function getThrottleInterval(): int;

    /**
     * Set value for throttle_interval
     * @param int $value
     * @return void
     */
    public function setThrottleInterval(int $value): void;

    /**
     * Get value for last_fired_at
     * @return int
     */
    public function getLastFiredAt(): int;

    /**
     * Set value for last_fired_at
     * @param int $value
     * @return void
     */
    public function setLastFiredAt(int $value): void;

    /**
     * Get value for fire_count
     * @return int
     */
    public function getFireCount(): int;

    /**
     * Set value for fire_count
     * @param int $value
     * @return void
     */
    public function setFireCount(int $value): void;

    /**
     * Get value for enabled
     * @return bool
     */
    public function getEnabled(): bool;

    /**
     * Set value for enabled
     * @param bool $value
     * @return void
     */
    public function setEnabled(bool $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     * @return \Magento\NotifierEventApi\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Magento\NotifierEventApi\Api\Data\RuleExtensionInterface;

    /**
     * Set an extension attributes object
     * @param \Magento\NotifierEventApi\Api\Data\RuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magento\NotifierEventApi\Api\Data\RuleExtensionInterface $extensionAttributes
    );
}
