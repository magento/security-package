<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Plugin\Block\Account;

use Magento\Customer\Block\Account\AuthenticationPopup;
use Magento\ReCaptchaFrontendUi\Model\FrontendConfigInterface;
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
     * @var FrontendConfigInterface
     */
    private $reCaptchaFrontendConfig;

    /**
     * @param LayoutSettings $layoutSettings
     * @param FrontendConfigInterface $reCaptchaFrontendConfig
     */
    public function __construct(
        LayoutSettings $layoutSettings,
        FrontendConfigInterface $reCaptchaFrontendConfig
    ) {
        $this->layoutSettings = $layoutSettings;
        $this->reCaptchaFrontendConfig = $reCaptchaFrontendConfig;
    }

    /**
     * @param AuthenticationPopup $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsLayout(AuthenticationPopup $subject, $result)
    {
        $layout = Json::decode($result, Json::TYPE_ARRAY);

        if ($this->reCaptchaFrontendConfig->areKeysConfigured()) {
            $layout['components']['authenticationPopup']['children']['recaptcha']['settings']
                = $this->layoutSettings->getCaptchaSettings();
        } else {
            unset($layout['components']['authenticationPopup']['children']['recaptcha']);
        }
        return Json::encode($layout);
    }
}
