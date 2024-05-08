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

namespace Magento\ReCaptchaResendConfirmationEmail\Test\Api\GraphQl\ResendConfirmationEmail;

use Magento\TestFramework\Fixture\Config as ConfigFixture;
use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * GraphQl test for resend confirmation email functionality with ReCaptcha enabled.
 */
class ResendConfirmationEmailTest extends GraphQlAbstract
{

    #[
        ConfigFixture('recaptcha_frontend/type_invisible/public_key', 'test_public_key'),
        ConfigFixture('recaptcha_frontend/type_invisible/private_key', 'test_private_key'),
        ConfigFixture('recaptcha_frontend/type_for/resend_confirmation_email', 'invisible')
    ]
    public function testResendConfirmationEmailReCaptchaValidationFailed(): void
    {
        $query = $this->getQuery("test@example.com");

        $this->expectExceptionMessage('ReCaptcha validation failed, please try again');
        $this->graphQlMutation($query);
    }

    /**
     * @param string $email
     * @return string
     */
    private function getQuery(string $email): string
    {
        return <<<QUERY
mutation {
    resendConfirmationEmail(
        email: "{$email}"
    )
}
QUERY;
    }
}
