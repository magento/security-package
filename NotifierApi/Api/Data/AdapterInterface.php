<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierApi\Api\Data;

/**
 * TODO
 *
 * @api
 */
interface AdapterInterface
{
    /**
     * Get adapter code.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Get adapter description.
     *
     * @return string
     */
    public function getDescription(): string;
}
