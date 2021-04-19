<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaNewsletter\Test\Api\GraphQl\SubscribeEmailToNewsletter;

use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * GraphQl test for send email to friend functionality with ReCaptcha enabled.
 */
class SubscribeEmailToNewsletterCaptchaTest extends GraphQlAbstract
{
    /**
     * @magentoConfigFixture default_store customer/captcha/enable 0
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/newsletter invisible
     *
     * @return void
     */
    public function testSubscribeEmailToNewsletterValidationFailed(): void
    {
        $query = $this->getQuery('suscriber1@mail.com');

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
    subscribeEmailToNewsletter(
        input: {
          email: {$email}
        }
    ) {
    status
    }
}
QUERY;
    }
}
