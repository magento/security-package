<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace MSP\NotifierEvent\Test\Integration;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use MSP\NotifierEvent\Model\GetAutomaticTemplateId;
use MSP\NotifierEvent\Model\Rule;
use MSP\NotifierEvent\Model\RuleRepository;
use MSP\NotifierEvent\Model\Throttle;
use MSP\NotifierEvent\Test\Integration\Mock\ConfigureMockTemplateGetter;
use MSP\NotifierEventApi\Model\ThrottleInterface;
use PHPUnit\Framework\TestCase;

class ThrottleTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Throttle
     */
    private $subject;

    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->subject = $this->objectManager->get(Throttle::class);
        $this->ruleRepository = $this->objectManager->get(RuleRepository::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/NotifierEvent/Test/Integration/_files/rules.php
     */
    public function testShouldThrottle(): void
    {
        /** @var Rule $rule */
        $rule = current($this->ruleRepository->getList()->getItems());

        for ($i = 0; $i < $rule->getThrottleLimit(); $i++) {
            $this->assertTrue($this->subject->execute($rule));
        }

        $this->assertFalse($this->subject->execute($rule));
    }
}
