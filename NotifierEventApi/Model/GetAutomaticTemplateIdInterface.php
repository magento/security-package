<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEventApi\Model;

use MSP\NotifierEventApi\Api\Data\RuleInterface;

/**
 * Automatic template selector  (Service Provider Interface - SPI)
 *
 * @api
 */
interface GetAutomaticTemplateIdInterface
{
    /**
     * Widcard to specify the automatic template selection while firing a rule
     */
    public const AUTOMATIC_TEMPLATE_ID = '*';

    /**
     * Return a template ID
     *
     * @param RuleInterface $rule
     * @param string $eventName
     * @param array $data
     * @return string
     */
    public function execute(RuleInterface $rule, string $eventName, array $data = []): string;
}
