<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventAdminUi\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierEventApi\Api\RuleRepositoryInterface;
use Magento\NotifierEventApi\Api\Data\RuleInterface;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_NotifierEvent::rule';

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
            $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $result->setActiveMenu('Magento_NotifierEvent::rule');
            $result->addBreadcrumb(__('Edit Rule'), __('Edit Rule'));
            $result->getConfig()
                ->getTitle()
                ->prepend(__('Edit Rule: %name', ['name' => $rule->getName()]));
        } catch (NoSuchEntityException $e) {
            $result = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(
                __('Rule with id "%value" does not exist.', ['value' => $ruleId])
            );
            $result->setPath('*/*');
        }

        return $result;
    }
}
