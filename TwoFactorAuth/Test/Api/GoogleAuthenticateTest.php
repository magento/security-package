<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\User\Model\UserFactory;
use OTPHP\TOTP;

class GoogleAuthenticateTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'twoFactorAuthGoogleAuthenticateV1';
    const OPERATION = 'GetTokenRequest';
    const RESOURCE_PATH = '/V1/tfa/provider/google/authenticate';

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var Google
     */
    private $google;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfig;

    /**
     * @var TfaInterface
     */
    private $tfa;

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->google = $objectManager->get(Google::class);
        $this->userConfig = $objectManager->get(UserConfigManagerInterface::class);
        $this->tfa = $objectManager->get(TfaInterface::class);
    }

    /**
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testInvalidCredentials()
    {
        $serviceInfo = $this->buildServiceInfo();

        try {
            $this->_webApiCall(
                $serviceInfo,
                [
                    'username' => 'customRoleUser',
                    'password' => 'bad',
                    'otp' => 'foo'
                ]
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
     * @magentoConfigFixture twofactorauth/general/force_providers duo_security
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testUnavailableProvider()
    {
        $serviceInfo = $this->buildServiceInfo();

        try {
            $this->_webApiCall(
                $serviceInfo,
                [
                    'username' => 'customRoleUser',
                    'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD,
                    'otp' => 'foo'
                ]
            );
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame('Provider is not allowed.', $response['message']);
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testInvalidToken()
    {
        $userId = $this->getUserId();
        $serviceInfo = $this->buildServiceInfo();
        $this->tfa->getProviderByCode(Google::CODE)
            ->activate($userId);

        try {
            $this->_webApiCall(
                $serviceInfo,
                [
                    'username' => 'customRoleUser',
                    'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD,
                    'otp' => 'bad'
                ]
            );
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame('Invalid code.', $response['message']);
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testNotConfiguredProvider()
    {
        $userId = $this->getUserId();
        $serviceInfo = $this->buildServiceInfo();
        $this->tfa->getProviderByCode(Google::CODE)
            ->resetConfiguration($userId);

        try {
            $this->_webApiCall(
                $serviceInfo,
                [
                    'username' => 'customRoleUser',
                    'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD,
                    'otp' => 'foo'
                ]
            );
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame('Provider is not configured.', $response['message']);
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testValidToken()
    {
        $userId = $this->getUserId();
        $otp = $this->getUserOtp();
        $serviceInfo = $this->buildServiceInfo();
        $this->tfa->getProviderByCode(Google::CODE)
            ->activate($userId);

        $response = $this->_webApiCall(
            $serviceInfo,
            [
                'username' => 'customRoleUser',
                'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD,
                'otp' => $otp
            ]
        );
        self::assertNotEmpty($response);
        self::assertRegExp('/^[a-z0-9]{32}$/', $response);
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

    private function getUserOtp(): string
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('customRoleUser');
        $totp = new TOTP($user->getEmail(), $this->google->getSecretCode($user));

        // Enable longer window of valid tokens to prevent test race condition
        $this->userConfig->addProviderConfig((int)$user->getId(), Google::CODE, ['window' => 120]);

        return $totp->now();
    }
}
