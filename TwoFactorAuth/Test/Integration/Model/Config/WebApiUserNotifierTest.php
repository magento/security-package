<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Model\Config;

use Magento\Framework\App\ObjectManager;
use Magento\TwoFactorAuth\Model\Config\WebApiUserNotifier;
use PHPUnit\Framework\TestCase;

class WebApiUserNotifierTest extends TestCase
{
    /**
     * @var WebApiUserNotifier
     */
    private $notifier;

    protected function setUp(): void
    {
        $this->notifier = ObjectManager::getInstance()->get(WebApiUserNotifier::class);
    }

    /**
     * @magentoConfigFixture default/twofactorauth/general/webapi_notification_url example.com/:tfat/abc
     * @magentoAppArea webapi_rest
     */
    public function testCustomUrlIsUsed()
    {
        $url = $this->notifier->getPersonalRequestConfigUrl('mytoken');
        self::assertEquals('example.com/mytoken/abc', $url);
    }

    /**
     * @magentoAppArea webapi_rest
     */
    public function testDefaultUrl()
    {
        $url = $this->notifier->getPersonalRequestConfigUrl('mytoken');
        self::assertStringContainsString('backend/tfa/tfa/index/tfat/mytoken', $url);
    }
}
