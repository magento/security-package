<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\ScopeInterface;

/**
 * @inheritdoc
 */
class Config implements ConfigInterface
{
    private const XML_PATH_TYPE = 'recaptcha/general/type';
    private const XML_PATH_PUBLIC_KEY = 'recaptcha/general/public_key';
    private const XML_PATH_PRIVATE_KEY = 'recaptcha/general/private_key';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function getPublicKey(): string
    {
        return trim((string)$this->scopeConfig->getValue(static::XML_PATH_PUBLIC_KEY, ScopeInterface::SCOPE_WEBSITE));
    }

    /**
     * @inheritdoc
     */
    public function getPrivateKey(): string
    {
        return trim((string)$this->scopeConfig->getValue(static::XML_PATH_PRIVATE_KEY, ScopeInterface::SCOPE_WEBSITE));
    }

    /**
     * @inheritdoc
     */
    public function isInvisibleRecaptcha(): bool
    {
        return in_array($this->getType(), ['invisible', 'recaptcha_v3'], true);
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return (string)$this->scopeConfig->getValue(static::XML_PATH_TYPE);
    }
}
