<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Model;

use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaWebapiApi\Api\Data\EndpointInterface;
use Magento\ReCaptchaWebapiApi\Api\WebapiValidationConfigProviderInterface;

/**
 * Provide customer related endpoint configuration.
 */
class WebapiConfigProvider implements WebapiValidationConfigProviderInterface
{
    private const RESET_PASSWORD_CAPTCHA_ID = 'customer_forgot_password';

    private const CHANGE_PASSWORD_CAPTCHA_ID = 'customer_edit';

    private const LOGIN_CAPTCHA_ID = 'customer_login';

    private const CREATE_CAPTCHA_ID = 'customer_create';

    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isEnabled;

    /**
     * @var ValidationConfigResolverInterface
     */
    private $configResolver;

    /**
     * @param IsCaptchaEnabledInterface $isEnabled
     * @param ValidationConfigResolverInterface $configResolver
     */
    public function __construct(IsCaptchaEnabledInterface $isEnabled, ValidationConfigResolverInterface $configResolver)
    {
        $this->isEnabled = $isEnabled;
        $this->configResolver = $configResolver;
    }

    /**
     * Validates ifChangedPasswordCaptchaEnabled using captcha_id
     *
     * @param string $serviceMethod
     * @param string $serviceClass
     * @return ValidationConfigInterface|null
     */
    private function validateChangePasswordCaptcha($serviceMethod, $serviceClass): ?ValidationConfigInterface
    {
        //phpcs:disable Magento2.PHP.LiteralNamespaces
        if ($serviceMethod === 'resetPassword' || $serviceMethod === 'initiatePasswordReset'
            || $serviceClass === 'Magento\CustomerGraphQl\Model\Resolver\ResetPassword'
            || $serviceClass === 'Magento\CustomerGraphQl\Model\Resolver\RequestPasswordResetEmail'
        ) {
            return $this->isEnabled->isCaptchaEnabledFor(self::RESET_PASSWORD_CAPTCHA_ID) ?
                $this->configResolver->get(self::RESET_PASSWORD_CAPTCHA_ID) : null;
        } elseif ($serviceMethod === 'changePasswordById'
            || $serviceClass === 'Magento\CustomerGraphQl\Model\Resolver\ChangePassword'
        ) {
            return $this->isEnabled->isCaptchaEnabledFor(self::CHANGE_PASSWORD_CAPTCHA_ID) ?
                $this->configResolver->get(self::CHANGE_PASSWORD_CAPTCHA_ID) : null;
        }
        //phpcs:enable Magento2.PHP.LiteralNamespaces

        return null;
    }

    /**
     * Validates ifLoginCaptchaEnabled using captcha_id
     *
     * @return ValidationConfigInterface|null
     */
    private function validateLoginCaptcha(): ?ValidationConfigInterface
    {
        return  $this->isEnabled->isCaptchaEnabledFor(self::LOGIN_CAPTCHA_ID) ?
            $this->configResolver->get(self::LOGIN_CAPTCHA_ID) : null;
    }

    /**
     * Validates ifCreateCustomerCaptchaEnabled using captcha_id
     *
     * @return ValidationConfigInterface|null
     */
    private function validateCreateCustomerCaptcha(): ?ValidationConfigInterface
    {
        return  $this->isEnabled->isCaptchaEnabledFor(self::CREATE_CAPTCHA_ID) ?
            $this->configResolver->get(self::CREATE_CAPTCHA_ID) : null;
    }

    /**
     * @inheritDoc
     */
    public function getConfigFor(EndpointInterface $endpoint): ?ValidationConfigInterface
    {
        $serviceClass = $endpoint->getServiceClass();
        $serviceMethod = $endpoint->getServiceMethod();

        //phpcs:disable Magento2.PHP.LiteralNamespaces
        if (($serviceClass === 'Magento\Integration\Api\CustomerTokenServiceInterface'
                && $serviceMethod === 'createCustomerAccessToken')
            || $serviceClass === 'Magento\CustomerGraphQl\Model\Resolver\GenerateCustomerToken'
        ) {
            return $this->validateLoginCaptcha();
        } elseif (($serviceClass === 'Magento\Customer\Api\AccountManagementInterface'
                && $serviceMethod === 'createAccount')
            || $serviceClass === 'Magento\CustomerGraphQl\Model\Resolver\CreateCustomer'
        ) {
            return $this->validateCreateCustomerCaptcha();
        }
        //phpcs:enable Magento2.PHP.LiteralNamespaces

        return $this->validateChangePasswordCaptcha($serviceMethod, $serviceClass);
    }
}
