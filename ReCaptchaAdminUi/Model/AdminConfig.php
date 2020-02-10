<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\ReCaptcha\Model\ConfigInterface as ReCaptchaConfig;

/**
 * @inheritdoc
 */
class AdminConfig implements AdminConfigInterface
{
    private const XML_PATH_SIZE_MIN_SCORE = 'recaptcha/backend/min_score';
    private const XML_PATH_SIZE = 'recaptcha/backend/size';
    private const XML_PATH_THEME= 'recaptcha/backend/theme';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ReCaptchaConfig
     */
    private $reCaptchaConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ReCaptchaConfig $reCaptchaConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig, ReCaptchaConfig $reCaptchaConfig)
    {
        $this->scopeConfig = $scopeConfig;
        $this->reCaptchaConfig = $reCaptchaConfig;
    }

    /**
     * @inheritdoc
     */
    public function isBackendEnabled(): bool
    {
        if (!$this->reCaptchaConfig->getPrivateKey() || !$this->reCaptchaConfig->getPublicKey()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(self::XML_PATH_ENABLED);
    }

    /**
     * @inheritdoc
     */
    public function getSize(): string
    {
        if ($this->reCaptchaConfig->isInvisibleRecaptcha()) {
            return 'invisible';
        }

        return (string)$this->scopeConfig->getValue(self::XML_PATH_SIZE);
    }

    /**
     * @inheritdoc
     */
    public function getTheme(): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_THEME);
    }

    /**
     * @inheritdoc
     */
    public function getMinScore(): float
    {
        return min(1.0, max(0.1, (float) $this->scopeConfig->getValue(
            self::XML_PATH_SIZE_MIN_SCORE
        )));
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessage(): Phrase
    {
        if ($this->reCaptchaConfig->getType() === 'recaptcha_v3') {
            return __('You cannot proceed with such operation, your reCaptcha reputation is too low.');
        }

        return __('Incorrect ReCaptcha validation');
    }
}
