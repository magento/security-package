<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierApi\Test\Unit\Model\AdapterEngine\MessageValidator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use MSP\NotifierApi\Model\AdapterEngine\MessageValidator\Required;
use PHPUnit\Framework\TestCase;

class RequiredTest extends TestCase
{
    /**
     * @var Required
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->subject = (new ObjectManager($this))->getObject(Required::class);
    }

    public function testEmptyValue(): void
    {
        $this->expectException(ValidatorException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute('');
    }

    public function testSpaceOnlyValue(): void
    {
        $this->expectException(ValidatorException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute('    ');
    }

    public function testNonEmptyValue(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute('Some text');
    }
}
