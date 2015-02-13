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
 * Adminhtml customer groups edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shopix_AutoAssignCustomerGroup_Block_Adminhtml_Customer_Group_Edit_Form
    extends Mage_Adminhtml_Block_Customer_Group_Edit_Form
{
    /**
     * Prepare form for render
     */
    protected function _prepareLayout()
    {
        Mage_Adminhtml_Block_Widget_Form::_prepareLayout();

        $form = new Varien_Data_Form();
        $customerGroup = Mage::registry('current_group');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('customer')->__('Group Information')));

        $validateClass = sprintf('required-entry validate-length maximum-length-%d',
            Mage_Customer_Model_Group::GROUP_CODE_MAX_LENGTH);
        $name = $fieldset->addField('customer_group_code', 'text',
            array(
                'name'  => 'code',
                'label' => Mage::helper('customer')->__('Customer Group'),
                'title' => Mage::helper('customer')->__('Customer Group'),
                'note'  => Mage::helper('customer')->__('Maximum length must be less then %s symbols', Mage_Customer_Model_Group::GROUP_CODE_MAX_LENGTH),
                'class' => $validateClass,
                'required' => true,
            )
        );

        if ($customerGroup->getId()==0 && $customerGroup->getCustomerGroupCode() ) {
            $name->setDisabled(true);
        }

        $fieldset->addField('tax_class_id', 'select',
            array(
                'name'  => 'tax_class',
                'label' => Mage::helper('customer')->__('Tax Class'),
                'title' => Mage::helper('customer')->__('Tax Class'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Mage::getSingleton('tax/class_source_customer')->toOptionArray()
            )
        );

        $fieldset = $form->addFieldset('wine_direct_fieldset', array('legend'=>Mage::helper('customer')->__('Wine Direct Information')));

        $fieldset->addField('group_name', 'text',
            array(
                'name'  => 'group_name',
                'label' => Mage::helper('customer')->__('Group Name'),
                'title' => Mage::helper('customer')->__('Group Name'),
            )
        );

        $fieldset->addField('group_title', 'text',
            array(
                'name'  => 'group_title',
                'label' => Mage::helper('customer')->__('Group Title'),
                'title' => Mage::helper('customer')->__('Group Title'),
            )
        );

        $fieldset->addField('group_owner', 'text',
            array(
                'name'  => 'group_owner',
                'label' => Mage::helper('customer')->__('Group Owner'),
                'title' => Mage::helper('customer')->__('Group Owner'),
            )
        );

        $fieldset->addField('code_pdf', 'text',
            array(
                'name'  => 'code_pdf',
                'label' => Mage::helper('customer')->__('PDF Code'),
                'title' => Mage::helper('customer')->__('PDF Code'),
            )
        );

        $fieldset->addField('code_url', 'text',
            array(
                'name'  => 'code_url',
                'label' => Mage::helper('customer')->__('URL Code'),
                'title' => Mage::helper('customer')->__('URL Code'),
            )
        );

        $fieldset = $form->addFieldset('sales_fieldset', array('legend'=>Mage::helper('customer')->__('Sales Person Information')));

        $fieldset->addField('sales_first_name', 'text',
            array(
                'name'  => 'sales_first_name',
                'label' => Mage::helper('customer')->__('First Name'),
                'title' => Mage::helper('customer')->__('First Name'),
            )
        );

        $fieldset->addField('sales_last_name', 'text',
            array(
                'name'  => 'sales_last_name',
                'label' => Mage::helper('customer')->__('Last Name'),
                'title' => Mage::helper('customer')->__('Last Name'),
            )
        );

        $fieldset->addField('sales_role', 'text',
            array(
                'name'  => 'sales_role',
                'label' => Mage::helper('customer')->__('Role'),
                'title' => Mage::helper('customer')->__('Role'),
            )
        );

        $fieldset->addField('sales_email', 'text',
            array(
                'name'  => 'sales_email',
                'label' => Mage::helper('customer')->__('Email Address'),
                'title' => Mage::helper('customer')->__('Email Address'),
                'class' => 'validate-email',
            )
        );

        $fieldset->addField('sales_phone', 'text',
            array(
                'name'  => 'sales_phone',
                'label' => Mage::helper('customer')->__('Phone Number'),
                'title' => Mage::helper('customer')->__('Phone Number'),
            )
        );

        if (!is_null($customerGroup->getId())) {
            // If edit add id
            $form->addField('id', 'hidden',
                array(
                    'name'  => 'id',
                    'value' => $customerGroup->getId(),
                )
            );
        }

        if( Mage::getSingleton('adminhtml/session')->getCustomerGroupData() ) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getCustomerGroupData());
            Mage::getSingleton('adminhtml/session')->setCustomerGroupData(null);
        } else {
            $form->addValues($customerGroup->getData());
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $this->setForm($form);
    }
}

