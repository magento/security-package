<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Model\Provider\Engine\U2fKey;

use Magento\Framework\App\ObjectManager;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * @var Session
     */
    private $session;

    protected function setUp(): void
    {
        $this->session = ObjectManager::getInstance()->get(Session::class);
    }

    public function testU2fChallenge()
    {
        self::assertNull($this->session->getU2fChallenge());
        $this->session->setU2fChallenge([123, 456]);
        self::assertSame([123, 456], $this->session->getU2fChallenge());
        $this->session->setU2fChallenge([345, 678]);
        self::assertSame([345, 678], $this->session->getU2fChallenge());
        $this->session->setU2fChallenge(null);
        self::assertNull($this->session->getU2fChallenge());
        $this->session->setU2fChallenge([]);
    }
}
