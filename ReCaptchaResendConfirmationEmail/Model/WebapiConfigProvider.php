<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained from
 * Adobe.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaResendConfirmationEmail\Model;

use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaWebapiApi\Api\Data\EndpointInterface;
use Magento\ReCaptchaWebapiApi\Api\WebapiValidationConfigProviderInterface;
use Magento\CustomerGraphQl\Model\Resolver\ResendConfirmationEmail;

/**
 * Provide ResendConfirmationEmail related endpoint configuration.
 */
class WebapiConfigProvider implements WebapiValidationConfigProviderInterface
{
    /**
     * Captcha id from config.
     */
    private const CAPTCHA_ID = 'resend_confirmation_email';

    /**
     * @param IsCaptchaEnabledInterface $isEnabled
     * @param ValidationConfigResolverInterface $configResolver
     */
    public function __construct(
        private readonly IsCaptchaEnabledInterface $isEnabled,
        private readonly ValidationConfigResolverInterface $configResolver
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getConfigFor(EndpointInterface $endpoint): ?ValidationConfigInterface
    {
        if ($endpoint->getServiceMethod() === 'resolve'
            && $endpoint->getServiceClass() === ResendConfirmationEmail::class
        ) {
            if ($this->isEnabled->isCaptchaEnabledFor(self::CAPTCHA_ID)) {
                return $this->configResolver->get(self::CAPTCHA_ID);
            }
        }

        return null;
    }
}
