<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Unit\Model\Provider\Engine;

use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class U2fKeyTest extends TestCase
{
    /**
     * @var U2fKey
     */
    private $model;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(U2fKey::class);
    }

    /**
     * Check that the provider is available based on configuration.
     *
     * @return void
     */
    public function testIsEnabled(): void
    {
        $this->assertTrue($this->model->isEnabled());
    }
}
