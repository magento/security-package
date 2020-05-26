<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Model;

use Magento\Framework\App\ObjectManager;
use Magento\TwoFactorAuth\Model\TfaSession;
use PHPUnit\Framework\TestCase;

class TfaSessionTest extends TestCase
{
    /**
     * @var TfaSession
     */
    private $session;

    protected function setUp(): void
    {
        $this->session = ObjectManager::getInstance()->get(TfaSession::class);
    }

    public function testSkipProvider()
    {
        self::assertSame([], $this->session->getSkippedProviderConfig());
        $this->session->setSkippedProviderConfig(['foo' => true]);
        self::assertSame(['foo' => true], $this->session->getSkippedProviderConfig());
        $this->session->setSkippedProviderConfig(['foo' => true, 'bar' => true]);
        self::assertSame(['foo' => true, 'bar' => true], $this->session->getSkippedProviderConfig());
        $this->session->setSkippedProviderConfig([]);
        self::assertSame([], $this->session->getSkippedProviderConfig());
    }
}
