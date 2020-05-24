<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Block\Provider\Authy;

use Magento\Backend\Block\Template;
use Magento\TwoFactorAuth\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;

/**
 * @api
 */
class Configure extends Template
{
    /**
     * @var CountryCollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * @param Template\Context $context
     * @param CountryCollectionFactory $countryCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CountryCollectionFactory $countryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * Get a country list
     *
     * @return array
     */
    private function getCountriesList()
    {
        return $this->countryCollectionFactory->create()->addOrder('name', 'asc')->getItems();
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $countries = [];
        foreach ($this->getCountriesList() as $country) {
            $countries[] = [
                'dial_code' => $country->getDialCode(),
                'name' => $country->getName(),
            ];
        }

        $this->jsLayout['components']['tfa-configure']['children']['register']['configurePostUrl'] =
            $this->getUrl('*/*/configurepost');

        $this->jsLayout['components']['tfa-configure']['children']['verify']['verifyPostUrl'] =
            $this->getUrl('*/*/configureverifypost');

        $this->jsLayout['components']['tfa-configure']['children']['verify']['successUrl'] =
            $this->getUrl($this->_urlBuilder->getStartupPageUrl());

        $this->jsLayout['components']['tfa-configure']['children']['register']['countries'] =
            $countries;

        return parent::getJsLayout();
    }
}
