<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Securitytxt\ViewModel;

use Magento\Securitytxt\Model\Securitytxt as SecuritytxtModel;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Securitytxt Block Class.
 *
 * Prepares base content for security.txt
 *
 * @api
 */
class Securitytxt implements ArgumentInterface
{
    /**
     * @var SecuritytxtModel
     */
    private $securitytxt;

    /**
     * Securitytxt constructor.
     * @param SecuritytxtModel $securitytxt
     */
    public function __construct(
        SecuritytxtModel $securitytxt
    ) {
        $this->securitytxt = $securitytxt;
    }

    /**
     * @return string
     */
    public function getSecuritytxt(): string
    {
        return $this->securitytxt->getSecuritytxt();
    }

    /**
     * @return string
     */
    public function getSecuritytxtsig(): string
    {
        return $this->securitytxt->getSecuritytxtsig();
    }
}