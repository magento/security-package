<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface RuleInterface extends ExtensibleDataInterface
{
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
