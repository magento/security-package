<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;

/**
 * Revoke 2FA trusted host authorization controller
 *
 * @deprecated Trusted Devices functionality was removed.
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Revoke extends AbstractAction implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @param Context $context
     * @param \Magento\TwoFactorAuth\Api\TrustedManagerInterface $trustedManager
     */
    public function __construct(
        Context $context,
        \Magento\TwoFactorAuth\Api\TrustedManagerInterface $trustedManager
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        trigger_error('Trusted devices are no longer supported', E_USER_DEPRECATED);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Magento_TwoFactorAuth::tfa');
    }
}
