<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Ui\Component\Form\User;

use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\User\Model\ResourceModel\User\CollectionFactory;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Model\Config\Source\EnabledProvider;

class DataProvider extends AbstractDataProvider
{
    private $loadedData = null;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var EnabledProvider
     */
    private $enabledProvider;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param CollectionFactory $collectionFactory
     * @param EnabledProvider $enabledProvider
     * @param UserConfigManagerInterface $userConfigManager
     * @param UrlInterface $url
     * @param TfaInterface $tfa
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        EnabledProvider $enabledProvider,
        UserConfigManagerInterface $userConfigManager,
        UrlInterface $url,
        TfaInterface $tfa,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->tfa = $tfa;
        $this->enabledProvider = $enabledProvider;
        $this->userConfigManager = $userConfigManager;
        $this->url = $url;
    }

    /**
     * Get a list of forced providers
     *
     * @return array
     */
    private function getForcedProviders()
    {
        $names = [];
        $forcedProviders = $this->tfa->getForcedProviders();
        if (!empty($forcedProviders)) {
            foreach ($forcedProviders as $forcedProvider) {
                $names[] = $forcedProvider->getName();
            }
        }

        return $names;
    }

    /**
     * Get reset provider urls
     *
     * @param User $user
     * @return array
     */
    private function getResetProviderUrls(User $user)
    {
        $providers = $this->tfa->getAllEnabledProviders();

        $resetProviders = [];
        foreach ($providers as $provider) {
            if ($provider->isConfigured((int) $user->getId()) && $provider->isResetAllowed()) {
                $resetProviders[] = [
                    'value' => $provider->getCode(),
                    'label' => __('Reset %1', $provider->getName()),
                    'url' => $this->url->getUrl('tfa/tfa/reset', [
                        'id' => (int) $user->getId(),
                        'provider' => $provider->getCode(),
                    ]),
                ];
            }
        }

        return $resetProviders;
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        $meta['base_fieldset']['children']['tfa_providers']['arguments']['data']['config']['forced_providers'] =
            $this->getForcedProviders();
        $meta['base_fieldset']['children']['tfa_providers']['arguments']['data']['config']['enabled_providers'] =
            $this->enabledProvider->toOptionArray();

        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if ($this->loadedData === null) {
            $this->loadedData = [];
            $items = $this->collection->getItems();

            /** @var User $user */
            foreach ($items as $user) {
                $providerCodes = $this->userConfigManager->getProvidersCodes((int) $user->getId());
                $resetProviders = $this->getResetProviderUrls($user);

                $data = [
                    'reset_providers' => $resetProviders,
                    'tfa_providers' => $providerCodes,
                ];
                $this->loadedData[(int) $user->getId()] = $data;
            }
        }

        return $this->loadedData;
    }
}
