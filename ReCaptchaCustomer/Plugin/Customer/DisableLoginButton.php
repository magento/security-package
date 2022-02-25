<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Plugin\Customer;

use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;
use Magento\Customer\ViewModel\LoginButton;

/**
 * Disable Login button while captcha is loading
 */
class DisableLoginButton
{
    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     */
    public function __construct(
        IsCaptchaEnabledInterface $isCaptchaEnabled
    ) {
        $this->isCaptchaEnabled = $isCaptchaEnabled;
    }

    /**
     * Temporally disable Login button while captcha is loading
     *
     * @param LoginButton $subject
     * @return bool
     * @throws InputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDisabled(LoginButton $subject): bool
    {
        $key = 'customer_login';
        return $this->isCaptchaEnabled->isCaptchaEnabledFor($key);
    }
}
