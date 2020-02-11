<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Model;

use Magento\Framework\Exception\LocalizedException;
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
     * @var string[]
     */
    private $configFlags;

    /**
     * @param CaptchaConfigInterface $captchaConfig
     * @param string[] $configFlags
     */
    public function __construct(
        CaptchaConfigInterface $captchaConfig,
        array $configFlags
    ) {
        $this->captchaConfig = $captchaConfig;
        $this->configFlags = $configFlags;
    }

    /**
     * Return layout configuration setting
     *
     * @return array
     * @throws LocalizedException
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
        foreach ($this->configFlags as $configFlag) {
            $settings['enabled'][$configFlag] = $this->captchaConfig->isCaptchaEnabledFor($configFlag);
        }
        return $settings;
    }
}
