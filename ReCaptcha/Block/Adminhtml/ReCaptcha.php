<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ReCaptcha\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\ReCaptcha\Model\Config;

class ReCaptcha extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * ReCaptcha constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
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
