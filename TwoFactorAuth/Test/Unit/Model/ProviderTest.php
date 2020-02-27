<?php
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\TwoFactorAuth\Model\Provider;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    /**
     * @var Provider
     */
    private $model;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            Provider::class,
            [
                'code' => 'test',
                'name' => 'test',
                'icon' => 'test.jpg',
                'configureAction' =>'configure',
                'authAction' => 'auth'
            ]
        );
    }

    /**
     * Check that trusted devices functionality is disabled
     *
     * @return void
     */
    public function testTrustedDevicesEnabled(): void
    {
        $this->assertFalse($this->model->isTrustedDevicesAllowed());
    }
}
