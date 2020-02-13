<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptchaUi\Model\CaptchaUiConfigInterface;

/**
 * @inheritdoc
 */
class CaptchaUiConfig implements CaptchaUiConfigInterface
{
    private const XML_PATH_PUBLIC_KEY = 'recaptcha/backend/public_key';
    private const XML_PATH_SIZE = 'recaptcha/backend/size';
    private const XML_PATH_THEME = 'recaptcha/backend/theme';
    private const XML_PATH_POSITION = 'recaptcha/backend/position';
    private const XML_PATH_LANGUAGE_CODE = 'recaptcha/backend/lang';

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
        return trim((string)$this->scopeConfig->getValue(self::XML_PATH_PUBLIC_KEY));
    }

    /**
     * @inheritdoc
     */
    public function getSize(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_SIZE);
    }

    /**
     * @inheritdoc
     */
    public function getTheme(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_THEME);
    }

    /**
     * @inheritdoc
     */
    public function getLanguageCode(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_LANGUAGE_CODE
        );
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_POSITION
        );
    }
}
