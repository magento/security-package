<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Command;

use Magento\Framework\App\ObjectManager;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Command\GoogleSecret;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\User\Model\UserFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @magentoDbIsolation enabled
 */
class GoogleSecretTest extends TestCase
{
    /**
     * @var GoogleSecret
     */
    private $command;

    /**
     * @var UserConfigManagerInterface
     */
    private $configManager;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var MockObject|InputInterface
     */
    private $consoleInput;

    /**
     * @var MockObject|OutputInterface
     */
    private $consoleOutput;

    /**
     * @var Google
     */
    private $google;

    protected function setUp(): void
    {
        $objectManager = ObjectManager::getInstance();
        $this->command = $objectManager->get(GoogleSecret::class);
        $this->configManager = $objectManager->get(UserConfigManagerInterface::class);
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->consoleInput = $this->createMock(InputInterface::class);
        $this->consoleOutput = $this->createMock(OutputInterface::class);
        $this->google = $objectManager->get(Google::class);
    }

    /**
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testSetSecret()
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('adminUser');
        $userId = (int)$user->getId();

        self::assertFalse(
            $this->configManager->isProviderConfigurationActive(
                $userId,
                Google::CODE
            )
        );
        $this->command->run(
            new ArgvInput(['security:tfa:google:set-secret', 'adminUser', 'MFRGGZDF']),
            $this->consoleOutput
        );
        self::assertTrue(
            $this->configManager->isProviderConfigurationActive(
                $userId,
                Google::CODE
            )
        );
        self::assertSame(
            'MFRGGZDF',
            $this->google->getSecretCode($user)
        );
    }
}
