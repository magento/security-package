<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Unit\Model\Config\Backend;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\TwoFactorAuth\Api\ProviderInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Model\Config\Backend\ForceProviders;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ForceProvidersTest extends TestCase
{
    /**
     * @var ForceProviders
     */
    private $model;

    /**
     * @var TfaInterface|MockObject
     */
    private $tfa;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->tfa = $this->createMock(TfaInterface::class);

        $this->model = $objectManager->getObject(
            ForceProviders::class,
            [
                'tfa' => $this->tfa
            ]
        );
    }

    /**
     * Check that beforeSave validates values.
     *
     * @return void
     */
    public function testBeforeSaveInvalid(): void
    {
        $this->expectException(\Magento\Framework\Exception\ValidatorException::class);
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
        $provider1 = $this->createMock(ProviderInterface::class);
        $provider1->method('getCode')
            ->willReturn('provider1');
        $provider2 = $this->createMock(ProviderInterface::class);
        $provider2->method('getCode')
            ->willReturn('provider2');
        $provider3 = $this->createMock(ProviderInterface::class);
        $provider3->method('getCode')
            ->willReturn('provider3');
        $this->tfa->method('getAllProviders')
            ->willReturn([$provider1, $provider2, $provider3]);
        $this->model->setValue(['provider1', 'ignoreme', 'provider2']);
        $this->model->beforeSave();
        self::assertSame(['provider1', 'provider2'], $this->model->getValue());
    }
}
