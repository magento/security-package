<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Model\Provider\Engine\DuoSecurity;

use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Bootstrap;
use Magento\TwoFactorAuth\Api\Data\DuoDataInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity;
use Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity\Authenticate;
use Magento\User\Model\UserFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDbIsolation enabled
 */
class AuthenticateTest extends TestCase
{
    /**
     * @var Authenticate
     */
    private $model;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var DuoSecurity|MockObject
     */
    private $duo;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    protected function setUp(): void
    {
        $objectManager = ObjectManager::getInstance();
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->tokenManager = $objectManager->get(UserConfigTokenManagerInterface::class);
        $this->tfa = $objectManager->get(TfaInterface::class);
        $this->duo = $this->createMock(DuoSecurity::class);
        $this->model = $objectManager->create(
            Authenticate::class,
            [
                'duo' => $this->duo,
            ]
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testGetAuthenticateDataInvalidCredentials()
    {
        $this->expectException(\Magento\Framework\Exception\AuthenticationException::class);
        $this->tfa->getProviderByCode(DuoSecurity::CODE)
            ->activate($this->getUserId());
        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->getAuthenticateData(
            'adminUser',
            'abc'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testGetAuthenticateDataNotConfiguredProvider()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Provider is not configured.');
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(DuoSecurity::CODE)
            ->resetConfiguration($userId);

        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->getAuthenticateData(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers authy
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testGetAuthenticateDataUnavailableProvider()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Provider is not allowed.');
        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->getAuthenticateData(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD
        );
    }
    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testVerifyInvalidCredentials()
    {
        $this->expectException(\Magento\Framework\Exception\AuthenticationException::class);
        $this->tfa->getProviderByCode(DuoSecurity::CODE)
            ->activate($this->getUserId());
        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->createAdminAccessTokenWithCredentials(
            'adminUser',
            'abc',
            'signature'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testVerifyNotConfiguredProvider()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Provider is not configured.');
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(DuoSecurity::CODE)
            ->resetConfiguration($userId);

        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->createAdminAccessTokenWithCredentials(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            'signature'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers authy
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testVerifyUnavailableProvider()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Provider is not allowed.');
        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->createAdminAccessTokenWithCredentials(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            'signature'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testGetAuthenticateDataValidRequest()
    {
        $userId = $this->getUserId();

        $this->tfa->getProviderByCode(DuoSecurity::CODE)
            ->activate($userId);

        $this->duo
            ->method('getApiHostname')
            ->willReturn('abc');
        $this->duo
            ->method('getRequestSignature')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                })
            )
            ->willReturn('cba');

        $result = $this->model->getAuthenticateData(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD
        );

        self::assertInstanceOf(DuoDataInterface::class, $result);
        self::assertSame('abc', $result->getApiHostname());
        self::assertSame('cba', $result->getSignature());
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testVerifyValidRequest()
    {
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(DuoSecurity::CODE)
            ->activate($userId);

        $signature = 'a signature';
        $this->duo->method('verify')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                }),
                $this->callback(function ($value) use ($signature) {
                    return $value->getData('sig_response') === $signature;
                })
            )
            ->willReturn(true);

        $token = $this->model->createAdminAccessTokenWithCredentials(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            $signature
        );

        self::assertNotEmpty($token);
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testVerifyInvalidRequest()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Invalid response');
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(DuoSecurity::CODE)
            ->activate($userId);

        $signature = 'a signature';
        $this->duo->method('verify')
            ->willReturn(false);

        $token = $this->model->createAdminAccessTokenWithCredentials(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            $signature
        );

        self::assertEmpty($token);
    }

    private function getUserId(): int
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('adminUser');

        return (int)$user->getId();
    }
}
