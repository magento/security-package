<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Block;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\ReCaptcha\Model\ConfigInterface;
use Magento\ReCaptchaFrontendUi\Model\FrontendConfigInterface as ReCaptchaFrontendUiConfig;
use Magento\ReCaptchaFrontendUi\Model\LayoutSettings;

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
     * @var LayoutSettings
     */
    private $layoutSettings;

    /**
     * @var ReCaptchaFrontendUiConfig
     */
    private $reCaptchaFrontendConfig;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param Template\Context $context
     * @param LayoutSettings $layoutSettings
     * @param ConfigInterface $config
     * @param ReCaptchaFrontendUiConfig $reCaptchaFrontendConfig
     * @param Json $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        LayoutSettings $layoutSettings,
        ConfigInterface $config,
        ReCaptchaFrontendUiConfig $reCaptchaFrontendConfig,
        Json $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layoutSettings = $layoutSettings;
        $this->config = $config;
        $this->reCaptchaFrontendConfig = $reCaptchaFrontendConfig;
        $this->serializer = $serializer;
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
        $layout = $this->serializer->unserialize(parent::getJsLayout());

        if ($this->reCaptchaFrontendConfig->isFrontendEnabled()) {
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

        return $this->serializer->serialize($layout);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (!$this->reCaptchaFrontendConfig->isFrontendEnabled()) {
            return '';
        }

        return parent::toHtml();
    }
}
