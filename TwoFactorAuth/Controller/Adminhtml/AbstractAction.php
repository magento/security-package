<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\App\RequestInterface;

/**
 * Abstraction for 2FA controllers
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractAction extends Action
{
    /**
     * @inheritDoc
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_isAllowed()) {
            $this->_response->setStatusHeader(403, '1.1', 'Forbidden');
            return $this->_redirect('*/auth/login');
        }

        return parent::dispatch($request);
    }
}
