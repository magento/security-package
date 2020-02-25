<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Block;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

/**
 * @api
 */
class ReCaptcha extends Template
{
    /**
     * @var UiConfigResolverInterface
     */
    private $captchaUiConfigResolver;

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
     * @param UiConfigResolverInterface $captchaUiConfigResolver
     * @param CaptchaConfigInterface $captchaConfig
     * @param Json $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        UiConfigResolverInterface $captchaUiConfigResolver,
        CaptchaConfigInterface $captchaConfig,
        Json $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        $this->captchaConfig = $captchaConfig;
        $this->serializer = $serializer;
    }

    /**
     * Get reCAPTCHA ID
     */
    public function getRecaptchaId()
    {
        return (string)$this->getData('recaptcha_id') ?: 'recaptcha-' . md5($this->getNameInLayout());
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     * @throws InputException
     */
    public function getJsLayout()
    {
        $layout = $this->serializer->unserialize(parent::getJsLayout());

        if (isset($layout['components']['recaptcha'])) {
            $layout['components'][$this->getRecaptchaId()] = $layout['components']['recaptcha'];
            unset($layout['components']['recaptcha']);
        }

        $layout['components'][$this->getRecaptchaId()] = array_replace_recursive(
            [
                'settings' => $this->getCaptchaUiConfig(),
            ],
            $layout['components'][$this->getRecaptchaId()]
        );
        $layout['components'][$this->getRecaptchaId()]['reCaptchaId'] = $this->getRecaptchaId();

        return $this->serializer->serialize($layout);
    }

    /**
     * Get UI config for reCAPTCHA rendering
     *
     * @return array
     * @throws InputException
     */
    public function getCaptchaUiConfig(): array
    {
        $key = $this->getData('recaptcha_for');
        $uiConfig = $this->getData('captcha_ui_config');

        if ($uiConfig) {
            $uiConfig = array_replace_recursive($this->captchaUiConfigResolver->get($key), $uiConfig);
        } else {
            $uiConfig = $this->captchaUiConfigResolver->get($key);
        }
        return $uiConfig;
    }


    /**
     * @return string
     */
    public function toHtml()
    {
        $key = $this->getData('recaptcha_for');
        if (empty($key) || !$this->captchaConfig->isCaptchaEnabledFor($key)) {
            return '';
        }

        return parent::toHtml();
    }
}
