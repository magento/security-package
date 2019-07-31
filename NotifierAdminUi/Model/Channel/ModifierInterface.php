<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierAdminUi\Model\Channel;

/**
 * @api
 */
interface ModifierInterface extends \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    /**
     * Return adapter code for this modifier
     * @return string
     */
    public function getAdapterCode(): string;
}
