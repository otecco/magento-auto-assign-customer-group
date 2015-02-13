<?php
 /*
  *  Shopix_AutoAssignCustomerGroup - Magento Auto-Assign Customer Group
  *  Copyright (C) 2015 Shopix Pty Ltd (http://www.shopix.com.au)
  *
  *  This program is free software: you can redistribute it and/or modify
  *  it under the terms of the GNU Affero General Public License as
  *  published by the Free Software Foundation, either version 3 of the
  *  License, or (at your option) any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *  GNU Affero General Public License for more details.
  *
  *  You should have received a copy of the GNU Affero General Public License
  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  */

class Shopix_AutoAssignCustomerGroup_Model_Observer extends Varien_Object
{
    // For One Page Checkout, "register" checkout mode.
    public function coreCopyFieldsetCustomerAccountToQuote($observer) {
        $quote = $observer->getTarget();
	$customer = Mage::getSingleton('customer/session')->getCustomer();

	if (! ($quote->getCheckoutMethod() == 'register'
		 || $customer->getGroupId() == Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID)))
	    return;

        $storeurlparamorderData = unserialize($quote->getStoreurlparamorderData());

        $paramName = Mage::getStoreConfig('autoassigncustomergroup/general/param_name');

        if (empty($storeurlparamorderData) || ! isset($storeurlparamorderData[$paramName]))
            return;

        /* Load the group by code and assign to customer. */
        $group = Mage::getModel('customer/group')->load($storeurlparamorderData[$paramName], 'customer_group_code');
        if (empty($group))
            return;

	if ($quote->getCheckoutMethod() == 'register')
		$quote->setCustomerGroupId($group->getId());
	else {
		$customer->setGroupId($group->getId());
		$customer->save();
	}
    }

    public function customerSaveBefore($observer) {
        try {
            /* Only set customer group if the current is the default one. */
            $customer = $observer->getCustomer();
            if ($customer->getGroupId() != Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID))
                return;

            /* Retrieve group name from Shopix_StoreUrlParamOrder data. */
            $cart = Mage::getSingleton('checkout/cart');
            $quote = $cart->getQuote();
            $storeurlparamorderData = unserialize($quote->getStoreurlparamorderData());

            $paramName = Mage::getStoreConfig('autoassigncustomergroup/general/param_name');

            if (empty($storeurlparamorderData) || ! isset($storeurlparamorderData[$paramName]))
                return;

            /* Load the group by code and assign to customer. */
            $group = Mage::getModel('customer/group')->load($storeurlparamorderData[$paramName], 'customer_group_code');
            if (empty($group))
                return;

            $customer->setGroupId($group->getId());

        } catch (Exception $e) {
            Mage::log("customer_save_before observer failed: " . $e->getMessage());
        }
    }

    /*
     * ALTER TABLE newsletter_subscriber ADD COLUMN customer_group_id SMALLINT(6) DEFAULT NULL COMMENT 'Customer Group Id';
     */
    public function newsletterSubscriberSaveBefore($observer) {
        try {
            $subscriber = $observer->getSubscriber();

            /* Do not change customer group id if set. */
            $existing = $subscriber->getCustomerGroupId();
            if (! empty($existing))
                return;

            $cart = Mage::getSingleton('checkout/cart');
            $quote = $cart->getQuote();
            $storeurlparamorderData = unserialize($quote->getStoreurlparamorderData());
            $paramName = Mage::getStoreConfig('autoassigncustomergroup/general/param_name');

            if (empty($storeurlparamorderData) || ! isset($storeurlparamorderData[$paramName]))
                $group_id = Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
            else {
                /* Load the group by code and assign to customer. */
                $group = Mage::getModel('customer/group')->load($storeurlparamorderData[$paramName], 'customer_group_code');
                if (! empty($group))
                    $group_id = $group->getId();
            }

            /* Only set customer_group_id for guest subscribers. */
            if (! $subscriber->getCustomerId()) {
                $subscriber->setCustomerGroupId($group_id);
                Mage::getSingleton('customer/session')->setData('customer_group_id_from_url_param', $group_id);
            }

        } catch (Exception $e) {
            Mage::log("newsletter_subscriber_save_before observer failed: " . $e->getMessage());
        }
    }

}

