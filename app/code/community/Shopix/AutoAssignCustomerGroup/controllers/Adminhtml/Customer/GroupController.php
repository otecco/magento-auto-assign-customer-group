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
/**
 * Customer groups controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once('Mage/Adminhtml/controllers/Customer/GroupController.php');
class Shopix_AutoAssignCustomerGroup_Adminhtml_Customer_GroupController
    extends Mage_Adminhtml_Customer_GroupController
{
    /**
     * Create or save customer group.
     */
    public function saveAction()
    {
        $customerGroup = Mage::getModel('customer/group');
        $id = $this->getRequest()->getParam('id');
        if (!is_null($id)) {
            $customerGroup->load((int)$id);
        }

        $taxClass = (int)$this->getRequest()->getParam('tax_class');

        if ($taxClass) {
            try {
                $customerGroupCode = (string)$this->getRequest()->getParam('code');

                if (!empty($customerGroupCode)) {
                    $customerGroup->setCode($customerGroupCode);
                }

                $fields = array('group_name', 'group_title', 'group_owner', 'code_pdf', 'code_url', 
                    'sales_first_name', 'sales_last_name', 'sales_role', 'sales_email', 
                    'sales_phone');
                foreach ($fields as $field) {
                    $value = (string)$this->getRequest()->getParam($field);
                    if (! empty($value))
                        $customerGroup->setData($field, $value);
                }

                $customerGroup->setTaxClassId($taxClass)->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customer')->__('The customer group has been saved.'));
                $this->getResponse()->setRedirect($this->getUrl('*/customer_group'));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCustomerGroupData($customerGroup->getData());
                $this->getResponse()->setRedirect($this->getUrl('*/customer_group/edit', array('id' => $id)));
                return;
            }
        } else {
            $this->_forward('new');
        }
    }
}
