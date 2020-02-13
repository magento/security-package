<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Block;

use Magento\Framework\View\Element\Template;
use Magento\ReCaptchaUi\Model\CaptchaUiConfigInterface;

/**
 * @api
 */
class ReCaptcha extends Template
{
    /**
     * @var CaptchaUiConfigInterface
     */
    private $captchaUiConfig;

    /**
     * @param Template\Context $context
     * @param CaptchaUiConfigInterface $captchaUiConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CaptchaUiConfigInterface $captchaUiConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->captchaUiConfig = $captchaUiConfig;
    }

    /**
     * Get public reCaptcha key
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->captchaUiConfig->getPublicKey();
    }

    /**
     * Get backend theme
     * @return string
     */
    public function getTheme(): string
    {
        return $this->captchaUiConfig->getTheme();
    }

    /**
     * Get backend size
     * @return string
     */
    public function getSize(): string
    {
        return $this->captchaUiConfig->getSize();
    }

    /**
     * Get position
     * @return string|null
     */
    public function getPosition(): string
    {
        return $this->captchaUiConfig->getPosition();
    }

    /**
     * Get language code
     * @return string|null
     */
    public function getLanguageCode(): string
    {
        return $this->captchaUiConfig->getLanguageCode();
    }

    /**
     * @return string
     */
    public function toHtml(): string
    {
        if ('' === $this->captchaUiConfig->getPublicKey()) {
            return '';
        }
        return parent::toHtml();
    }
}
