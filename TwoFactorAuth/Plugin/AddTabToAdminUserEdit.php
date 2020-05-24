<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Plugin;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\User\Block\User\Edit\Tabs;

/**
 * Add 2FA tab to admin user edit in backend
 */
class AddTabToAdminUserEdit
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param TfaInterface $tfa
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        TfaInterface $tfa,
        AuthorizationInterface $authorization
    ) {
        $this->tfa = $tfa;
        $this->authorization = $authorization;
    }

    /**
     * Check if tab should be displayed
     *
     * @param Tabs $subject
     * @throws LocalizedException
     */
    public function beforeToHtml(Tabs $subject)
    {
        if (empty($this->tfa->getAllEnabledProviders()) ||
            !$this->authorization->isAllowed('Magento_TwoFactorAuth::tfa')
        ) {
            return;
        }

        $tfaForm = $subject->getLayout()->renderElement('tfa_edit_user_form');

        $subject->addTabAfter(
            'twofactorauth',
            [
                'label' => __('2FA'),
                'title' => __('2FA'),
                'content' => $tfaForm,
                'active' => true
            ],
            'roles_section'
        );
    }
}
