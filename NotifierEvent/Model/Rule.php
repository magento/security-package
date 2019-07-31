<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEvent\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use MSP\NotifierEventApi\Api\Data\RuleExtensionInterface;
use MSP\NotifierEventApi\Api\Data\RuleInterface;

class Rule extends AbstractExtensibleModel implements RuleInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $value): void
    {
        $this->setData(self::NAME, $value);
    }

    /**
     * @inheritdoc
     */
    public function getEvents(): string
    {
        return (string) $this->getData(self::EVENTS);
    }

    /**
     * @inheritdoc
     */
    public function setEvents(string $value): void
    {
        $this->setData(self::EVENTS, $value);
    }

    /**
     * @inheritdoc
     */
    public function getChannelsCodes(): string
    {
        return (string) $this->getData(self::CHANNELS_CODES);
    }

    /**
     * @inheritdoc
     */
    public function setChannelsCodes(string $value): void
    {
        $this->setData(self::CHANNELS_CODES, $value);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateId(): string
    {
        return (string) $this->getData(self::TEMPLATE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTemplateId(string $value): void
    {
        $this->setData(self::TEMPLATE_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getThrottleLimit(): int
    {
        return (int) $this->getData(self::THROTTLE_LIMIT);
    }

    /**
     * @inheritdoc
     */
    public function setThrottleLimit(int $value): void
    {
        $this->setData(self::THROTTLE_LIMIT, $value);
    }

    /**
     * @inheritdoc
     */
    public function getThrottleInterval(): int
    {
        return (int) $this->getData(self::THROTTLE_INTERVAL);
    }

    /**
     * @inheritdoc
     */
    public function setThrottleInterval(int $value): void
    {
        $this->setData(self::THROTTLE_INTERVAL, $value);
    }

    /**
     * @inheritdoc
     */
    public function getLastFiredAt(): int
    {
        return (int) $this->getData(self::LAST_FIRED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setLastFiredAt(int $value): void
    {
        $this->setData(self::LAST_FIRED_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function getFireCount(): int
    {
        return (int) $this->getData(self::FIRE_COUNT);
    }

    /**
     * @inheritdoc
     */
    public function setFireCount(int $value): void
    {
        $this->setData(self::FIRE_COUNT, $value);
    }

    /**
     * @inheritdoc
     */
    public function getEnabled(): bool
    {
        return (bool) $this->getData(self::ENABLED);
    }

    /**
     * @inheritdoc
     */
    public function setEnabled(bool $value): void
    {
        $this->setData(self::ENABLED, $value);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?RuleExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        RuleExtensionInterface $extensionAttributes
    ): void {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
