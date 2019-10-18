<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Test\Unit\Model\AdapterEngine\ParamsValidator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\NotifierApi\Model\AdapterEngine\ParamsValidator\Required;
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
        $this->subject = (new ObjectManager($this))->getObject(
            Required::class,
            [
                'requiredParams' => [
                    'requiredParam' => true,
                ],
            ]
        );
    }

    public function testMissingRequiredParameter(): void
    {
        $this->expectException(ValidatorException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute([
            'someOtherNonRequiredParam' => 'some value'
        ]);
    }

    public function testRequiredParameter(): void
    {
        $this->expectException(ValidatorException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute([
            'someOtherNonRequiredParam' => 'some value',
            'requiredParam' => 'some value',
        ]);
    }
}
