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
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
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
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param UiConfigResolverInterface $captchaUiConfigResolver
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param Json $serializer
     */
    public function __construct(
        UiConfigResolverInterface $captchaUiConfigResolver,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        Json $serializer
    ) {
        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
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

        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)) {
            $layout['components']['authenticationPopup']['children']['recaptcha']['settings']
                = $this->captchaUiConfigResolver->get($key);
        } else {
            unset($layout['components']['authenticationPopup']['children']['recaptcha']);
        }
        return $this->serializer->serialize($layout);
    }
}
