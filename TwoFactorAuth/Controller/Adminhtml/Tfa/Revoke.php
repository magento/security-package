<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TwoFactorAuth\Api\TrustedManagerInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;

/**
 * Revoke 2FA trusted host authorization controller
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Revoke extends AbstractAction implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var TrustedManagerInterface
     */
    private $trustedManager;

    public function __construct(
        Action\Context $context,
        TrustedManagerInterface $trustedManager
    ) {
        parent::__construct($context);
        $this->trustedManager = $trustedManager;
    }

    /**
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $tokenId = (int) $this->getRequest()->getParam('id');
        $userId = (int) $this->getRequest()->getParam('user_id');
        $this->trustedManager->revokeTrustedDevice($tokenId);

        $this->messageManager->addSuccessMessage(__('Device authorization revoked'));
        return $this->_redirect('adminhtml/user/edit', ['user_id' => $userId]);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Magento_TwoFactorAuth::tfa');
    }
}
