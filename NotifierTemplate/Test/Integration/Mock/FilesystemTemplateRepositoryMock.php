<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Test\Integration\Mock;

use Magento\NotifierTemplate\Model\FilesystemTemplateRepository;

class FilesystemTemplateRepositoryMock extends FilesystemTemplateRepository
{
    /**
     * @inheritDoc
     */
    public function get(string $templateId): string
    {
        return 'Magento_NotifierTemplate::' . $templateId . '.html';
    }
}
