<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Provider\Failure\RedirectUrl;

use Magento\ReCaptcha\Model\Provider\Failure\RedirectUrlProviderInterface;
use Magento\Framework\App\Response\RedirectInterface;

/**
 * @inheritDoc
 */
class RefererProvider implements RedirectUrlProviderInterface
{
    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @param RedirectInterface $redirect
     */
    public function __construct(
        RedirectInterface $redirect
    ) {
        $this->redirect = $redirect;
    }

    /**
     * Get redirection URL
     * @return string
     */
    public function execute(): string
    {
        return $this->redirect->getRedirectUrl();
    }
}
