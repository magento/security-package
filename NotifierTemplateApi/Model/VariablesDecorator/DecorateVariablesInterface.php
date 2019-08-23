<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateApi\Model\VariablesDecorator;

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
     * @return void
     */
    public function execute(array $data): void;
}
