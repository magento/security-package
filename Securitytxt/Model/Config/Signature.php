<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Securitytxt\Model\Config;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\Escaper;

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
     * @var Escaper
     */
    private $escaper;

    /**
     * @param Escaper $escaper
     * @param string $instructionLink
     */
    public function __construct(
        Escaper $escaper,
        string $instructionLink = ''
    ) {
        $this->escaper = $escaper;
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
        if ($this->instructionLink === '') {
            return '';
        }
        return sprintf(
            "<a href='%s' target='_blank'>%s</a>",
            $this->escaper->escapeUrl($this->instructionLink),
            __('Read instructions on how to generate signature')
        );
    }
}
