<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Block;

use Magento\Framework\View\Element\Template;
use Magento\ReCaptcha\Model\ConfigInterface;

/**
 * @api
 */
class ReCaptcha extends Template
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param Template\Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ConfigInterface $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * Get public reCaptcha key
     * @return string
     */
    public function getPublicKey()
    {
        return $this->config->getPublicKey();
    }

    /**
     * Get backend theme
     * @return string
     */
    public function getTheme()
    {
        return $this->config->getBackendTheme();
    }

    /**
     * Get backend size
     * @return string
     */
    public function getSize()
    {
        return $this->config->getBackendSize();
    }

    /**
     * Return true if can display reCaptcha
     * @return bool
     */
    public function canDisplayCaptcha()
    {
        return $this->config->isEnabledBackend();
    }
}
