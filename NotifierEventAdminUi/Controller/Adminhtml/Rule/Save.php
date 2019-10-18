<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventAdminUi\Controller\Adminhtml\Rule;

use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierEvent\Model\RuleFactory;
use Magento\NotifierApi\Model\SerializerInterface;
use Magento\NotifierEventApi\Api\RuleRepositoryInterface;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
use Magento\NotifierEventApi\Model\GetAutomaticTemplateIdInterface;

class Save extends Action implements HttpPostActionInterface
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
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param Action\Context $context
     * @param RuleRepositoryInterface $ruleRepository
     * @param ChannelRepositoryInterface $channelRepository
     * @param SerializerInterface $serializer
     * @param RuleFactory $ruleFactory
     * @param DataObjectHelper $dataObjectHelper
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        Action\Context $context,
        RuleRepositoryInterface $ruleRepository,
        ChannelRepositoryInterface $channelRepository,
        SerializerInterface $serializer,
        RuleFactory $ruleFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
        $this->ruleFactory = $ruleFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->serializer = $serializer;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $ruleId = (int) $this->getRequest()->getParam(RuleInterface::ID);

        $request = $this->getRequest();
        $requestData = $request->getParams();

        if (empty($requestData['general']) || !$request->isPost()) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $this->redirectAfterFailure($ruleId);
        }

        $ruleId = (int) $requestData['general'][RuleInterface::ID];
        try {
            $rule = $this->save($ruleId, $requestData['general']);
            $this->messageManager->addSuccessMessage(__('Rule "%1" saved.', $rule->getName()));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not save rule: %1.', $e->getMessage()));
            return $this->redirectAfterFailure($ruleId);
        }

        return $this->redirectAfterSave();
    }

    /**
     * Save rule
     * @param int $ruleId
     * @param array $data
     * @return RuleInterface
     * @throws NoSuchEntityException
     */
    private function save(int $ruleId, array $data): RuleInterface
    {
        if ($ruleId) {
            $rule = $this->ruleRepository->get($ruleId);
        } else {
            $rule = $this->ruleFactory->create();
        }

        $data['events'] = $this->serializer->serialize(
            array_unique(preg_split('/[^\w_]+/', strtolower($data['events'])))
        );

        // Filter only existing channels
        $channels = [];
        foreach ($data['channels_codes'] as $channelCode) {
            try {
                $channel = $this->channelRepository->getByCode($channelCode);
                $channels[] = $channel->getCode();
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        $data['channels_codes'] = $this->serializer->serialize($channels);

        if ($data['template_id_auto']) {
            $data['template_id'] = GetAutomaticTemplateIdInterface::AUTOMATIC_TEMPLATE_ID;
        }

        $this->dataObjectHelper->populateWithArray(
            $rule,
            $data,
            RuleInterface::class
        );

        $this->ruleRepository->save($rule);

        return $rule;
    }

    /**
     * Return a redirect result
     * @param int $ruleId
     * @return ResultInterface
     */
    private function redirectAfterFailure(int $ruleId): ResultInterface
    {
        $result = $this->resultRedirectFactory->create();

        if (null === $ruleId) {
            $result->setPath('*/*/new');
        } else {
            $result->setPath('*/*/edit', [RuleInterface::ID => $ruleId]);
        }

        return $result;
    }

    /**
     * Return a redirect result after a successful save
     * @return ResultInterface
     */
    private function redirectAfterSave(): ResultInterface
    {
        $result = $this->resultRedirectFactory->create();
        $result->setPath('*/*/index');

        return $result;
    }
}
