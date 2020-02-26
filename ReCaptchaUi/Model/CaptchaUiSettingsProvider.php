<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaApi\Api\IsInvisibleCaptchaInterface;

/**
 * @inheritdoc
 */
class CaptchaUiSettingsProvider implements CaptchaUiSettingsProviderInterface
{
    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @var IsInvisibleCaptchaInterface
     */
    private $isInvisibleCaptcha;

    /**
     * @param CaptchaConfigInterface $captchaConfig
     * @param IsInvisibleCaptchaInterface $isInvisibleCaptcha
     */
    public function __construct(
        CaptchaConfigInterface $captchaConfig,
        IsInvisibleCaptchaInterface $isInvisibleCaptcha
    ) {
        $this->captchaConfig = $captchaConfig;
        $this->isInvisibleCaptcha = $isInvisibleCaptcha;
    }

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        $settings = [
            'render' => [
                'sitekey' => $this->captchaConfig->getPublicKey(),
                'theme' => $this->captchaConfig->getTheme(),
                'size' => $this->captchaConfig->getSize(),
                'badge' => $this->captchaConfig->getInvisibleBadgePosition(),
            ],
            'lang' => $this->captchaConfig->getLanguageCode(),
            'invisible' => $this->isInvisibleCaptcha->isInvisible($this->captchaConfig->getCaptchaType()),
        ];
        return $settings;
    }
}
