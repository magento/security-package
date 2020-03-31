<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Config\Model\Config;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Bootstrap as TestBootstrap;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\User\Model\UserFactory;

class AdminIntegrationTokenTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'integrationAdminTokenServiceV1';
    const OPERATION = 'CreateAdminAccessTokenRequest';
    const RESOURCE_PATH = '/V1/integration/admin/token';

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfig;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var Config
     */
    private $config;

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->userConfig = $objectManager->get(UserConfigManagerInterface::class);
        $this->tfa = $objectManager->get(TfaInterface::class);
        $this->config = $objectManager->get(Config::class);
    }

    /**
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testDefaultBehaviorForInvalidCredentials()
    {
        $serviceInfo = $this->buildServiceInfo();

        try {
            $this->_webApiCall(
                $serviceInfo,
                ['username' => 'customRoleUser', 'password' => 'bad']
            );
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame(
                'The account sign-in was incorrect or your account is disabled temporarily. '
                . 'Please wait and try again later.',
                $response['message']
            );
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testUserWithConfigured2fa()
    {
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(Google::CODE)->activate($userId);
        $serviceInfo = $this->buildServiceInfo();

        try {
            $this->_webApiCall(
                $serviceInfo,
                ['username' => 'customRoleUser', 'password' => TestBootstrap::ADMIN_PASSWORD]
            );
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame('Please use the 2fa provider-specific endpoints to obtain a token.', $response['message']);
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers duo_security
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testUserWithAvailableButUnconfigured2fa()
    {
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(Google::CODE)->activate($userId);
        $serviceInfo = $this->buildServiceInfo();

        try {
            $this->_webApiCall(
                $serviceInfo,
                ['username' => 'customRoleUser', 'password' => TestBootstrap::ADMIN_PASSWORD]
            );
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame(
                'You are required to configure personal Two-Factor Authorization in order to login. '
                . 'Please check your email.',
                $response['message']
            );
        }
    }

    /**
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testNoAvailable2faProviders()
    {
        $this->config->setDataByPath('twofactorauth/general/force_providers', '');
        $this->config->save();
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(Google::CODE)->activate($userId);
        $serviceInfo = $this->buildServiceInfo();

        try {
            $this->_webApiCall(
                $serviceInfo,
                ['username' => 'customRoleUser', 'password' => TestBootstrap::ADMIN_PASSWORD]
            );
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame(
                'Please ask an administrator with sufficient access to configure 2FA first',
                $response['message']
            );
        }
    }

    /**
     * @return array
     */
    private function buildServiceInfo(): array
    {
        return [
            'rest' => [
                // Ensure the default auth is invalidated
                'token' => 'invalid',
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST
            ],
            'soap' => [
                // Ensure the default auth is invalidated
                'token' => 'invalid',
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
