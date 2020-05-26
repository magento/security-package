<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface as GoogleConfigureData;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\User\Model\UserFactory;

class GoogleConfigureTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'twoFactorAuthGoogleConfigureV1';
    const OPERATION = 'GetConfigurationData';
    const RESOURCE_PATH = '/V1/tfa/provider/google/configure';

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var TfaInterface
     */
    private $tfa;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->tokenManager = $objectManager->get(UserConfigTokenManagerInterface::class);
        $this->tfa = $objectManager->get(TfaInterface::class);
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testInvalidTfat()
    {
        $serviceInfo = $this->buildServiceInfo();

        try {
            $this->_webApiCall($serviceInfo, ['tfaToken' => 'abc']);
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            if (json_last_error()) {
                $message = $exception->getMessage();
            } else {
                $message = $response['message'];
            }
            self::assertSame('Invalid two-factor authorization token', $message);
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers duo_security
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testUnavailableProvider()
    {
        $userId = $this->getUserId();
        $token = $this->tokenManager->issueFor($userId);
        $serviceInfo = $this->buildServiceInfo();

        try {
            $this->_webApiCall($serviceInfo, ['tfaToken' => $token]);
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            if (json_last_error()) {
                $message = $exception->getMessage();
            } else {
                $message = $response['message'];
            }
            self::assertSame('Provider is not allowed.', $message);
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testAlreadyConfiguredProvider()
    {
        $userId = $this->getUserId();
        $token = $this->tokenManager->issueFor($userId);
        $serviceInfo = $this->buildServiceInfo();
        $this->tfa->getProviderByCode(Google::CODE)
            ->activate($userId);

        try {
            $this->_webApiCall($serviceInfo, ['tfaToken' => $token]);
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            if (json_last_error()) {
                $message = $exception->getMessage();
            } else {
                $message = $response['message'];
            }
            self::assertSame('Provider is already configured.', $message);
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testValidRequest()
    {
        $userId = $this->getUserId();
        $token = $this->tokenManager->issueFor($userId);
        $serviceInfo = $this->buildServiceInfo();

        $response = $this->_webApiCall($serviceInfo, ['tfaToken' => $token]);
        self::assertNotEmpty($response[GoogleConfigureData::QR_CODE_BASE64]);
        self::assertMatchesRegularExpression('/^[a-zA-Z0-9+\/=]+$/', $response[GoogleConfigureData::QR_CODE_BASE64]);
        self::assertNotEmpty($response['secret_code']);
    }

    /**
     * @return array
     */
    private function buildServiceInfo(): array
    {
        return [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . self::OPERATION
            ]
        ];
    }

    private function getUserId(): int
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('customRoleUser');

        return (int)$user->getId();
    }
}
