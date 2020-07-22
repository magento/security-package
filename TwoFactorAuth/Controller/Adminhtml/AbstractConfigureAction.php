<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;

/**
 * Base action class for controllers related to 2FA provider configuration.
 */
abstract class AbstractConfigureAction extends AbstractAction
{
    /**
     * @var HtmlAreaTokenVerifier
     */
    private $tokenVerifier;

    /**
     * @param Context $context
     * @param HtmlAreaTokenVerifier $tokenVerifier
     */
    public function __construct(Context $context, HtmlAreaTokenVerifier $tokenVerifier)
    {
        parent::__construct($context);
        $this->tokenVerifier = $tokenVerifier;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $isAllowed = parent::_isAllowed();
        if ($isAllowed) {
            $isAllowed = $this->tokenVerifier->isConfigTokenProvided();
        }

        return $isAllowed;
    }
}
