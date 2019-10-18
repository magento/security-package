<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEmailAdapter\Test\Unit\Model\AdapterEngine\ParamsValidator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\NotifierEmailAdapter\Model\AdapterEngine\ParamsValidator\IsEmail;
use PHPUnit\Framework\TestCase;

class IsEmailTest extends TestCase
{
    /**
     * @var IsEmail
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->subject = (new ObjectManager($this))->getObject(
            IsEmail::class,
            [
                'parameterName' => 'testParam',
            ]
        );
    }

    /**
     * @return array
     */
    public function invalidEmailDataProvider(): array
    {
        return [
            ['invalidemail'],
            ['invalidemail@somewhere'],
            ['invalidemail@somewhere:a'],
            ['invalidemail@somewhere,com'],
            ['invalidemail@somewhere..com'],
            ['invalid@email@somewhere.com'],
            ['valid@email.com,invalidemail'],
        ];
    }

    /**
     * @return array
     */
    public function validEmailDataProvider(): array
    {
        return [
            [''],
            ['some@email.com,some2@email.com,some3@email.com'],
            ['my_email@test.com'],
            ['some@email.co.uk'],
            ['some@awesome-email.com']
        ];
    }

    /**
     * @dataProvider invalidEmailDataProvider
     * @param string $email
     * @throws ValidatorException
     */
    public function testShouldTriggerValidatorException(string $email): void
    {
        $this->expectException(ValidatorException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute([
            'testParam' => $email
        ]);
    }

    /**
     * @dataProvider validEmailDataProvider
     * @param string $email
     * @throws ValidatorException
     */
    public function testShouldValidateEmail(string $email): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute([
            'testParam' => $email
        ]);
    }
}
