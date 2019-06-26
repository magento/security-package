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

namespace Kalpesh\Securitytxt\Model\Config;

use \Magento\Config\Model\Config\CommentInterface;

class Signature implements CommentInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManagerInterface;

    /**
     * Signature constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
    )
    {
        $this->_storeManagerInterface = $storeManagerInterface;
    }

    /**
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        return "<a href='https://devdocs.magento.com/' target='_blank'>Read instructions on how to generate signature</a>";
    }
}