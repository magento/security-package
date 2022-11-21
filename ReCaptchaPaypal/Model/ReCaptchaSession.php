<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaPaypal\Model;

use Magento\Framework\Session\SessionManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Saves the date and time the reCaptcha was verified
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class ReCaptchaSession
{
    private const PAYPAL_PAYFLOWPRO_RECAPTCHA = 'paypal_payflowpro_recaptcha';
    private const TIMEOUT = 120;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $timezone;

    /**
     * @var SessionManager
     */
    private SessionManager $transparentSession;

    /**
     * @var SessionManager
     */
    private SessionManager $checkoutSession;

    /**
     * @param TimezoneInterface $timezone
     * @param SessionManager $transparentSession
     * @param SessionManager $checkoutSession
     */
    public function __construct(
        TimezoneInterface $timezone,
        SessionManager $transparentSession,
        SessionManager $checkoutSession,
    ) {
        $this->timezone = $timezone;
        $this->transparentSession = $transparentSession;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Saves quote_id and datetime the reCaptcha was verified in session
     *
     * @return bool
     */
    public function save(): bool
    {
        $result = false;
        if ($this->checkoutSession->getQuote()) {
            $this->transparentSession->setData(
                self::PAYPAL_PAYFLOWPRO_RECAPTCHA,
                [
                    'quote_id' => $this->checkoutSession->getQuote()->getId(),
                    'verified_at' => $this->timezone->date()->getTimestamp(),
                ]
            );
            $result = true;
        }
        return $result;
    }

    /**
     * Checks whether the time since reCaptcha was verified is not more than the timeout
     *
     * @param int $quoteId
     * @return bool
     */
    public function isValid(int $quoteId): bool
    {
        $result = false;
        $data = $this->transparentSession->getData(self::PAYPAL_PAYFLOWPRO_RECAPTCHA) ?? [];
        if (isset($data['quote_id'])
            && (int) $data['quote_id'] === $quoteId
            && ($data['verified_at'] + self::TIMEOUT) >= $this->timezone->date()->getTimestamp()
        ) {
            $this->transparentSession->unsetData(self::PAYPAL_PAYFLOWPRO_RECAPTCHA);
            $result = true;
        }
        return $result;
    }
}
