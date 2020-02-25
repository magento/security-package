<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Plugin\Block\Account;

use Magento\Customer\Block\Account\AuthenticationPopup;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaUi\Model\CaptchaUiSettingsProviderInterface;

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
     * @var Json
     */
    private $serializer;

    /**
     * @param CaptchaUiSettingsProviderInterface $captchaUiSettingsProvider
     * @param CaptchaConfigInterface $captchaConfig
     * @param Json $serializer
     */
    public function __construct(
        CaptchaUiSettingsProviderInterface $captchaUiSettingsProvider,
        CaptchaConfigInterface $captchaConfig,
        Json $serializer
    ) {
        $this->captchaUiSettingsProvider = $captchaUiSettingsProvider;
        $this->captchaConfig = $captchaConfig;
        $this->serializer = $serializer;
    }

    /**
     * @param AuthenticationPopup $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsLayout(AuthenticationPopup $subject, $result)
    {
        $layout = $this->serializer->unserialize($result);

        if ($this->captchaConfig->isCaptchaEnabledFor('customer_login')) {
            $layout['components']['authenticationPopup']['children']['recaptcha']['settings']
                = $this->captchaUiSettingsProvider->get();
        } else {
            unset($layout['components']['authenticationPopup']['children']['recaptcha']);
        }
        return $this->serializer->serialize($layout);
    }
}
