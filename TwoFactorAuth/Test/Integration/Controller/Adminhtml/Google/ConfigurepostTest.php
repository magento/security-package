<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Controller\Adminhtml\Google;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\TwoFactorAuth\TestFramework\TestCase\AbstractConfigureBackendController;

/**
 * Test for the configure google 2FA form page processor.
 *
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 */
class ConfigurepostTest extends AbstractConfigureBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/tfa/google/configurepost';

    /**
     * @inheritDoc
     */
    protected $httpMethod = Request::METHOD_POST;

    /**
     * @inheritDoc
     * @magentoConfigFixture default/twofactorauth/general/force_providers google
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
     */
    public function testTokenAccess(): void
    {
        parent::testTokenAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/twofactorauth/general/force_providers google
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
     */
    public function testAclHasAccess()
    {
        parent::testAclHasAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/twofactorauth/general/force_providers google
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
     */
    public function testAclNoAccess()
    {
        parent::testAclNoAccess();
    }
}
