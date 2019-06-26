<?php
/**
 * This file is part of the Kalpesh_Securitytxt module.
 *
 * @author      Kalpesh Mehta <k@lpe.sh>
 * @copyright   Copyright (c) 2018-2019
 *
 * For full copyright and license information, please check the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalpesh\Securitytxt\Block;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Kalpesh\Securitytxt\Model\Securitytxt as SecuritytxtModel;
use Magento\Framework\App\RequestInterface;

/**
 * Securitytxt Block Class.
 *
 * Prepares base content for security.txt
 *
 * @api
 */
class Securitytxt extends AbstractBlock
{
    /**
     * @var SecuritytxtModel
     */
    private $securitytxt;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param Context $context
     * @param SecuritytxtModel $securitytxt
     * @param RequestInterface $request ,
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        Context $context,
        SecuritytxtModel $securitytxt,
        RequestInterface $request,
        array $data = []
    )
    {
        $this->securitytxt = $securitytxt;
        $this->request = $request;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve content for security.txt file
     *
     * @return string
     */
    protected function _toHtml()
    {
        $identifier = trim($this->request->getPathInfo(), '/');
        if ($identifier === '.well-known/security.txt') {
            return $this->securitytxt->getSecuritytxt();
        } else if ($identifier === '.well-known/security.txt.sig') {
            return $this->securitytxt->getSecuritytxtsig();
        }
    }
}