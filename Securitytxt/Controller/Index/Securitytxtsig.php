<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Securitytxt\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Securitytxt\Model\Securitytxt as SecuritytxtModel;

/**
 * Processes request to security.txt signature file and returns security.txt signature content as result
 */
class Securitytxtsig extends Action implements HttpGetActionInterface
{
    /**
     * @var ResultFactory
     */
    private $resultPageFactory;

    /**
     * @var SecuritytxtModel
     */
    private $securitytxtModel;

    /**
     * @param Context $context
     * @param ResultFactory $resultPageFactory
     * @param SecuritytxtModel $securitytxtModel
     */
    public function __construct(
        Context $context,
        ResultFactory $resultPageFactory,
        SecuritytxtModel $securitytxtModel
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->securitytxtModel = $securitytxtModel;
        parent::__construct($context);
    }

    /**
     * Generates security.txt signature data and returns it as result
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $securitytxtSignature = $this->securitytxtModel->getSecuritytxtsig();
        $result = $this->resultPageFactory->create(ResultFactory::TYPE_RAW);
        $result->setHeader('Content-Type', 'text/plain');
        $result->setContents($securitytxtSignature);
        return $result;
    }
}
