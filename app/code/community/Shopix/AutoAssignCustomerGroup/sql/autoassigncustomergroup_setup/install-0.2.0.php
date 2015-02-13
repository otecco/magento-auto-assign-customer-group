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

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer  = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$column_names = array('group_name', 'group_title', 'group_owner', 'code_pdf', 'code_url',
    'sales_first_name', 'sales_last_name', 'sales_role', 'sales_email', 'sales_phone');

foreach ($column_names as $column) {
    $connection->addColumn($installer->getTable('customer/customer_group'), $column, array(
            'nullable' => true,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => "Wine Direct specific ($column)",
        )
    );
}

$connection->addColumn($installer->getTable('newsletter/subscriber'), 'customer_group_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'unsigned' => true,
        'nullable' => true,
        'default' => NULL,
        'comment' => 'Customer group ID',
    )
);

$installer->endSetup();
