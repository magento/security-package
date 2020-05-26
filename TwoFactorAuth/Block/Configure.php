<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Block;

use Magento\Backend\Block\Template;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\ProviderInterface;

/**
 * Block with providers list allowing to configure 2FA providers to be used to authorize users.
 *
 * @api
 */
class Configure extends Template
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @param Template\Context $context
     * @param TfaInterface $tfa
     * @param array $data
     */
    public function __construct(Template\Context $context, TfaInterface $tfa, array $data = [])
    {
        parent::__construct($context, $data);
        $this->tfa = $tfa;
    }

    /**
     * Create list of providers for user to choose.
     *
     * @return array
     */
    public function getProvidersList(): array
    {
        $selected = $this->tfa->getForcedProviders();
        $list = [];
        foreach ($this->tfa->getAllEnabledProviders() as $provider) {
            $list[] = [
                'code' => $provider->getCode(),
                'name' => $provider->getName(),
                'icon' => $this->getViewFileUrl($provider->getIcon()),
                'selected' => in_array($provider, $selected, true)
            ];
        }

        return $list;
    }

    /**
     * Get the form's action URL.
     *
     * @return string
     */
    public function getActionUrl(): string
    {
        return $this->getUrl('tfa/tfa/configurepost');
    }
}
