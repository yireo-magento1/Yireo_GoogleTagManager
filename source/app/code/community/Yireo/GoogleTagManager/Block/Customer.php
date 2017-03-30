<?php
/**
 * GoogleTagManager plugin for Magento
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_GoogleTagManager_Block_Customer
 */
class Yireo_GoogleTagManager_Block_Customer extends Yireo_GoogleTagManager_Block_Default
{
    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        /** @var Mage_Customer_Model_Session $customerSession */
        $customerSession = Mage::getSingleton('customer/session');
        $customer = $customerSession->getCustomer();
        return $customer;
    }

    /**
     * @return Mage_Customer_Model_Group
     */
    public function getCustomerGroup()
    {
        $customer = $this->getCustomer();

        /** @var Mage_Customer_Model_Group $customerGroup */
        $customerGroup = Mage::getSingleton('customer/group');
        $customerGroup->load($customer->getGroupId());

        return $customerGroup;
    }
}
