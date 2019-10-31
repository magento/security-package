<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAdminUi\Controller\Adminhtml\Channel;

use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Notifier\Model\ChannelFactory;
use Magento\NotifierApi\Model\SerializerInterface;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Notifier::channel';

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var ChannelFactory
     */
    private $channelFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var SerializerInterface
     */
    private $channelParamsSerializer;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param ChannelRepositoryInterface $channelRepository
     * @param SerializerInterface $channelParamsSerializer
     * @param ChannelFactory $channelFactory
     * @param DataObjectHelper $dataObjectHelper
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        Action\Context $context,
        ChannelRepositoryInterface $channelRepository,
        SerializerInterface $channelParamsSerializer,
        ChannelFactory $channelFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct($context);
        $this->channelRepository = $channelRepository;
        $this->channelFactory = $channelFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->channelParamsSerializer = $channelParamsSerializer;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $channelId = (int) $this->getRequest()->getParam('channel_id');

        $request = $this->getRequest();
        $requestData = $request->getParams();

        if (empty($requestData['general'])) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $this->redirectAfterFailure($channelId);
        }

        $channelId = (int) $requestData['general']['channel_id'];
        if (!$channelId && ($adapterCode = $this->getRequest()->getParam('adapter_code'))) {
            $requestData['general']['adapter_code'] = $adapterCode;
        }

        try {
            $channel = $this->save($channelId, $requestData['general']);
            $this->messageManager->addSuccessMessage(__('Channel "%1" saved.', $channel->getName()));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not save channel: %1.', $e->getMessage()));
            return $this->redirectAfterFailure($channelId);
        }

        return $this->redirectAfterSave();
    }

    /**
     * Save channel
     * @param int $channelId
     * @param array $data
     * @return ChannelInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function save(int $channelId, array $data): ChannelInterface
    {
        if ($channelId) {
            $channel = $this->channelRepository->get($channelId);
        } else {
            $channel = $this->channelFactory->create();
        }

        if (!isset($data['configuration'])) {
            $data['configuration'] = [];
        }
        $data['configuration_json'] = $this->channelParamsSerializer->serialize($data['configuration']);

        $this->dataObjectHelper->populateWithArray(
            $channel,
            $data,
            ChannelInterface::class
        );

        $this->channelRepository->save($channel);

        return $channel;
    }

    /**
     * Return a redirect result
     * @param int $channelId
     * @return ResultInterface
     */
    private function redirectAfterFailure(int $channelId): ResultInterface
    {
        $result = $this->resultRedirectFactory->create();

        if (null === $channelId) {
            $result->setPath('*/*/new');
        } else {
            $result->setPath('*/*/edit', ['channel_id' => $channelId]);
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
