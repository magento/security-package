<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Securitytxt\Model\Config;

use Magento\Config\Model\Config\CommentInterface;

/**
 * Signature field description
 */
class Signature implements CommentInterface
{
    /**
     * @var string
     */
    private $instructionLink;

    /**
     * @param string $instructionLink
     */
    public function __construct(
        string $instructionLink = ''
    ) {
        $this->instructionLink = $instructionLink;
    }

    /**
     * Get comment for signature field of security txt extension.
     *
     * @param string $elementValue
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCommentText($elementValue): string
    {
        return "<a href='{$this->instructionLink}' target='_blank'>
                    Read instructions on how to generate signature
                </a>";
    }
}
