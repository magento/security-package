<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Block;

use Magento\Framework\View\Element\Template;
use Magento\ReCaptchaAdminUi\Model\AdminConfigInterface;

/**
 * @api
 */
class ReCaptcha extends Template
{
    /**
     * @var AdminConfigInterface
     */
    private $reCaptchaAdminConfig;

    /**
     * @param Template\Context $context
     * @param AdminConfigInterface $reCaptchaAdminConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AdminConfigInterface $reCaptchaAdminConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->reCaptchaAdminConfig = $reCaptchaAdminConfig;
    }

    /**
     * Get public reCaptcha key
     * @return string
     */
    public function getPublicKey()
    {
        return $this->reCaptchaAdminConfig->getPublicKey();
    }

    /**
     * Get backend theme
     * @return string
     */
    public function getTheme()
    {
        return $this->reCaptchaAdminConfig->getTheme();
    }

    /**
     * Get backend size
     * @return string
     */
    public function getSize()
    {
        return $this->reCaptchaAdminConfig->getSize();
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
