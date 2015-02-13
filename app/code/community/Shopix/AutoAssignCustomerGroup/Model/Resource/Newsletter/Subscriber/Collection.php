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
 * Newsletter subscribers collection
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shopix_AutoAssignCustomerGroup_Model_Resource_Newsletter_Subscriber_Collection
    extends Mage_Newsletter_Model_Resource_Subscriber_Collection
{
    /**
     * Constructor
     * Configures collection
     *
     */
    protected function _construct()
    {  
        parent::_construct();
        $this->_map['fields']['customer_group_code'] = 'customer_group_table.customer_group_code';
    }

    /**
     * Adds customer info to select
     *
     * @return Mage_Newsletter_Model_Resource_Subscriber_Collection
     */
    public function showCustomerInfo()
    {
        parent::showCustomerInfo();

        $this->getSelect()
            ->joinLeft(
                array('anonymous_group_table'=>$this->getTable('customer/customer_group')),
                'anonymous_group_table.customer_group_id=main_table.customer_group_id',
                array('anonymous_group_code' => 'customer_group_code')
            )
            ->joinLeft(
                array('customer_table'=>$this->getTable('customer/entity')),
                'customer_table.entity_id=main_table.customer_id',
                array('group_id')
            )
            ->joinLeft(
                array('customer_group_table'=>$this->getTable('customer/customer_group')),
                'customer_group_table.customer_group_id=customer_table.group_id',
                array('customer_group_code' => new Zend_Db_Expr('IFNULL(customer_group_table.customer_group_code, anonymous_group_table.customer_group_code)'))
            );

        return $this;
    }
}
