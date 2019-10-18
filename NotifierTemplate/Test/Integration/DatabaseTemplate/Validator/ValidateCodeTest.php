<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Test\Integration\DatabaseTemplate\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierTemplate\Model\DatabaseTemplate\Validator\ValidateCode;
use Magento\NotifierTemplate\Model\DatabaseTemplate;
use PHPUnit\Framework\TestCase;

class ValidateCodeTest extends TestCase
{
    /**
     * @var ValidateCode
     */
    private $subject;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->subject = $this->objectManager->get(ValidateCode::class);
    }

    /**
     * @return array
     */
    public function invalidDataProvider(): array
    {
        return [
            [
                'templateData' => [
                    'code' => ''
                ],
                'errorMessage' => 'Template identifier is required'
            ],
            [
                'templateData' => [
                    'code' => '               '
                ],
                'errorMessage' => 'Template identifier is required'
            ],
            [
                'templateData' => [
                    'code' => 'Some#Invalid code'
                ],
                'errorMessage' => 'Invalid template identifier: Only alphanumeric chars + columns'
            ]
        ];
    }

    /**
     * @return array
     */
    public function validDataProvider(): array
    {
        return [
            [
                'templateData' => [
                    'code' => 'test_template'
                ]
            ],
            [
                'templateData' => [
                    'code' => 'fake:test_template'
                ]
            ]
        ];
    }

    /**
     * @param array $templateData
     * @param string $errorMessage
     * @dataProvider invalidDataProvider
     */
    public function testShouldTriggerValidationException(array $templateData, string $errorMessage): void
    {
        $channel = $this->objectManager->create(
            DatabaseTemplate::class,
            [
                'data' => $templateData
            ]
        );

        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage($errorMessage);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute($channel);
    }

    /**
     * @param array $templateData
     * @dataProvider validDataProvider
     */
    public function testShouldValidate(array $templateData): void
    {
        $channel = $this->objectManager->create(
            DatabaseTemplate::class,
            [
                'data' => $templateData
            ]
        );

        $this->subject->execute($channel);
    }
}
