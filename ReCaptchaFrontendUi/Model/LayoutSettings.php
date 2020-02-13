<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Model;

use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;

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
     * @param CaptchaConfigInterface $captchaConfig
     */
    public function __construct(
        CaptchaConfigInterface $captchaConfig
    ) {
        $this->captchaConfig = $captchaConfig;
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
            'badge' => $this->captchaConfig->getPosition(),
            'theme' => $this->captchaConfig->getTheme(),
            'lang' => $this->captchaConfig->getLanguageCode(),
        ];
        return $settings;
    }
}
