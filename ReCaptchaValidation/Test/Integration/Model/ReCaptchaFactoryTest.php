<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\ReCaptchaValidation\Test\Integration\Model;

use Magento\ReCaptchaValidation\Model\ReCaptchaFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod\CurlPost;

class ReCaptchaFactoryTest extends TestCase
{
    public function testCreatedReCaptchaVerifiesOverCurl(): void
    {
        $reCaptcha = Bootstrap::getObjectManager()->get(ReCaptchaFactory::class)
            ->create(['secret' => 'test-secret']);

        $requestMethod = new \ReflectionProperty(ReCaptcha::class, 'requestMethod');

        self::assertInstanceOf(CurlPost::class, $requestMethod->getValue($reCaptcha));
    }
}
