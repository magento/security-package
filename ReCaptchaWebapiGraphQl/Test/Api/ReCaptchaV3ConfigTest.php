<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaWebapiGraphQl\Test\Api;

use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Paypal\Model\Payflow\Service\Request\SecureToken;
use Magento\TestFramework\Fixture\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Test recaptcha config query
 */
class ReCaptchaV3ConfigTest extends GraphQlAbstract
{

    #[
        Config(
            'recaptcha_frontend/type_recaptcha_v3/public_key',
            '0:3:Ent2kwHywmyrrUVQdCZBv6ORhf1F8AvaNAMQdQ0I5azeDnIXxcllym9jZA=='
        ),
        Config(
            'recaptcha_frontend/type_recaptcha_v3/private_key',
            '0:3:wVQDJLt9bSnKThKbczu4In1iXoU+Bs4STrrgdmuZQ1Vk5olAwBzX3yMF2Yo='
        ),
        Config('recaptcha_frontend/type_recaptcha_v3/score_threshold', 0.75),
        Config('recaptcha_frontend/type_recaptcha_v3/position', 'bottomright'),
        Config('recaptcha_frontend/type_recaptcha_v3/lang', 'en'),
        Config('recaptcha_frontend/type_recaptcha_v3/validation_failure_message', 'Test failure message'),
        Config('recaptcha_frontend/type_for/customer_login', 'recaptcha_v3'),
    ]
    public function testQuery(): void
    {
        $query = <<<QUERY
query {
    recaptchaV3Config {
        website_key
        minimum_score
        badge_position
        language_code
        failure_message
        forms
    }
}
QUERY;

        $response = $this->graphQlQuery($query);
        $this->assertEquals(
            [
                'recaptchaV3Config' => [
                    'website_key' => 'test_public_key',
                    'minimum_score' => 0.75,
                    'badge_position' => 'bottomright',
                    'language_code' => 'en',
                    'failure_message' => 'reCAPTCHA verification failed.',
                    'forms' => [
                        'CUSTOMER_LOGIN'
                    ]
                ]
            ],
            $this->graphQlQuery($query)
        );
    }

}
