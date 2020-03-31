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
use Magento\User\Model\UserFactory;

class GoogleConfigureTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'twoFactorAuthGoogleConfigureV1';
    const OPERATION = 'GetConfigurationDataRequest';
    const RESOURCE_PATH = '/V1/tfa/provider/google/configure';

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->tokenManager = $objectManager->get(UserConfigTokenManagerInterface::class);
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testInvalidTfat()
    {
        $serviceInfo = $this->buildServiceInfo($this->getUserId());

        try {
            $this->_webApiCall($serviceInfo, ['tfaToken' => ['token' => 'abc']]);
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame('Invalid tfat token', $response['message']);
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
        $serviceInfo = $this->buildServiceInfo($userId);

        try {
            $this->_webApiCall($serviceInfo, ['tfaToken' => ['token' => $token]]);
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
    public function testValidRequest()
    {
        $userId = $this->getUserId();
        $token = $this->tokenManager->issueFor($userId);
        $serviceInfo = $this->buildServiceInfo($userId);

        $response = $this->_webApiCall($serviceInfo, ['tfaToken' => ['token' => $token]]);
        self::assertNotEmpty($response['qr_code_url']);
        self::assertStringStartsWith('data:image/png', $response['qr_code_url']);
        self::assertNotEmpty($response['secret_code']);
    }

    /**
     * @param int $userId
     * @return array
     */
    private function buildServiceInfo(int $userId): array
    {
        return [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $userId,
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
