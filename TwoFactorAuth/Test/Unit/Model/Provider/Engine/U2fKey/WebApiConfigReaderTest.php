<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Unit\Model\Provider\Engine\U2fKey;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey\ConfigReader;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey\WebApiConfigReader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WebApiConfigReaderTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfig;

    /**
     * @var ConfigReader|MockObject
     */
    private $defaultConfigReader;

    /**
     * @var WebApiConfigReader
     */
    private $reader;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->defaultConfigReader = $this->createMock(ConfigReader::class);
        $this->reader = $objectManager->getObject(
            WebApiConfigReader::class,
            [
                'scopeConfig' => $this->scopeConfig,
                'configReader' => $this->defaultConfigReader
            ]
        );
    }

    public function testDomainFromConfig()
    {
        $this->defaultConfigReader
            ->expects($this->never())
            ->method('getDomain');
        $this->scopeConfig
            ->method('getValue')
            ->with(U2fKey::XML_PATH_WEBAPI_DOMAIN)
            ->willReturn('foo');
        $result = $this->reader->getDomain();
        self::assertSame('foo', $result);
    }

    public function testDomainFromDefault()
    {
        $this->defaultConfigReader
            ->method('getDomain')
            ->willReturn('foo');
        $this->scopeConfig
            ->method('getValue')
            ->with(U2fKey::XML_PATH_WEBAPI_DOMAIN)
            ->willReturn(null);
        $result = $this->reader->getDomain();
        self::assertSame('foo', $result);
    }
}
