<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Test\Integration\Mock;

use Magento\Framework\Module\Dir\Reader;

class ModuleDirReaderMock extends Reader
{
    /**
     * @inheritDoc
     */
    public function getModuleDir($type, $moduleName)
    {
        return __DIR__ . '/../_files';
    }
}
