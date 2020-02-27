<?php
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Controller\Adminhtml\Tfa;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TwoFactorAuth\TestFramework\TestCase\AbstractBackendController;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;

/**
 * Testing the controller for the page that redirects user to proper pages depending on 2FA state.
 *
 * @magentoAppArea adminhtml
 */
class IndexTest extends AbstractBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/tfa/tfa/index';

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->tfa = Bootstrap::getObjectManager()->get(TfaInterface::class);
    }

    /**
     * Verify that user is taken to Config Request page when 2FA is not configured.
     *
     * @return void
     */
    public function testNoneConfigured(): void
    {
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('requestconfig'));
    }

    /**
     * Verify that user is taken to provider's configuration page when only personal 2FA is not configured.
     *
     * @return void
     * @magentoConfigFixture default/twofactorauth/general/force_providers google
     */
    public function testUserNotConfigured(): void
    {
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('google'));
    }

    /**
     * Verify that user is taken to configured provider's challenge page.
     *
     * @return void
     * @magentoConfigFixture default/twofactorauth/general/force_providers google
     * @magentoDbIsolation enabled
     */
    public function testConfigured(): void
    {
        //Activating a provider for the user.
        $this->tfa->getProvider(Google::CODE)->activate((int)$this->_session->getUser()->getId());

        $this->dispatch($this->uri);
        //Taken to the provider's challenge page.
        $this->assertRedirect($this->stringContains('google'));
    }
}
