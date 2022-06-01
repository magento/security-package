<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Plugin\Customer;

use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\Customer\ViewModel\ForgotPasswordButton;

/**
 * Disable Forgot password button while captcha is loading
 */
class DisableForgotPasswordButton
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
     * Temporally disable Forgot password button while captcha is loading
     *
     * @param ForgotPasswordButton $subject
     * @return bool
     * @throws InputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDisabled(ForgotPasswordButton $subject): bool
    {
        $key = 'customer_forgot_password';
        return $this->isCaptchaEnabled->isCaptchaEnabledFor($key);
    }
}
