<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;

/**
 * @inheritdoc
 */
class CaptchaConfig implements CaptchaConfigInterface
{
    private const XML_PATH_TYPE = 'recaptcha/backend/type';
    private const XML_PATH_PUBLIC_KEY = 'recaptcha/backend/public_key';
    private const XML_PATH_PRIVATE_KEY = 'recaptcha/backend/private_key';

    private const XML_PATH_SCORE_THRESHOLD = 'recaptcha/backend/score_threshold';
    private const XML_PATH_SIZE = 'recaptcha/backend/size';
    private const XML_PATH_THEME = 'recaptcha/backend/theme';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $invisibleTypes;

    /**
     * @var array
     */
    private $captchaErrorMessages;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param array $invisibleTypes
     * @param array $captchaErrorMessages
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        array $invisibleTypes = [],
        array $captchaErrorMessages = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->invisibleTypes = $invisibleTypes;
        $this->captchaErrorMessages = $captchaErrorMessages;
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
    public function getPrivateKey(): string
    {
        return trim((string)$this->scopeConfig->getValue(self::XML_PATH_PRIVATE_KEY));
    }

    /**
     * @inheritdoc
     */
    public function areKeysConfigured(): bool
    {
        return $this->getPrivateKey() && $this->getPublicKey();
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
    public function getSize(): string
    {
        if ($this->isInvisibleRecaptcha()) {
            return 'invisible';
        }

        return (string)$this->scopeConfig->getValue(self::XML_PATH_SIZE);
    }

    /**
     * @inheritdoc
     */
    public function getTheme(): ?string
    {
        if ($this->isInvisibleRecaptcha()) {
            return null;
        }

        return (string)$this->scopeConfig->getValue(self::XML_PATH_THEME);
    }

    /**
     * @inheritdoc
     */
    public function getScoreThreshold(): float
    {
        return min(1.0, max(0.1, (float)$this->scopeConfig->getValue(
            self::XML_PATH_SCORE_THRESHOLD
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

        return __('Incorrect ReCaptcha validation');
    }

    /**
     * @return bool
     */
    public function isInvisibleRecaptcha(): bool
    {
        return in_array($this->getCaptchaType(), $this->invisibleTypes, true);
    }

    /**
     * @inheritdoc
     */
    public function getLanguageCode(): string
    {
        throw new \RuntimeException('Support not implemented');
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): ?string
    {
        throw new \RuntimeException('Support not implemented');
    }
}
