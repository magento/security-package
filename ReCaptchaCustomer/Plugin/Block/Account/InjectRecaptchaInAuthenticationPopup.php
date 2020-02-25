<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Plugin\Block\Account;

use Magento\Customer\Block\Account\AuthenticationPopup;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

/**
 * Inject authentication popup in layout
 */
class InjectRecaptchaInAuthenticationPopup
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
     * @param UiConfigResolverInterface $captchaUiConfigResolver
     * @param CaptchaConfigInterface $captchaConfig
     * @param Json $serializer
     */
    public function __construct(
        UiConfigResolverInterface $captchaUiConfigResolver,
        CaptchaConfigInterface $captchaConfig,
        Json $serializer
    ) {
        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        $this->captchaConfig = $captchaConfig;
        $this->serializer = $serializer;
    }

    /**
     * @param AuthenticationPopup $subject
     * @param string $result
     * @return string
     * @throws InputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsLayout(AuthenticationPopup $subject, $result)
    {
        $layout = $this->serializer->unserialize($result);
        $key = 'customer_login';

        if ($this->captchaConfig->isCaptchaEnabledFor($key)) {
            $layout['components']['authenticationPopup']['children']['recaptcha']['settings']
                = $this->captchaUiConfigResolver->get($key);
        } else {
            unset($layout['components']['authenticationPopup']['children']['recaptcha']);
        }
        return $this->serializer->serialize($layout);
    }
}
