<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Test\Integration;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Notifier\Model\IsEnabled;
use PHPUnit\Framework\TestCase;

class IsEnabledTest extends TestCase
{
    /**
     * @var IsEnabled
     */
    private $subject;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->subject = $this->objectManager->get(IsEnabled::class);
    }

    /**
     * @magentoAdminConfigFixture msp_notifier/general/enabled 1
     */
    public function testShouldBeEnabled(): void
    {
        $this->assertTrue($this->subject->execute());
    }

    /**
     * @magentoAdminConfigFixture msp_notifier/general/enabled 0
     */
    public function testShouldBeDisabled(): void
    {
        $this->assertFalse($this->subject->execute());
    }
}
