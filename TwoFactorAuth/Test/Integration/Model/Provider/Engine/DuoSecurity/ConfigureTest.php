<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Model\Provider\Engine\DuoSecurity;

use Magento\Framework\App\ObjectManager;
use Magento\TwoFactorAuth\Api\Data\DuoDataInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity;
use Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity\Authenticate;
use Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity\Configure;
use Magento\User\Model\UserFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDbIsolation enabled
 */
class ConfigureTest extends TestCase
{
    /**
     * @var Configure
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

    /**
     * @var Authenticate|MockObject
     */
    private $authenticate;

    protected function setUp(): void
    {
        $objectManager = ObjectManager::getInstance();
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->tokenManager = $objectManager->get(UserConfigTokenManagerInterface::class);
        $this->tfa = $objectManager->get(TfaInterface::class);
        $this->duo = $this->createMock(DuoSecurity::class);
        $this->authenticate = $this->createMock(Authenticate::class);
        $this->model = $objectManager->create(
            Configure::class,
            [
                'duo' => $this->duo,
                'authenticate' => $this->authenticate
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
    public function testGetConfigurationDataInvalidTfat()
    {
        $this->expectException(\Magento\Framework\Exception\AuthorizationException::class);
        $this->expectExceptionMessage('Invalid two-factor authorization token');
        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->getConfigurationData(
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
    public function testGetConfigurationDataAlreadyConfiguredProvider()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Provider is already configured.');
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(DuoSecurity::CODE)
            ->activate($userId);

        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->getConfigurationData(
            $this->tokenManager->issueFor($userId)
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers authy
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testGetConfigurationDataUnavailableProvider()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Provider is not allowed.');
        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->getConfigurationData(
            $this->tokenManager->issueFor($this->getUserId())
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testActivateInvalidTfat()
    {
        $this->expectException(\Magento\Framework\Exception\AuthorizationException::class);
        $this->expectExceptionMessage('Invalid two-factor authorization token');
        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->activate(
            'abc',
            'something'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testActivateAlreadyConfiguredProvider()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Provider is already configured.');
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(DuoSecurity::CODE)
            ->activate($userId);
        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->activate(
            $this->tokenManager->issueFor($userId),
            'something'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers authy
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testActivateUnavailableProvider()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Provider is not allowed.');
        $userId = $this->getUserId();
        $this->duo
            ->expects($this->never())
            ->method('getRequestSignature');
        $this->model->activate(
            $this->tokenManager->issueFor($userId),
            'something'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testGetConfigurationDataValidRequest()
    {
        $userId = $this->getUserId();

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

        $result = $this->model->getConfigurationData(
            $this->tokenManager->issueFor($userId)
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
    public function testActivateValidRequest()
    {
        $userId = $this->getUserId();
        $tfat = $this->tokenManager->issueFor($userId);

        $signature = 'a signature';
        $this->authenticate->method('assertResponseIsValid')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                }),
                $signature
            );

        $this->model->activate($tfat, $signature);

        self::assertTrue($this->tfa->getProviderByCode(DuoSecurity::CODE)->isActive($userId));
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key abc123
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname abc123
     * @magentoConfigFixture default/twofactorauth/duo/secret_key abc123
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testActivateInvalidDataThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Something');
        $userId = $this->getUserId();
        $tfat = $this->tokenManager->issueFor($userId);

        $signature = 'a signature';
        $this->authenticate->method('assertResponseIsValid')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                }),
                $signature
            )
            ->willThrowException(new \InvalidArgumentException('Something'));

        $result = $this->model->activate($tfat, $signature);

        self::assertEmpty($result);
    }

    private function getUserId(): int
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('adminUser');

        return (int)$user->getId();
    }
}
