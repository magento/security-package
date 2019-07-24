<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Securitytxt\Model\Config;

use Magento\Config\Model\Config\CommentInterface;

class Signature implements CommentInterface
{
    /**
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue): string
    {
        return "<a href='https://devdocs.magento.com/' target='_blank'>
                    Read instructions on how to generate signature
                </a>";
    }
}