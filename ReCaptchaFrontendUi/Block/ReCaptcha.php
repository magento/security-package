<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Block;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaUi\Model\CaptchaUiSettingsProviderInterface;

/**
 * @api
 */
class ReCaptcha extends Template
{
    /**
     * @var CaptchaUiSettingsProviderInterface
     */
    private $captchaUiSettingsProvider;

    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param Template\Context $context
     * @param CaptchaUiSettingsProviderInterface $captchaUiSettingsProvider
     * @param CaptchaConfigInterface $captchaConfig
     * @param Json $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CaptchaUiSettingsProviderInterface $captchaUiSettingsProvider,
        CaptchaConfigInterface $captchaConfig,
        Json $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->captchaUiSettingsProvider = $captchaUiSettingsProvider;
        $this->captchaConfig = $captchaConfig;
        $this->serializer = $serializer;
    }

    /**
     * Get current recaptcha ID
     */
    public function getRecaptchaId()
    {
        return (string)$this->getData('recaptcha_id') ?: 'recaptcha-' . md5($this->getNameInLayout());
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $layout = $this->serializer->unserialize(parent::getJsLayout());

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
            $this->captchaUiSettingsProvider->get(),
            $recaptchaComponentSettings
        );

        $layout['components'][$this->getRecaptchaId()]['reCaptchaId'] = $this->getRecaptchaId();

        return $this->serializer->serialize($layout);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $key = $this->jsLayout['components']['recaptcha']['zone'];

        if (!$this->captchaConfig->isCaptchaEnabledFor($key)) {
            return '';
        }
        return parent::toHtml();
    }
}
