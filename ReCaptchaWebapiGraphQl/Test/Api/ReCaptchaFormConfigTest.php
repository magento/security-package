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

namespace Magento\ReCaptchaWebapiGraphQl\Test\Api;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\TestFramework\Fixture\Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Test recaptcha config query
 */
class ReCaptchaFormConfigTest extends GraphQlAbstract
{
    /**
     * @var EncryptorInterface $encryptor
     */
    private $encryptor;

    /**
     * @var \Magento\Config\Model\Config $config
     */
    private $config;

    public function setUp(): void
    {
        $this->encryptor = Bootstrap::getObjectManager()->get(EncryptorInterface::class);
        $this->config = Bootstrap::getObjectManager()->get(\Magento\Config\Model\Config::class);
    }

    #[
        Config('recaptcha_frontend/type_recaptcha_v3/score_threshold', 0.5),
        Config('recaptcha_frontend/type_recaptcha_v3/position', 'inline'),
        Config('recaptcha_frontend/type_recaptcha_v3/lang', 'en'),
        Config('recaptcha_frontend/failure_messages/validation_failure_message', 'Test validation failure message'),
        Config('recaptcha_frontend/failure_messages/technical_failure_message', 'Test technical failure message'),
        Config('recaptcha_frontend/type_for/customer_login', 'recaptcha_v3')
    ]
    public function testRecaptchaFormConfigQueryForReCaptchaV3(): void
    {
        $this->setWebapiKeys('type_recaptcha_v3');

        $result = $this->graphQlQuery($this->getQueryForForm());
        $response = [
            "recaptchaFormConfig" => [
                "is_enabled" => true,
                "configurations" => [
                    "re_captcha_type" => "RECAPTCHA_V3",
                    "badge_position" => "inline",
                    "theme" => "light",
                    "website_key" => "test_public_key",
                    "language_code" => "en",
                    "minimum_score" => 0.5,
                    "validation_failure_message" => "Test validation failure message",
                    "technical_failure_message" => "Test technical failure message"
                ]
            ]
        ];

        $this->assertEquals($response, $result, "reCaptcha config contains errors");
    }

    #[
        Config('recaptcha_frontend/type_recaptcha/position', 'bottomright'),
        Config('recaptcha_frontend/type_recaptcha/lang', 'en'),
        Config('recaptcha_frontend/failure_messages/validation_failure_message', 'Test validation failure message'),
        Config('recaptcha_frontend/failure_messages/technical_failure_message', 'Test technical failure message'),
        Config('recaptcha_frontend/type_for/customer_login', 'recaptcha')
    ]
    public function testRecaptchaFormConfigQueryForReCaptchaV2(): void
    {
        $this->setWebapiKeys('type_recaptcha');

        $result = $this->graphQlQuery($this->getQueryForForm());
        $response = [
            "recaptchaFormConfig" => [
                "is_enabled" => true,
                "configurations" => [
                    "re_captcha_type" => "RECAPTCHA",
                    "badge_position" => '',
                    "theme" => "light",
                    "website_key" => "test_public_key",
                    "minimum_score" => null,
                    "language_code" => "en",
                    "validation_failure_message" => "Test validation failure message",
                    "technical_failure_message" => "Test technical failure message"
                ]
            ]
        ];

        $this->assertEquals($response, $result, "reCaptcha config contains errors");
    }

    #[
        Config('recaptcha_frontend/type_invisible/position', 'bottomright'),
        Config('recaptcha_frontend/type_invisible/lang', 'en'),
        Config('recaptcha_frontend/failure_messages/validation_failure_message', 'Test validation failure message'),
        Config('recaptcha_frontend/failure_messages/technical_failure_message', 'Test technical failure message'),
        Config('recaptcha_frontend/type_for/customer_login', 'invisible')
    ]
    public function testRecaptchaFormConfigQueryForReCaptchaV2Invisible(): void
    {
        $this->setWebapiKeys('type_invisible');

        $result = $this->graphQlQuery($this->getQueryForForm());
        $response = [
            "recaptchaFormConfig" => [
                "is_enabled" => true,
                "configurations" => [
                    "re_captcha_type" => "INVISIBLE",
                    "badge_position" => "bottomright",
                    "theme" => "light",
                    "website_key" => "test_public_key",
                    "minimum_score" => null,
                    "language_code" => "en",
                    "validation_failure_message" => "Test validation failure message",
                    "technical_failure_message" => "Test technical failure message"
                ]
            ]
        ];

        $this->assertEquals($response, $result, "reCaptcha config contains errors");
    }

    public function testRecaptchaFormConfigQueryForNotConfiguredForm(): void
    {
        $result = $this->graphQlQuery($this->getQueryForForm());
        $response = [
            "recaptchaFormConfig" => [
                "is_enabled" => false,
                "configurations" => null
            ]
        ];
        $this->assertEquals($response, $result, "reCaptcha config contains errors");
    }

    /**
     * Generates wepapi private/public key for reCaptcha config
     *
     * @param string $captchaType
     */
    private function setWebapiKeys(string $captchaType)
    {
        $this->config->setDataByPath(
            "recaptcha_frontend/{$captchaType}/public_key",
            $this->encryptor->encrypt('test_public_key')
        );
        $this->config->setDataByPath(
            "recaptcha_frontend/{$captchaType}/private_key",
            $this->encryptor->encrypt('test_private_key')
        );
        $this->config->save();
    }

    /**
     * Returns formatted query for reCaptcha configuration
     */
    private function getQueryForForm(): string
    {
        $query = <<<QUERY
        query
        {
        recaptchaFormConfig(formType: CUSTOMER_LOGIN){
          is_enabled
          configurations{
            re_captcha_type
            badge_position
            theme
            website_key
            language_code
            minimum_score
            validation_failure_message
            technical_failure_message
            }
        }
    }
QUERY;

        return $query;
    }

    public function tearDown(): void
    {
        /** @var ResourceConnection $resource */
        $resource = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        /** @var AdapterInterface $connection */
        $connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);

        $connection->delete(
            $resource->getTableName('core_config_data')
        );
        parent::tearDown();
    }
}
