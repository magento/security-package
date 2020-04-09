<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Bootstrap;
use Magento\TwoFactorAuth\Api\Data\U2FWebAuthnRequestInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
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
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var U2fKey|MockObject
     */
    private $u2fkey;

    /**
     * @var UserFactory
     */
    private $userFactory;

    protected function setUp()
    {
        $objectManager = ObjectManager::getInstance();
        $this->tfa = $objectManager->get(TfaInterface::class);
        $this->u2fkey = $this->createMock(U2fKey::class);
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->model = $objectManager->create(
            Authenticate::class,
            [
                'u2fKey' => $this->u2fkey
            ]
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Exception\AuthenticationException
     */
    public function testGetDataInvalidCredentials()
    {
        $this->tfa->getProviderByCode(U2fKey::CODE)
            ->activate($this->getUserId());
        $this->u2fkey
            ->expects($this->never())
            ->method('getAuthenticateData');
        $this->model->getAuthenticationData(
            'adminUser',
            'bad'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Webapi\Exception
     * @expectedExceptionMessage Provider is not configured.
     */
    public function testGetDataNotConfiguredProvider()
    {
        $this->u2fkey
            ->expects($this->never())
            ->method('getAuthenticateData');
        $this->model->getAuthenticationData(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Webapi\Exception
     * @expectedExceptionMessage Provider is not allowed.
     */
    public function testGetDataUnavailableProvider()
    {
        $this->u2fkey
            ->expects($this->never())
            ->method('getAuthenticateData');
        $this->model->getAuthenticationData(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Exception\AuthenticationException
     */
    public function testVerifyInvalidCredentials()
    {
        $this->tfa->getProviderByCode(U2fKey::CODE)
            ->activate($this->getUserId());
        $this->u2fkey
            ->expects($this->never())
            ->method('verify');
        $this->u2fkey->method('getAuthenticateData')
            ->willReturn(['credentialRequestOptions' => ['challenge' => [1, 2, 3]]]);
        $this->model->getAuthenticationData('adminUser', Bootstrap::ADMIN_PASSWORD);
        $this->model->verify(
            'adminUser',
            'bad',
            'I identify as JSON'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Webapi\Exception
     * @expectedExceptionMessage Provider is not configured.
     */
    public function testVerifyNotConfiguredProvider()
    {
        $this->u2fkey
            ->expects($this->never())
            ->method('verify');
        $this->model->verify(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            'I identify as JSON'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Webapi\Exception
     * @expectedExceptionMessage Provider is not allowed.
     */
    public function testVerifyUnavailableProvider()
    {
        $this->u2fkey
            ->expects($this->never())
            ->method('verify');
        $this->model->verify(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            'I identify as JSON'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testGetDataValidRequest()
    {
        $this->tfa->getProviderByCode(U2fKey::CODE)
            ->activate($this->getUserId());
        $userId = $this->getUserId();

        $data = ['credentialRequestOptions' => ['challenge' => [1, 2, 3]]];
        $this->u2fkey
            ->expects($this->once())
            ->method('getAuthenticateData')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                })
            )
            ->willReturn($data);

        $result = $this->model->getAuthenticationData(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD
        );

        self::assertInstanceOf(U2FWebAuthnRequestInterface::class, $result);
        self::assertSame(json_encode($data), $result->getCredentialRequestOptionsJson());
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testVerifyValidRequest()
    {
        $this->tfa->getProviderByCode(U2fKey::CODE)
            ->activate($this->getUserId());
        $userId = $this->getUserId();

        $this->u2fkey
            ->expects($this->once())
            ->method('getAuthenticateData')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                })
            )
            ->willReturn(['credentialRequestOptions' => ['challenge' => [3, 2, 1]]]);
        $this->model->getAuthenticationData('adminUser', Bootstrap::ADMIN_PASSWORD);

        $verifyData = ['foo' => 'bar'];
        $this->u2fkey
            ->expects($this->once())
            ->method('verify')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                }),
                $this->callback(function ($data) use ($verifyData) {
                    return $data->getData('publicKeyCredential') === $verifyData
                        // Assert the previously issued challenge is used for verification
                        && $data->getData('originalChallenge') === [3, 2, 1];
                })
            );

        $token = $this->model->verify(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            json_encode($verifyData)
        );
        self::assertRegExp('/^[a-z0-9]{32}$/', $token);
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Something
     */
    public function testVerifyThrowsExceptionRequest()
    {
        $this->tfa->getProviderByCode(U2fKey::CODE)
            ->activate($this->getUserId());
        $userId = $this->getUserId();

        $this->u2fkey
            ->method('getAuthenticateData')
            ->willReturn(['credentialRequestOptions' => ['challenge' => [4, 5, 6]]]);
        $this->model->getAuthenticationData('adminUser', Bootstrap::ADMIN_PASSWORD);

        $this->u2fkey
            ->method('verify')
            ->willThrowException(new \InvalidArgumentException('Something'));

        $result = $this->model->verify(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            json_encode(['foo' => 'bar'])
        );

        self::assertEmpty($result);
    }

    private function getUserId(): int
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('adminUser');

        return (int)$user->getId();
    }
}
