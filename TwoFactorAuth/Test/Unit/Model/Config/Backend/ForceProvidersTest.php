<?php
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Unit\Model\Config\Backend;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\TwoFactorAuth\Model\Config\Backend\ForceProviders;
use PHPUnit\Framework\TestCase;

class ForceProvidersTest extends TestCase
{
    /**
     * @var ForceProviders
     */
    private $model;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(ForceProviders::class);
    }

    /**
     * Check that beforeSave validates values.
     *
     * @return void
     * @expectedException \Magento\Framework\Exception\ValidatorException
     */
    public function testBeforeSaveInvalid(): void
    {
        $this->model->setValue('');
        $this->model->beforeSave();
    }

    /**
     * Check that beforeSave validates values.
     *
     * @return void
     */
    public function testBeforeSaveValid(): void
    {
        $this->model->setValue('provider1, provider2');
        $this->model->beforeSave();
    }
}
