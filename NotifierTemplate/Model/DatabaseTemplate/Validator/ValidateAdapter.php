<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Model\DatabaseTemplate\Validator;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use MSP\NotifierApi\Api\AdaptersPoolInterface;
use MSP\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use MSP\NotifierTemplateApi\Model\DatabaseTemplate\Validator\ValidateDatabaseTemplateInterface;

class ValidateAdapter implements ValidateDatabaseTemplateInterface
{
    /**
     * @var AdaptersPoolInterface
     */
    private $adapterRepository;

    /**
     * @param AdaptersPoolInterface $adapterRepository
     */
    public function __construct(
        AdaptersPoolInterface $adapterRepository
    ) {
        $this->adapterRepository = $adapterRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(DatabaseTemplateInterface $template): bool
    {
        if (!trim($template->getAdapterCode())) {
            return true;
        }

        try {
            $this->adapterRepository->getAdapterByCode($template->getAdapterCode());
        } catch (NoSuchEntityException $e) {
            throw new ValidatorException(__('Invalid adapter code'));
        }

        return true;
    }
}
