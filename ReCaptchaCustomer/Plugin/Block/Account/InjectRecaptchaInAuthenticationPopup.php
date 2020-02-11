<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Plugin\Block\Account;

use Magento\Customer\Block\Account\AuthenticationPopup;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaFrontendUi\Model\LayoutSettings;
use Zend\Json\Json;

/**
 * Inject authentication popup in layout
 */
class InjectRecaptchaInAuthenticationPopup
{
    /**
     * @var LayoutSettings
     */
    private $layoutSettings;

    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @param LayoutSettings $layoutSettings
     * @param CaptchaConfigInterface $captchaConfig
     */
    public function __construct(
        LayoutSettings $layoutSettings,
        CaptchaConfigInterface $captchaConfig
    ) {
        $this->layoutSettings = $layoutSettings;
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
                = $this->layoutSettings->getCaptchaSettings();
        } else {
            unset($layout['components']['authenticationPopup']['children']['recaptcha']);
        }
        return Json::encode($layout);
    }
}
