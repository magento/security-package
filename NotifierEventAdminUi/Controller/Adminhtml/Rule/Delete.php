<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEventAdminUi\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use MSP\NotifierEventApi\Api\RuleRepositoryInterface;
use MSP\NotifierEventApi\Api\Data\RuleInterface;

class Delete extends Action implements HttpGetActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MSP_NotifierEvent::rule';

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @param Action\Context $context
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        Action\Context $context,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $ruleId = (int) $this->getRequest()->getParam(RuleInterface::ID);

        try {
            $rule = $this->ruleRepository->get($ruleId);
            $this->ruleRepository->deleteById((int) $rule->getId());
            $this->messageManager->addSuccessMessage(__('Rule "%1" deleted.', $rule->getName()));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not delete rule: %1.', $e->getMessage()));
        }

        $result = $this->resultRedirectFactory->create();
        $result->setPath('*/*/index');

        return $result;
    }
}
