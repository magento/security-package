<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Model;

use Magento\NotifierApi\Api\Data\AdapterInterface;

class Adapter implements AdapterInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $code
     * @param string $description
     */
    public function __construct(
        string $code,
        string $description
    ) {
        $this->code = $code;
        $this->description = $description;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
