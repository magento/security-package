<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Plugin\Block\Account;

use Magento\Customer\Block\Account\AuthenticationPopup;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaUi\Model\CaptchaUiSettingsProviderInterface;
use Zend\Json\Json;

/**
 * Inject authentication popup in layout
 */
class InjectRecaptchaInAuthenticationPopup
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
     * @param CaptchaUiSettingsProviderInterface $captchaUiSettingsProvider
     * @param CaptchaConfigInterface $captchaConfig
     */
    public function __construct(
        CaptchaUiSettingsProviderInterface $captchaUiSettingsProvider,
        CaptchaConfigInterface $captchaConfig
    ) {
        $this->captchaUiSettingsProvider = $captchaUiSettingsProvider;
        $this->captchaConfig = $captchaConfig;
    }

    /**
     * @param AuthenticationPopup $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsLayout(AuthenticationPopup $subject, $result)
    {
        // TODO: serializer
        $layout = Json::decode($result, Json::TYPE_ARRAY);

        if ($this->captchaConfig->areKeysConfigured()) {
            $layout['components']['authenticationPopup']['children']['recaptcha']['settings']
                = $this->captchaUiSettingsProvider->get();
        } else {
            unset($layout['components']['authenticationPopup']['children']['recaptcha']);
        }
        return Json::encode($layout);
    }
}
