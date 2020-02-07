<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Plugin\Block\Account;

use Magento\Customer\Block\Account\AuthenticationPopup;
use Magento\ReCaptcha\Model\ConfigInterface;
use Magento\ReCaptcha\Model\LayoutSettings;
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
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param LayoutSettings $layoutSettings
     * @param ConfigInterface $config
     */
    public function __construct(
        LayoutSettings $layoutSettings,
        ConfigInterface $config
    ) {
        $this->layoutSettings = $layoutSettings;
        $this->config = $config;
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

        if ($this->config->isEnabledFrontend()) {
            $layout['components']['authenticationPopup']['children']['recaptcha']['settings']
                = $this->layoutSettings->getCaptchaSettings();
        } else {
            unset($layout['components']['authenticationPopup']['children']['recaptcha']);
        }
        return Json::encode($layout);
    }
}
