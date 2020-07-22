<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Country entity interface
 */
interface CountryInterface extends ExtensibleDataInterface
{
    /**
     * ID field name
     */
    public const ID = 'country_id';

    /**
     * Code field name
     */
    public const CODE = 'code';

    /**
     * Country name field name
     */
    public const NAME = 'name';

    /**
     * Dial code field name
     */
    public const DIAL_CODE = 'dial_code';

    /**
     * Get value for tfa_country_codes_id
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Set value for country_id
     *
     * @param int $value
     */
    public function setId($value): void;

    /**
     * Get value for code
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Set value for code
     *
     * @param string $value
     */
    public function setCode(string $value): void;

    /**
     * Get value for name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set value for name
     *
     * @param string $value
     */
    public function setName(string $value): void;

    /**
     * Get value for dial_code
     *
     * @return string
     */
    public function getDialCode(): string;

    /**
     * Set value for dial_code
     *
     * @param string $value
     */
    public function setDialCode(string $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\CountryExtensionInterface|null
     */
    public function getExtensionAttributes(): ?CountryExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\CountryExtensionInterface $extensionAttributes
     */
    public function setExtensionAttributes(
        CountryExtensionInterface $extensionAttributes
    ): void;
}
