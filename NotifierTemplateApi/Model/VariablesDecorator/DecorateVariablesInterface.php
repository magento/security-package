<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplateApi\Model\VariablesDecorator;

/**
 * Variable decorator interface - SPI
 *
 * @api
 */
interface DecorateVariablesInterface
{

    /**
     * Decorate array with variables
     * @param array $data
     * @return array
     */
    public function execute(array $data): array;
}
