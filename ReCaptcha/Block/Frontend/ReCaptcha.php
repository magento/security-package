<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Block\Frontend;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\ReCaptcha\Model\Config;
use Magento\ReCaptcha\Model\LayoutSettings;
use Zend\Json\Json;

/**
 * @api
 */
class ReCaptcha extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var LayoutSettings
     */
    private $layoutSettings;

    /**
     * @param Template\Context $context
     * @param LayoutSettings $layoutSettings
     * @param array $data
     * @param Config|null $config
     */
    public function __construct(
        Template\Context $context,
        LayoutSettings $layoutSettings,
        array $data = [],
        Config $config = null
    ) {
        parent::__construct($context, $data);
        $this->layoutSettings = $layoutSettings;
        $this->config = $config ?: ObjectManager::getInstance()->get(Config::class);
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
     * Get current recaptcha ID
     */
    public function getRecaptchaId()
    {
        return (string) $this->getData('recaptcha_id') ?: 'recaptcha-' . md5($this->getNameInLayout());
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $layout = Json::decode(parent::getJsLayout(), Json::TYPE_ARRAY);

        if ($this->config->isEnabledFrontend()) {
            // Backward compatibility with fixed scope name
            if (isset($layout['components']['recaptcha'])) {
                $layout['components'][$this->getRecaptchaId()] = $layout['components']['recaptcha'];
                unset($layout['components']['recaptcha']);
            }

            $recaptchaComponentSettings = [];
            if (isset($layout['components'][$this->getRecaptchaId()]['settings'])) {
                $recaptchaComponentSettings = $layout['components'][$this->getRecaptchaId()]['settings'];
            }
            $layout['components'][$this->getRecaptchaId()]['settings'] = array_replace_recursive(
                $this->layoutSettings->getCaptchaSettings(),
                $recaptchaComponentSettings
            );

            $layout['components'][$this->getRecaptchaId()]['reCaptchaId'] = $this->getRecaptchaId();
        }

        return Json::encode($layout);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (!$this->config->isEnabledFrontend()) {
            return '';
        }

        return parent::toHtml();
    }
}
