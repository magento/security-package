<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\UserFactory;

/**
 * @magentoDbIsolation enabled
 */
class UserConfigTokenManagerTest extends TestCase
{
    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var DateTime|MockObject
     */
    private $dateTimeMock;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->dateTimeMock = $this->getMockBuilder(DateTime::class)->disableOriginalConstructor()->getMock();
        $this->userFactory = Bootstrap::getObjectManager()->get(UserFactory::class);
        $this->tokenManager = Bootstrap::getObjectManager()->create(
            UserConfigTokenManagerInterface::class,
            ['dateTime' => $this->dateTimeMock]
        );
    }

    /**
     * Test that issued tokens are valid for specific users and can expire.
     *
     * @return void
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testToken(): void
    {
        $time = time();
        $this->dateTimeMock->method('timestamp')
            ->willReturnCallback(
                function () use (&$time) {
                    return $time;
                }
            );
        /** @var \Magento\User\Model\User $user1 */
        $user1 = $this->userFactory->create();
        $user1->loadByUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);
        /** @var \Magento\User\Model\User $user2 */
        $user2 = $this->userFactory->create();
        $user2->loadByUsername('adminUser');
        $this->assertNotEmpty($user1->getId());
        $this->assertNotEmpty($user2->getId());

        //Checking that tokens for different users are different even when issued at the same time.
        $token1 = $this->tokenManager->issueFor((int)$user1->getId());
        $token2 = $this->tokenManager->issueFor((int)$user2->getId());
        $this->assertNotEquals($token1, $token2);

        //Checking that valid tokens are only valid for corresponding users.
        $time += 5;
        $this->assertTrue($this->tokenManager->isValidFor((int)$user1->getId(), $token1));
        $this->assertTrue($this->tokenManager->isValidFor((int)$user2->getId(), $token2));
        $this->assertFalse($this->tokenManager->isValidFor((int)$user1->getId(), $token2));
        $this->assertFalse($this->tokenManager->isValidFor((int)$user2->getId(), $token1));

        //Checking that tokens do expire
        $time += 3601;
        $this->assertFalse($this->tokenManager->isValidFor((int)$user1->getId(), $token1));
        $this->assertFalse($this->tokenManager->isValidFor((int)$user2->getId(), $token2));
    }
}
