<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @inheritdoc
 */
class CaptchaConfig implements CaptchaConfigInterface
{
    private const XML_PATH_PRIVATE_KEY = 'recaptcha/frontend/private_key';
    private const XML_PATH_SCORE_THRESHOLD = 'recaptcha/frontend/score_threshold';
    private const XML_PATH_TYPE_FOR = 'recaptcha_frontend/type_for/';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $captchaErrorMessages;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param array $captchaErrorMessages
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        array $captchaErrorMessages = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->captchaErrorMessages = $captchaErrorMessages;
    }

    /**
     * @inheritdoc
     */
    public function getPrivateKey(): string
    {
        return trim((string)$this->scopeConfig->getValue(self::XML_PATH_PRIVATE_KEY, ScopeInterface::SCOPE_WEBSITE));
    }

    /**
     * @inheritdoc
     */
    public function getCaptchaType(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function getScoreThreshold(): float
    {
        return min(1.0, max(0.1, (float)$this->scopeConfig->getValue(
            self::XML_PATH_SCORE_THRESHOLD,
            ScopeInterface::SCOPE_WEBSITE
        )));
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessage(): Phrase
    {
        foreach ($this->captchaErrorMessages as $captchaErrorMessage) {
            if ($this->getCaptchaType() === $captchaErrorMessage['type']) {
                return __($captchaErrorMessage['message']);
            }
        }

        return __('Incorrect reCAPTCHA validation');
    }

    /**
     * @inheritdoc
     */
    public function isCaptchaEnabledFor(string $key): bool
    {
        return /*$this->areKeysConfigured() &&*/ (null !== $this->getCaptchaTypeFor($key));
    }

    /**
     * @inheritdoc
     */
    public function getCaptchaTypeFor(string $key): ?string
    {
        $type = $this->scopeConfig->getValue(
            self::XML_PATH_TYPE_FOR . $key,
            ScopeInterface::SCOPE_WEBSITE
        );
        return $type;
    }

    /**
     * Return true if reCAPTCHA keys (public and private) are configured
     *
     * @return bool
     */
    private function areKeysConfigured(): bool
    {
        return $this->getPrivateKey() && $this->getPublicKey();
    }
}
