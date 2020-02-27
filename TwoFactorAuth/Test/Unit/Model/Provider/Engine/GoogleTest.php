<?php
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Unit\Model\Provider\Engine;

use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class GoogleTest extends TestCase
{
    /**
     * @var Google
     */
    private $model;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(Google::class);
    }

    /**
     * Check that trusted devices functionality is disabled.
     *
     * @return void
     */
    public function testIsTrustedDevicesAllowed(): void
    {
        $this->assertFalse($this->model->isTrustedDevicesAllowed());
    }

    /**
     * Check that the provider is available based on configuration.
     *
     * @return void
     */
    public function testIsEnabled(): void {
        $this->assertTrue($this->model->isEnabled());
    }
}
