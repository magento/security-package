<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Block;

use Magento\Framework\View\Element\Template;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaUi\Model\CaptchaUiSettingsProviderInterface;

/**
 * @api
 */
class ReCaptcha extends Template
{
    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @var CaptchaUiSettingsProviderInterface
     */
    private $captchaUiSettingsProvider;

    /**
     * @param Template\Context $context
     * @param CaptchaConfigInterface $captchaConfig
     * @param CaptchaUiSettingsProviderInterface $captchaUiSettingsProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CaptchaConfigInterface $captchaConfig,
        CaptchaUiSettingsProviderInterface $captchaUiSettingsProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->captchaConfig = $captchaConfig;
        $this->captchaUiSettingsProvider = $captchaUiSettingsProvider;
    }

    /**
     * @return string|null
     */
    public function getLanguageCode(): ?string
    {
        $settings = $this->captchaUiSettingsProvider->get();
        return $settings['lang'] ?? null;
    }

    /**
     * @return array
     */
    public function getRenderSettings(): array
    {
        $settings = $this->captchaUiSettingsProvider->get();
        return $settings['render'] ?? [];
    }

    /**
     * @return bool
     */
    public function isInvisibleCaptchaType(): bool
    {
        $settings = $this->captchaUiSettingsProvider->get();
        return !empty($settings['invisible']);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $enabledFor = $this->getData('enabled_for');
        if (empty($enabledFor) || !$this->captchaConfig->isCaptchaEnabledFor($enabledFor)) {
            return '';
        }

        return parent::toHtml();
    }
}
