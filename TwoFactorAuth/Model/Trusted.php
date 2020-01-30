<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\TwoFactorAuth\Api\Data\TrustedInterface;
use Magento\TwoFactorAuth\Api\Data\TrustedInterfaceFactory;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Trusted extends AbstractModel
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TrustedInterfaceFactory
     */
    private $trustedDataFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param DataObjectHelper $dataObjectHelper
     * @param TrustedInterfaceFactory $trustedDataFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataObjectHelper $dataObjectHelper,
        TrustedInterfaceFactory $trustedDataFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->trustedDataFactory = $trustedDataFactory;
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Trusted::class);
    }

    /**
     * Retrieve Trusted model
     *
     * @return TrustedInterface
     */
    public function getDataModel(): TrustedInterface
    {
        $trustedData = $this->getData();

        /** @var TrustedInterface $trustedDataObject */
        $trustedDataObject = $this->trustedDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $trustedDataObject,
            $trustedData,
            TrustedInterface::class
        );
        $trustedDataObject->setId((int) $this->getId());

        return $trustedDataObject;
    }
}
