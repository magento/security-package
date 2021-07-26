<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Model\Config;

use Magento\Framework\App\ObjectManager;
use Magento\TwoFactorAuth\Model\Config\UserNotifier;
use PHPUnit\Framework\TestCase;

class UserNotifierTest extends TestCase
{
    /**
     * @var UserNotifier
     */
    private $notifier;

    protected function setUp(): void
    {
        $this->notifier = ObjectManager::getInstance()->get(UserNotifier::class);
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testPersonalRequestConfigUrl()
    {
        $url = $this->notifier->getPersonalRequestConfigUrl('mytoken');
        self::assertStringContainsString('backend/tfa/tfa/index/tfat/mytoken', $url);
    }

    /**
     * @magentoAppArea webapi_rest
     */
    public function testPersonalRequestConfigUrlInWebApi()
    {
        $url = $this->notifier->getPersonalRequestConfigUrl('mytoken');
        self::assertStringContainsString('backend/tfa/tfa/index/tfat/mytoken', $url);
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testGetAppRequestConfigUrl()
    {
        $url = $this->notifier->getAppRequestConfigUrl('mytoken');
        self::assertStringContainsString('backend/tfa/tfa/index/tfat/mytoken', $url);
    }
}
