<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Unit\Model\Provider\Engine;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google\TotpFactory;
use Magento\User\Api\Data\UserInterface;
use OTPHP\TOTPInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class GoogleTest extends TestCase
{
    /**
     * @var Google
     */
    private $model;

    /**
     * @var UserInterface|MockObject
     */
    private $user;

    /**
     * @var UserConfigManagerInterface|MockObject
     */
    private $configManager;

    /**
     * @var TOTPInterfaceFactory|MockObject
     */
    private $totpFactory;

    /**
     * @var TOTPInterface|MockObject
     */
    private $totp;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfig;

    /**
     * @var EncryptorInterface|MockObject
     */
    private $encryptor;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->totpFactory = $this->createMock(TotpFactory::class);
        $this->totp = $this->createMock(TOTPInterface::class);
        $this->user = $this->createMock(UserInterface::class);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->encryptor = $this->createMock(EncryptorInterface::class);
        $this->user->method('getId')
            ->willReturn('5');
        $this->user->method('getEmail')
            ->willReturn('john@example.com');
        $this->configManager = $this->createMock(UserConfigManagerInterface::class);
        $this->model = $objectManager->getObject(
            Google::class,
            [
                'configManager' => $this->configManager,
                'totpFactory' => $this->totpFactory,
                'scopeConfig' => $this->scopeConfig,
                'encryptor' => $this->encryptor
            ]
        );
    }

    /**
     * Check that the provider is available based on configuration.
     *
     * @return void
     */
    public function testIsEnabled(): void
    {
        $this->assertTrue($this->model->isEnabled());
    }

    public function testVerifyWithNoToken()
    {
        $valid = $this->model->verify($this->user, new DataObject(['tfa_code' => '']));

        self::assertFalse($valid);
    }

    public function testVerifyWithBadToken()
    {
        $this->configManager->method('getProviderConfig')
            ->willReturn(['secret' => 'cba']);
        $this->encryptor->method('decrypt')
            ->with('cba')
            ->willReturn('abc');
        $this->totpFactory->method('create')
            ->willReturn($this->totp);
        $this->totp->method('verify')
            ->with('123456')
            ->willReturn(false);

        $valid = $this->model->verify($this->user, new DataObject(['tfa_code' => '123456']));

        self::assertFalse($valid);
    }

    public function testVerifyWithGoodToken()
    {
        $this->configManager->method('getProviderConfig')
            ->willReturn(['secret' => 'cba']);
        $this->encryptor->method('decrypt')
            ->with('cba')
            ->willReturn('abc');
        $this->totpFactory->method('create')
            ->with('abc')
            ->willReturn($this->totp);
        $this->totp->method('verify')
            ->with('123456')
            ->willReturn(true);

        $valid = $this->model->verify($this->user, new DataObject(['tfa_code' => '123456']));

        self::assertTrue($valid);
    }

    public function testVerifyWithGoodTokenAndWindowFromUserConfig()
    {
        $this->configManager->method('getProviderConfig')
            ->willReturn(['secret' => 'cba', 'window' => 800]);
        $this->encryptor->method('decrypt')
            ->with('cba')
            ->willReturn('abc');
        $this->totpFactory->method('create')
            ->willReturn($this->totp);
        $this->totp->method('verify')
            ->with('123456', null, 800)
            ->willReturn(true);

        $valid = $this->model->verify($this->user, new DataObject(['tfa_code' => '123456']));

        self::assertTrue($valid);
    }

    public function testVerifyWithGoodTokenAndWindowFromScopeConfig()
    {
        $this->scopeConfig->method('getValue')
            ->willReturn(800);
        $this->configManager->method('getProviderConfig')
            ->willReturn(['secret' => 'cba']);
        $this->encryptor->method('decrypt')
            ->with('cba')
            ->willReturn('abc');
        $this->totpFactory->method('create')
            ->willReturn($this->totp);
        $this->totp->method('verify')
            ->with('123456', null, 800)
            ->willReturn(true);

        $valid = $this->model->verify($this->user, new DataObject(['tfa_code' => '123456']));

        self::assertTrue($valid);
    }

    public function testVerifyWindowFromUserConfigOverridesScopeConfig()
    {
        $this->scopeConfig->method('getValue')
            ->willReturn(800);
        $this->configManager->method('getProviderConfig')
            ->willReturn(['secret' => 'cba', 'window' => 500]);
        $this->encryptor->method('decrypt')
            ->with('cba')
            ->willReturn('abc');
        $this->totpFactory->method('create')
            ->willReturn($this->totp);
        $this->totp->method('verify')
            ->with('123456', null, 500)
            ->willReturn(true);

        $valid = $this->model->verify($this->user, new DataObject(['tfa_code' => '123456']));

        self::assertTrue($valid);
    }
}
