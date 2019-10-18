<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Channel Data Interface
 * @api
 */
interface ChannelInterface extends ExtensibleDataInterface
{
    /**
     * Get value for channel_id
     * @return int
     */
    public function getId();

    /**
     * Set value for channel_id
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
     * Get value for code
     * @return string
     */
    public function getCode(): string;

    /**
     * Set value for code
     * @param string $value
     * @return void
     */
    public function setCode(string $value): void;

    /**
     * Get value for adapter_code
     * @return string
     */
    public function getAdapterCode(): string;

    /**
     * Set value for adapter_code
     * @param string $value
     * @return void
     */
    public function setAdapterCode(string $value): void;

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
     * Get value for configuration_json
     * @return string
     */
    public function getConfigurationJson(): string;

    /**
     * Set value for configuration_json
     * @param string $value
     * @return void
     */
    public function setConfigurationJson(string $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     * @return \Magento\NotifierApi\Api\Data\ChannelExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Magento\NotifierApi\Api\Data\ChannelExtensionInterface;

    /**
     * Set an extension attributes object
     * @param \Magento\NotifierApi\Api\Data\ChannelExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Magento\NotifierApi\Api\Data\ChannelExtensionInterface $extensionAttributes
    ): void;
}
