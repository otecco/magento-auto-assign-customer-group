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
 * Adminhtml customers groups grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shopix_AutoAssignCustomerGroup_Block_Adminhtml_Customer_Group_Grid
    extends Mage_Adminhtml_Block_Customer_Group_Grid
{
    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('time', array(
            'header' => Mage::helper('customer')->__('ID'),
            'width' => '50px',
            'align' => 'right',
            'index' => 'customer_group_id',
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('customer')->__('Customer Group'),
            'index' => 'customer_group_code',
        ));

        $this->addColumn('group_name', array(
            'header' => Mage::helper('customer')->__('Group Name'),
            'index' => 'group_name',
        ));

        $this->addColumn('group_title', array(
            'header' => Mage::helper('customer')->__('Group Title'),
            'index' => 'group_title',
        ));

        $this->addColumn('code_pdf', array(
            'header' => Mage::helper('customer')->__('PDF Code'),
            'index' => 'code_pdf',
        ));

        $this->addColumn('code_url', array(
            'header' => Mage::helper('customer')->__('URL Code'),
            'index' => 'code_url',
        ));

        $this->addColumn('sales_first_name', array(
            'header' => Mage::helper('customer')->__('Sales First Name'),
            'index' => 'sales_first_name',
        ));

        $this->addColumn('sales_last_name', array(
            'header' => Mage::helper('customer')->__('Sales Last Name'),
            'index' => 'sales_last_name',
        ));

        $this->addColumn('sales_role', array(
            'header' => Mage::helper('customer')->__('Sales Role'),
            'index' => 'sales_role',
        ));

        $this->addColumn('sales_email', array(
            'header' => Mage::helper('customer')->__('Sales Email'),
            'index' => 'sales_email',
        ));

        $this->addColumn('sales_phone', array(
            'header' => Mage::helper('customer')->__('Sales Phone'),
            'index' => 'sales_phone',
        ));

        $this->addColumn('group_owner', array(
            'header' => Mage::helper('customer')->__('Group Owner'),
            'index' => 'group_owner',
        ));

        $this->sortColumnsByOrder();
        return $this;
    }
}
