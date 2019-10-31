<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace Magento\NotifierEvent\Test\Integration;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierEvent\Model\GetAutomaticTemplateId;
use Magento\NotifierEvent\Model\Rule;
use Magento\NotifierEvent\Model\RuleRepository;
use Magento\NotifierEvent\Model\Throttle;
use Magento\NotifierEvent\Test\Integration\Mock\ConfigureMockTemplateGetter;
use Magento\NotifierEventApi\Model\ThrottleInterface;
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
     * @magentoDataFixture ../../../../app/code/Magento/NotifierEvent/Test/Integration/_files/rules.php
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
