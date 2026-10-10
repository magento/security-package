<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Unit\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TwoFactorAuth\Model\Config\UserNotifier as UserNotifierConfig;
use Magento\TwoFactorAuth\Model\EmailUserNotifier;
use Magento\User\Model\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EmailUserNotifierTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var TransportBuilder|MockObject
     */
    private $transportBuilderMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var UserNotifierConfig|MockObject
     */
    private $userNotifierConfigMock;

    /**
     * @var Emulation|MockObject
     */
    private $appEmulationMock;

    /**
     * @var TransportInterface|MockObject
     */
    private $transportMock;

    /**
     * @var EmailUserNotifier
     */
    private $notifier;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->transportBuilderMock = $this->createMock(TransportBuilder::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->userNotifierConfigMock = $this->createMock(UserNotifierConfig::class);
        $this->appEmulationMock = $this->createMock(Emulation::class);
        $this->transportMock = $this->createMock(TransportInterface::class);

        $this->transportBuilderMock->method('setTemplateIdentifier')->willReturnSelf();
        $this->transportBuilderMock->method('setTemplateOptions')->willReturnSelf();
        $this->transportBuilderMock->method('setTemplateVars')->willReturnSelf();
        $this->transportBuilderMock->method('setFromByScope')->willReturnSelf();
        $this->transportBuilderMock->method('addTo')->willReturnSelf();
        $this->transportBuilderMock->method('getTransport')->willReturn($this->transportMock);

        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getFrontendName')->willReturn('Main Website Store');
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $this->notifier = new EmailUserNotifier(
            $this->scopeConfigMock,
            $this->transportBuilderMock,
            $this->storeManagerMock,
            $this->createMock(LoggerInterface::class),
            $this->userNotifierConfigMock,
            $this->appEmulationMock
        );
    }

    /**
     * The email is built for store 0/adminhtml, but the send-suppression plugin reads
     * system/smtp/disable from whatever store the environment currently resolves to. The send call
     * must happen inside default-store/adminhtml emulation so that check is scoped correctly too.
     */
    public function testSendMessageIsEmulatedAsDefaultStoreAdminhtml(): void
    {
        $callOrder = [];
        $this->appEmulationMock->expects($this->once())
            ->method('startEnvironmentEmulation')
            ->with(Store::DEFAULT_STORE_ID, Area::AREA_ADMINHTML)
            ->willReturnCallback(function () use (&$callOrder) {
                $callOrder[] = 'start';
            });
        $this->transportMock->expects($this->once())
            ->method('sendMessage')
            ->willReturnCallback(function () use (&$callOrder) {
                $callOrder[] = 'send';
            });
        $this->appEmulationMock->expects($this->once())
            ->method('stopEnvironmentEmulation')
            ->willReturnCallback(function () use (&$callOrder) {
                $callOrder[] = 'stop';
            });

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('admin@example.com');
        $user->method('getFirstName')->willReturn('Jane');
        $user->method('getLastName')->willReturn('Doe');

        $this->userNotifierConfigMock->method('getPersonalRequestConfigUrl')->willReturn('http://example.com/token');

        $this->notifier->sendUserConfigRequestMessage($user, 'token');

        self::assertSame(['start', 'send', 'stop'], $callOrder);
    }

    /**
     * Emulation must be torn down even if sending itself throws, otherwise the admin store context
     * leaks into the rest of the request.
     */
    public function testEmulationIsStoppedEvenWhenSendMessageThrows(): void
    {
        $this->appEmulationMock->expects($this->once())->method('startEnvironmentEmulation');
        $this->transportMock->method('sendMessage')->willThrowException(new \RuntimeException('SMTP down'));
        $this->appEmulationMock->expects($this->once())->method('stopEnvironmentEmulation');

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('admin@example.com');
        $user->method('getFirstName')->willReturn('Jane');
        $user->method('getLastName')->willReturn('Doe');

        $this->userNotifierConfigMock->method('getAppRequestConfigUrl')->willReturn('http://example.com/token');

        $this->expectException(\Magento\TwoFactorAuth\Model\Exception\NotificationException::class);
        $this->notifier->sendAppConfigRequestMessage($user, 'token');
    }
}
