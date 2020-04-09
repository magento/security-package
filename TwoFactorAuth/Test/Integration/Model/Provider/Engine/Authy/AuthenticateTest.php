<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Bootstrap;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
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
     * @var Authy|MockObject
     */
    private $authy;

    /**
     * @var UserFactory
     */
    private $userFactory;

    protected function setUp()
    {
        $objectManager = ObjectManager::getInstance();
        $this->tfa = $objectManager->get(TfaInterface::class);
        $this->authy = $this->createMock(Authy::class);
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->model = $objectManager->create(
            Authenticate::class,
            [
                'authy' => $this->authy
            ]
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers authy
     * @magentoConfigFixture default/twofactorauth/authy/api_key abc
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Exception\AuthenticationException
     */
    public function testAuthenticateInvalidCredentials()
    {
        $this->tfa->getProviderByCode(Authy::CODE)
            ->activate($this->getUserId());
        $this->authy
            ->expects($this->never())
            ->method('verify');
        $this->model->authenticateWithToken(
            'adminUser',
            'bad',
            'abc'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers authy
     * @magentoConfigFixture default/twofactorauth/authy/api_key abc
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Webapi\Exception
     * @expectedExceptionMessage Provider is not configured.
     */
    public function testAuthenticateNotConfiguredProvider()
    {
        $this->authy
            ->expects($this->never())
            ->method('verify');
        $this->model->authenticateWithToken(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            'abc'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     * @expectedException \Magento\Framework\Webapi\Exception
     * @expectedExceptionMessage Provider is not allowed.
     */
    public function testAuthenticateUnavailableProvider()
    {
        $this->authy
            ->expects($this->never())
            ->method('verify');
        $this->model->authenticateWithToken(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            'abc'
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers authy
     * @magentoConfigFixture default/twofactorauth/authy/api_key abc
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testAuthenticateValidRequest()
    {
        $this->tfa->getProviderByCode(Authy::CODE)
            ->activate($this->getUserId());
        $userId = $this->getUserId();
        $this->authy
            ->expects($this->once())
            ->method('verify')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                }),
                $this->callback(function ($value) {
                    return $value->getData('tfa_code') === 'abc';
                })
            );
        $result = $this->model->authenticateWithToken(
            'adminUser',
            Bootstrap::ADMIN_PASSWORD,
            'abc'
        );

        self::assertRegExp('/^[a-z0-9]{32}$/', $result);
    }

    private function getUserId(): int
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('adminUser');

        return (int)$user->getId();
    }
}
