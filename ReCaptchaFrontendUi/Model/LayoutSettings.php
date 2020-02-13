<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Model;

use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaApi\Api\IsInvisibleCaptchaInterface;

/**
 * Extension point of the layout configuration setting for reCaptcha
 *
 * @api
 */
class LayoutSettings
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
     * Return layout configuration setting
     *
     * @return array
     */
    public function getCaptchaSettings(): array
    {
        $settings = [
            'siteKey' => $this->captchaConfig->getPublicKey(),
            'size' => $this->captchaConfig->getSize(),
            'badge' => $this->captchaConfig->getInvisibleBadgePosition(),
            'theme' => $this->captchaConfig->getTheme(),
            'lang' => $this->captchaConfig->getLanguageCode(),
            'invisible' => $this->isInvisibleCaptcha->isInvisible($this->captchaConfig->getCaptchaType()),
        ];
        return $settings;
    }
}
