<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

use Magento\Framework\App\ObjectManager;
use Magento\TwoFactorAuth\Api\Data\U2FWebAuthnRequestInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
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
     * @var U2fKey|MockObject
     */
    private $u2fkey;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    protected function setUp()
    {
        $objectManager = ObjectManager::getInstance();
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->tokenManager = $objectManager->get(UserConfigTokenManagerInterface::class);
        $this->tfa = $objectManager->get(TfaInterface::class);
        $this->u2fkey = $this->createMock(U2fKey::class);
        $this->model = $objectManager->create(
            Configure::class,
            [
                'u2fKey' => $this->u2fkey
            ]
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Exception\AuthorizationException
     * @expectedExceptionMessage Invalid two-factor authorization token
     */
    public function testGetRegistrationDataInvalidTfat()
    {
        $userId = $this->getUserId();
        $this->u2fkey
            ->expects($this->never())
            ->method('getRegisterData');
        $this->model->getRegistrationData(
            'abc'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Provider is already configured.
     */
    public function testGetRegistrationDataAlreadyConfiguredProvider()
    {
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(U2fKey::CODE)
            ->activate($userId);
        $this->u2fkey
            ->expects($this->never())
            ->method('getRegisterData');
        $this->model->getRegistrationData(
            $this->tokenManager->issueFor($userId)
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Provider is not allowed.
     */
    public function testGetRegistrationDataUnavailableProvider()
    {
        $userId = $this->getUserId();
        $this->u2fkey
            ->expects($this->never())
            ->method('getRegisterData');
        $this->model->getRegistrationData(
            $this->tokenManager->issueFor($userId)
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Exception\AuthorizationException
     * @expectedExceptionMessage Invalid two-factor authorization token
     */
    public function testActivateInvalidTfat()
    {
        $userId = $this->getUserId();
        $this->u2fkey
            ->expects($this->never())
            ->method('registerDevice');
        $this->model->activate(
            'abc',
            'I identify as JSON'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Provider is already configured.
     */
    public function testActivateAlreadyConfiguredProvider()
    {
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(U2fKey::CODE)
            ->activate($userId);
        $this->u2fkey
            ->expects($this->never())
            ->method('registerDevice');
        $this->model->activate(
            $this->tokenManager->issueFor($userId),
            'I identify as JSON'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Provider is not allowed.
     */
    public function testActivateUnavailableProvider()
    {
        $userId = $this->getUserId();
        $this->u2fkey
            ->expects($this->never())
            ->method('registerDevice');
        $this->model->activate(
            $this->tokenManager->issueFor($userId),
            'I identify as JSON'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testGetRegistrationDataValidRequest()
    {
        $userId = $this->getUserId();
        $data = ['publicKey' => ['challenge' => [1, 2, 3]]];

        $this->u2fkey
            ->method('getRegisterData')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                })
            )
            ->willReturn($data);

        $result = $this->model->getRegistrationData(
            $this->tokenManager->issueFor($userId)
        );

        self::assertInstanceOf(U2FWebAuthnRequestInterface::class, $result);
        self::assertSame(json_encode($data), $result->getCredentialRequestOptionsJson());
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testActivateValidRequest()
    {
        $userId = $this->getUserId();
        $tfat = $this->tokenManager->issueFor($userId);

        $this->u2fkey
            ->method('getRegisterData')
            ->willReturn(['publicKey' => ['challenge' => [3, 2, 1]]]);
        $this->model->getRegistrationData($tfat);

        $activateData = ['foo' => 'bar'];
        $this->u2fkey
            ->method('registerDevice')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                }),
                [
                    'publicKeyCredential' => $activateData,
                    // Asserts the previously issued challenge was used for verification
                    'challenge' => [3, 2, 1]
                ]
            );

        $this->model->activate($tfat, json_encode($activateData));

        // Mock registerDevice call above is proof of activation
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Something
     */
    public function testActivateInvalidKeyDataThrowsException()
    {
        $userId = $this->getUserId();
        $tfat = $this->tokenManager->issueFor($userId);

        $this->u2fkey
            ->method('getRegisterData')
            ->willReturn(['publicKey' => ['challenge' => [3, 2, 1]]]);
        $this->model->getRegistrationData($tfat);

        $this->u2fkey
            ->method('registerDevice')
            ->willThrowException(new \InvalidArgumentException('Something'));

        $result = $this->model->activate($tfat, json_encode(['foo' => 'bar']));

        self::assertEmpty($result);
    }

    private function getUserId(): int
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('adminUser');

        return (int)$user->getId();
    }
}
