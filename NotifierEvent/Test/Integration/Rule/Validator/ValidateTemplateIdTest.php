<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace Magento\NotifierEvent\Test\Integration\Rule\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Notifier\Test\Integration\Mock\ConfigureMockAdapter;
use Magento\NotifierEvent\Model\GetAutomaticTemplateId;
use Magento\NotifierEvent\Model\Rule\Validator\ValidateTemplateId;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
use PHPUnit\Framework\TestCase;

class ValidateTemplateIdTest extends TestCase
{
    /**
     * @var ValidateTemplateId
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
        ConfigureMockAdapter::execute();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->subject = $this->objectManager->get(ValidateTemplateId::class);
    }

    /**
     * @return array
     */
    public function invalidDataProvider(): array
    {
        return [
            [
                'data' => [
                    'template_id' => ''
                ],
                'errorMessage' => 'Template is required'
            ],
            [
                'data' => [
                    'template_id' => '               '
                ],
                'errorMessage' => 'Template is required'
            ],
            [
                'data' => [
                    'template_id' => 'unknown_template_it'
                ],
                'errorMessage' => 'Invalid or unknown template id unknown_template_it'
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
                'data' => [
                    'template_id' => 'test_generic_template_1'
                ]
            ],
            [
                'data' => [
                    'template_id' => 'event:_default'
                ]
            ],
            [
                'data' => [
                    'template_id' => GetAutomaticTemplateId::AUTOMATIC_TEMPLATE_ID
                ]
            ]
        ];
    }

    /**
     * @param array $data
     * @param string $errorMessage
     * @dataProvider invalidDataProvider
     * @magentoDataFixture ../../../../app/code/Magento/NotifierTemplate/Test/Integration/_files/db_templates.php
     */
    public function testShouldTriggerValidationException(array $data, string $errorMessage): void
    {
        $channel = $this->objectManager->create(
            RuleInterface::class,
            [
                'data' => $data
            ]
        );

        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage($errorMessage);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute($channel);
    }

    /**
     * @param array $data
     * @dataProvider validDataProvider
     * @magentoDataFixture ../../../../app/code/Magento/NotifierTemplate/Test/Integration/_files/db_templates.php
     */
    public function testShouldValidateChannel(array $data): void
    {
        $channel = $this->objectManager->create(
            RuleInterface::class,
            [
                'data' => $data
            ]
        );

        $this->subject->execute($channel);
    }
}
