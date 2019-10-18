<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\NotifierApi\Api\Data\ChannelExtensionInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Channel extends AbstractExtensibleModel implements ChannelInterface
{
    /**
     * Field channel ID
     */
    private const ID = 'channel_id';

    /**
     * Field name
     */
    private const NAME = 'name';

    /**
     * Field adapter code
     */
    private const ADAPTER_CODE = 'adapter_code';

    /**
     * Field code
     */
    private const CODE = 'code';

    /**
     * Field enabled
     */
    private const ENABLED = 'enabled';

    /**
     * Configuration json field
     */
    private const CONFIGURATION_JSON = 'configuration_json';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Channel::class);
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
    public function getCode(): string
    {
        return (string) $this->getData(self::CODE);
    }

    /**
     * @inheritdoc
     */
    public function setCode(string $value): void
    {
        $this->setData(self::CODE, $value);
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
    public function getAdapterCode(): string
    {
        return (string) $this->getData(self::ADAPTER_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setAdapterCode(string $value): void
    {
        $this->setData(self::ADAPTER_CODE, $value);
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
    public function getConfigurationJson(): string
    {
        return $this->getData(self::CONFIGURATION_JSON);
    }

    /**
     * @inheritdoc
     */
    public function setConfigurationJson(string $value): void
    {
        $this->setData(self::CONFIGURATION_JSON, $value);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?\Magento\NotifierApi\Api\Data\ChannelExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        ChannelExtensionInterface $extensionAttributes
    ): void {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
