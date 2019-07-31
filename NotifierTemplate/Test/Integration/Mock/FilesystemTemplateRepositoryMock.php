<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Test\Integration\Mock;

use MSP\NotifierTemplate\Model\FilesystemTemplateRepository;

class FilesystemTemplateRepositoryMock extends FilesystemTemplateRepository
{
    /**
     * @inheritDoc
     */
    public function get(string $templateId): string
    {
        return 'MSP_NotifierTemplate::' . $templateId . '.html';
    }
}
