<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Unit\Model\Provider\Engine\U2fKey;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey\ConfigReader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigReaderTest extends TestCase
{
    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManager;

    /**
     * @var ConfigReader
     */
    private $reader;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->reader = $objectManager->getObject(
            ConfigReader::class,
            [
                'storeManager' => $this->storeManager
            ]
        );
    }

    public function testGetValidDomain()
    {
        $store = $this->createMock(Store::class);
        $store->method('getBaseUrl')
            ->willReturn('https://domain.com/');
        $this->storeManager
            ->method('getStore')
            ->with(Store::ADMIN_CODE)
            ->willReturn($store);
        $result = $this->reader->getDomain();
        self::assertSame('domain.com', $result);
    }

    public function testGetInvalidDomain()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Could not determine domain name.');
        $store = $this->createMock(Store::class);
        $store->method('getBaseUrl')
            ->willReturn('foo');
        $this->storeManager
            ->method('getStore')
            ->with(Store::ADMIN_CODE)
            ->willReturn($store);
        $this->reader->getDomain();
    }
}
