<?php
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Controller\Adminhtml\U2f;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\TwoFactorAuth\TestFramework\TestCase\AbstractConfigureBackendController;

/**
 * Test for the configure U2F 2FA form processor.
 *
 * @magentoAppArea adminhtml
 */
class ConfigurepostTest extends AbstractConfigureBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/tfa/u2f/configurepost';

    /**
     * @inheritDoc
     */
    protected $httpMethod = Request::METHOD_POST;

    /**
     * @inheritDoc
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     */
    public function testTokenAccess(): void
    {
        parent::testTokenAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     */
    public function testAclHasAccess()
    {
        parent::testAclHasAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/twofactorauth/general/force_providers u2fkey
     */
    public function testAclNoAccess()
    {
        parent::testAclNoAccess();
    }
}
