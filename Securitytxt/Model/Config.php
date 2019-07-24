<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Securitytxt\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const XML_SECURITYTXT_MODULE = 'magento_securitytxt_securitytxt';

    const XML_SECURITYTXT_ENABLED = self::XML_SECURITYTXT_MODULE . '/general/enabled';
    const XML_SECURITYTXT_EMAIL = self::XML_SECURITYTXT_MODULE . '/contact_information/email';
    const XML_SECURITYTXT_PHONE = self::XML_SECURITYTXT_MODULE . '/contact_information/phone';
    const XML_SECURITYTXT_CONTACTPAGE = self::XML_SECURITYTXT_MODULE . '/contact_information/contact_page';

    const XML_SECURITYTXT_ENCRYPTION = self::XML_SECURITYTXT_MODULE . '/other_information/encryption';
    const XML_SECURITYTXT_ACKNOWLEDGEMENTS = self::XML_SECURITYTXT_MODULE . '/other_information/acknowledgements';
    const XML_SECURITYTXT_PREFERREDLANGUAGES = self::XML_SECURITYTXT_MODULE . '/other_information/preferred_languages';
    const XML_SECURITYTXT_HIRING = self::XML_SECURITYTXT_MODULE . '/other_information/hiring';
    const XML_SECURITYTXT_POLICY = self::XML_SECURITYTXT_MODULE . '/other_information/policy';

    const XML_SECURITYTXT_SIGNATURE = self::XML_SECURITYTXT_MODULE . '/other_information/signature_text';


    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return true if module enabled
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(static::XML_SECURITYTXT_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Get email
     * @return string
     */
    public function getEmail(): string
    {
        return $this->scopeConfig->getValue(static::XML_SECURITYTXT_EMAIL, ScopeInterface::SCOPE_WEBSITE) ?: '';
    }

    /**
     * Get telephone
     * @return string
     */
    public function getPhone(): string
    {
        return $this->scopeConfig->getValue(static::XML_SECURITYTXT_PHONE, ScopeInterface::SCOPE_WEBSITE) ?: '';
    }

    /**
     * Get contact page url
     * @return string
     */
    public function getContactPage(): string
    {
        return $this->scopeConfig->getValue(static::XML_SECURITYTXT_CONTACTPAGE, ScopeInterface::SCOPE_WEBSITE) ?: '';
    }

    /**
     * Get encryption url
     * @return string
     */
    public function getEncryption(): string
    {
        return $this->scopeConfig->getValue(static::XML_SECURITYTXT_ENCRYPTION, ScopeInterface::SCOPE_WEBSITE) ?: '';
    }

    /**
     * Get acknowledgements url
     * @return string
     */
    public function getAcknowledgements(): string
    {
        return $this->scopeConfig
            ->getValue(static::XML_SECURITYTXT_ACKNOWLEDGEMENTS, ScopeInterface::SCOPE_WEBSITE) ?: '';
    }

    /**
     * Get preferred languages codes
     * @return string
     */
    public function getPreferredLanguages(): string
    {
        return $this->scopeConfig
            ->getValue(static::XML_SECURITYTXT_PREFERREDLANGUAGES, ScopeInterface::SCOPE_WEBSITE) ?: '';
    }

    /**
     * Get hiring url
     * @return string
     */
    public function getHiring(): string
    {
        return $this->scopeConfig->getValue(static::XML_SECURITYTXT_HIRING, ScopeInterface::SCOPE_WEBSITE) ?: '';
    }

    /**
     * Get policy url
     * @return string
     */
    public function getPolicy(): string
    {
        return $this->scopeConfig->getValue(static::XML_SECURITYTXT_POLICY, ScopeInterface::SCOPE_WEBSITE) ?: '';
    }

    /**
     * Get signature
     * @return string
     */
    public function getSignature(): string
    {
        return $this->scopeConfig->getValue(static::XML_SECURITYTXT_SIGNATURE, ScopeInterface::SCOPE_WEBSITE) ?: '';
    }
}