<?php
/**
 * This file is part of the Kalpesh_Securitytxt module.
 *
 * @author      Kalpesh Mehta <k@lpe.sh>
 * @copyright   Copyright (c) 2018-2019
 *
 * For full copyright and license information, please check the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalpesh\Securitytxt\Model\Config\Backend;

use Magento\Framework\Validator\Exception as ValidatorException;
use Kalpesh\Securitytxt\Model\Config;

class Validate extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this|\Magento\Framework\App\Config\Value
     * @throws ValidatorException
     */
    public function validateBeforeSave()
    {
        $sectionPathStr = explode('/', $this->getPath());
        $sectionName = reset($sectionPathStr);

        if ($sectionName == Config::XML_SECURITYTXT_MODULE &&
            $this->getData('group_id') == 'contact_information') {

            $dataGroup = $this->getData()['groups'];
            $contactInfoFields = $dataGroup['contact_information']['fields'];
            $otherInfoFields = $dataGroup['other_information']['fields'];
            $isEnabled = (bool)$dataGroup['general']['fields']['enabled']['value'];
            $contactEmail = $contactInfoFields['email']['value'];
            $contactPhone = $contactInfoFields['phone']['value'];
            $contactWebpage = $contactInfoFields['contact_page']['value'];
            $encryption = $otherInfoFields['encryption']['value'];
            $acknowledgements = $otherInfoFields['acknowledgements']['value'];
            $hiring = $otherInfoFields['hiring']['value'];
            $policy = $otherInfoFields['policy']['value'];

            if ($isEnabled) {
                if ($contactEmail == '' && $contactPhone == '' && $contactWebpage == '') {
                    throw new ValidatorException(__('At least one contact information is required.'));
                }
            }

            if ($contactEmail != '') {
                if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidatorException(
                        __('Contact Information: Email validation failed. Please enter in correct format.')
                    );
                }
            }

            if ($contactPhone != '') {
                if (!$this->validatePhone($contactPhone)) {
                    throw new ValidatorException(
                        __('Contact Information: Phone number validation failed. Please enter in correct format.')
                    );
                }
            }

            if ($contactWebpage != '') {
                if (!$this->validateUrl($contactWebpage)) {
                    throw new ValidatorException(
                        __('Contact Information: Contact Page URL should be in correct format and must start with HTTPS.')
                    );
                }
            }

            if ($encryption != '') {
                if (!$this->validateUrl($encryption)) {
                    throw new ValidatorException(
                        __('Other Information: Encryption URL should be in correct format and must start with HTTPS.')
                    );
                }
            }

            if ($acknowledgements != '') {
                if (!$this->validateUrl($acknowledgements)) {
                    throw new ValidatorException(
                        __('Other Information: Acknowledgements URL should be in correct format and must start with HTTPS.')
                    );
                }
            }

            if ($hiring != '') {
                if (!$this->validateUrl($hiring)) {
                    throw new ValidatorException(
                        __('Other Information: Hiring URL should be in correct format and must start with HTTPS.')
                    );
                }
            }

            if ($policy != '') {
                if (!$this->validateUrl($policy)) {
                    throw new ValidatorException(
                        __('Other Information: Policy URL should be in correct format and must start with HTTPS.')
                    );
                }
            }

        }
        parent::validateBeforeSave();
        return $this;
    }

    /**
     * @param $phone
     * @return bool
     */
    protected function validatePhone(string $phone)
    {
        $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
        $phone_to_check = str_replace("-", "", $filtered_phone_number);
        if (strlen($phone_to_check) < 10 || strlen($phone_to_check) > 14) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $url
     * @return bool
     */
    protected function validateUrl(string $url)
    {
        if (preg_match('|^https://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)) {
            return true;
        } else {
            return false;
        }
    }
}