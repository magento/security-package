<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Plugin\Customer;

use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\Customer\ViewModel\CreateAccountButton;

/**
 * Disable button Create Account while captcha is loading
 */
class DisableCreateAccountButton
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
     * Temporally disable button Create Account while captcha is loading
     *
     * @param CreateAccountButton $subject
     * @return bool
     * @throws InputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDisabled(CreateAccountButton $subject): bool
    {
        $key = 'customer_create';
        return $this->isCaptchaEnabled->isCaptchaEnabledFor($key);
    }
}
