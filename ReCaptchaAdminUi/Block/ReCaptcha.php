<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Block;

use Magento\Framework\View\Element\Template;
use Magento\ReCaptcha\Model\CaptchaConfigInterface;

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
     * @param Template\Context $context
     * @param CaptchaConfigInterface $captchaConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CaptchaConfigInterface $captchaConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->captchaConfig = $captchaConfig;
    }

    /**
     * Get public reCaptcha key
     * @return string
     */
    public function getPublicKey()
    {
        return $this->captchaConfig->getPublicKey();
    }

    /**
     * Get backend theme
     * @return string
     */
    public function getTheme()
    {
        return $this->captchaConfig->getTheme();
    }

    /**
     * Get backend size
     * @return string
     */
    public function getSize()
    {
        return $this->captchaConfig->getSize();
    }

    /**
     * Return true if can display reCaptcha
     * @return bool
     */
    public function canDisplayCaptcha()
    {
        // TODO:
        return true;
    }
}
