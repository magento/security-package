<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\Framework\App\ObjectManager;
use Magento\TwoFactorAuth\Api\Data\AuthyDeviceInterface;
use Magento\TwoFactorAuth\Api\Data\AuthyDeviceInterfaceFactory;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
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
     * @var Verification|MockObject
     */
    private $verification;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var AuthyDeviceInterfaceFactory
     */
    private $deviceDataFactory;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    protected function setUp()
    {
        $objectManager = ObjectManager::getInstance();
        $this->verification = $this->createMock(Verification::class);
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->deviceDataFactory = $objectManager->get(AuthyDeviceInterfaceFactory::class);
        $this->tokenManager = $objectManager->get(UserConfigTokenManagerInterface::class);
        $this->tfa = $objectManager->get(TfaInterface::class);
        $this->model = $objectManager->create(
            Configure::class,
            [
                'verification' => $this->verification
            ]
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers authy
     * @magentoConfigFixture default/twofactorauth/authy/api_key abc
     * @magentoDataFixture Magento/User/_files/user_with_custom_role.php
     * @expectedException \Magento\Framework\Exception\AuthorizationException
     * @expectedExceptionMessage Invalid tfa token
     */
    public function testInvalidTfat()
    {
        $userId = $this->getUserId();
        $this->verification
            ->expects($this->never())
            ->method('request');
        $this->model->sendDeviceRegistrationPrompt(
            $userId,
            'abc',
            $this->deviceDataFactory->create(
                [
                    'data' => [
                        AuthyDeviceInterface::COUNTRY => '1',
                        AuthyDeviceInterface::PHONE => '555-555-5555',
                        AuthyDeviceInterface::METHOD => AuthyDeviceInterface::METHOD_SMS,
                    ]
                ]
            )
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers authy
     * @magentoConfigFixture default/twofactorauth/authy/api_key abc
     * @magentoDataFixture Magento/User/_files/user_with_custom_role.php
     * @expectedException \Magento\Framework\Webapi\Exception
     * @expectedExceptionMessage Provider is already configured.
     */
    public function testAlreadyConfiguredProvider()
    {
        $userId = $this->getUserId();
        $this->tfa->getProviderByCode(Authy::CODE)
            ->activate($userId);
        $this->verification
            ->expects($this->never())
            ->method('request');
        $this->model->sendDeviceRegistrationPrompt(
            $userId,
            'abc',
            $this->deviceDataFactory->create(
                [
                    'data' => [
                        AuthyDeviceInterface::COUNTRY => '1',
                        AuthyDeviceInterface::PHONE => '555-555-5555',
                        AuthyDeviceInterface::METHOD => AuthyDeviceInterface::METHOD_SMS,
                    ]
                ]
            )
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoDataFixture Magento/User/_files/user_with_custom_role.php
     * @expectedException \Magento\Framework\Webapi\Exception
     * @expectedExceptionMessage Provider is not allowed.
     */
    public function testUnavailableProvider()
    {
        $userId = $this->getUserId();
        $this->verification
            ->expects($this->never())
            ->method('request');
        $this->model->sendDeviceRegistrationPrompt(
            $userId,
            'abc',
            $this->deviceDataFactory->create(
                [
                    'data' => [
                        AuthyDeviceInterface::COUNTRY => '1',
                        AuthyDeviceInterface::PHONE => '555-555-5555',
                        AuthyDeviceInterface::METHOD => AuthyDeviceInterface::METHOD_SMS,
                    ]
                ]
            )
        );
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/force_providers authy
     * @magentoConfigFixture default/twofactorauth/authy/api_key abc
     * @magentoDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testValidRequest()
    {
        $userId = $this->getUserId();

        $this->verification
            ->method('request')
            ->with(
                $this->callback(function ($value) use ($userId) {
                    return (int)$value->getId() === $userId;
                }),
                '4',
                '555-555-5555',
                AuthyDeviceInterface::METHOD_SMS,
                $this->anything()
            )
            ->willReturnCallback(
                function ($userId, $country, $phone, $method, &$response) {
                    // These keys come from authy api not our model
                    $response['message'] = 'foo';
                    $response['seconds_to_expire'] = 123;
                }
            );

        $result = $this->model->sendDeviceRegistrationPrompt(
            $userId,
            $this->tokenManager->issueFor($userId),
            $this->deviceDataFactory->create(
                [
                    'data' => [
                        AuthyDeviceInterface::COUNTRY => '4',
                        AuthyDeviceInterface::PHONE => '555-555-5555',
                        AuthyDeviceInterface::METHOD => AuthyDeviceInterface::METHOD_SMS,
                    ]
                ]
            )
        );

        self::assertSame('foo', $result->getMessage());
        self::assertSame(123, $result->getExpirationSeconds());
    }

    private function getUserId(): int
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('customRoleUser');

        return (int)$user->getId();
    }
}
