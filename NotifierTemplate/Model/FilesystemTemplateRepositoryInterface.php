<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Filesystem templates resolver  (Service Provider Interface - SPI)
 *
 * @api
 */
interface FilesystemTemplateRepositoryInterface
{
    /**
     * Get a list of available templates
     * @return array
     */
    public function getList(): array;

    /**
     * Get template file by template ID
     * @param string $templateId
     * @return string
     * @throws NoSuchEntityException
     */
    public function get(string $templateId): string;
}
