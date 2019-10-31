<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Test\Integration\DatabaseTemplate\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierTemplate\Model\DatabaseTemplate\Validator\ValidateName;
use Magento\NotifierTemplate\Model\DatabaseTemplate;
use PHPUnit\Framework\TestCase;

class ValidateNameTest extends TestCase
{
    /**
     * @var ValidateName
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
        $this->subject = $this->objectManager->get(ValidateName::class);
    }

    /**
     * @return array
     */
    public function invalidDataProvider(): array
    {
        return [
            [
                'templateData' => [
                    'name' => ''
                ],
                'errorMessage' => 'Template name is required'
            ],
            [
                'templateData' => [
                    'name' => '               '
                ],
                'errorMessage' => 'Template name is required'
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
                    'name' => 'test_template'
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
