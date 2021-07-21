<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Authy device data interface
 *
 * @api
 */
interface AuthyDeviceInterface extends ExtensibleDataInterface
{
    /**
     * Country field
     */
    public const COUNTRY = 'country';

    /**
     * Phone number field
     */
    public const PHONE = 'phone_number';

    /**
     * Method of authentication field
     */
    public const METHOD = 'method';

    /**
     * Authenticate via sms
     */
    public const METHOD_SMS = 'sms';

    /**
     * Authenticate via phone call
     */
    public const METHOD_CALL = 'call';

    /**
     * Get the country
     *
     * @return string
     */
    public function getCountry(): string;

    /**
     * Set the country
     *
     * @param string $value
     * @return void
     */
    public function setCountry(string $value): void;

    /**
     * Get the phone number
     *
     * @return string
     */
    public function getPhoneNumber(): string;

    /**
     * Set the phone number
     *
     * @param string $value
     * @return void
     */
    public function setPhoneNumber(string $value): void;

    /**
     * Get the method to authenticate with
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Set the method to authenticate with
     *
     * @param string $value
     * @return void
     */
    public function setMethod(string $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\AuthyDeviceExtensionInterface|null
     */
    public function getExtensionAttributes(): ?AuthyDeviceExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\AuthyDeviceExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        AuthyDeviceExtensionInterface $extensionAttributes
    ): void;
}
