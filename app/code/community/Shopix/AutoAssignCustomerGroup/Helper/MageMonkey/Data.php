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
 * Mage Monkey default helper
 *
 * @category   Ebizmarts
 * @package    Ebizmarts_MageMonkey
 * @author     Ebizmarts Team <info@ebizmarts.com>
 */
class Shopix_AutoAssignCustomerGroup_Helper_MageMonkey_Data extends Ebizmarts_MageMonkey_Helper_Data
{
	/**
	 * Return Merge Fields mapped to Magento attributes
	 *
	 * @param object $customer
	 * @param bool $includeEmail
	 * @param integer $websiteId
	 * @return array
	 */
	public function getMergeVars($customer, $includeEmail = FALSE, $websiteId = NULL)
	{
		$merge_vars   = array();
        $maps         = $this->getMergeMaps($customer->getStoreId());

		if(!$maps){
			return;
		}

		$request = Mage::app()->getRequest();

		//Add Customer data to Subscriber if is Newsletter_Subscriber is Customer
		if($customer->getCustomerId()){
			$customer->addData(Mage::getModel('customer/customer')->load($customer->getCustomerId())
									->toArray());
		}

                if ($customer->getId())
                        $groupId = (int)$customer->getData('group_id');
                else { // Anonymous subscriber
                        $groupId = (int)Mage::getSingleton('customer/session')->getData('customer_group_id_from_url_param');
                }
                $customerGroup = Mage::getModel('customer/group')->load($groupId);

		foreach($maps as $map){

			$customAtt = $map['magento'];
			$chimpTag  = $map['mailchimp'];

			if($chimpTag && $customAtt){

				$key = strtoupper($chimpTag);

				switch ($customAtt) {
					case 'gender':
							$val = (int)$customer->getData(strtolower($customAtt));
							if($val == 1){
								$merge_vars[$key] = 'Male';
							}elseif($val == 2){
								$merge_vars[$key] = 'Female';
							}
						break;
					case 'dob':
							$dob = (string)$customer->getData(strtolower($customAtt));
							if($dob){
								$merge_vars[$key] = (substr($dob, 5, 2) . '/' . substr($dob, 8, 2));
							}
						break;
					case 'billing_address':
					case 'shipping_address':

						$addr = explode('_', $customAtt);
						$address = $customer->{'getPrimary'.ucfirst($addr[0]).'Address'}();
						if($address){
							$merge_vars[$key] = array(
																	'addr1'   => $address->getStreet(1),
														   			'addr2'   => $address->getStreet(2),
															   		'city'    => $address->getCity(),
															   		'state'   => (!$address->getRegion() ? $address->getCity() : $address->getRegion()),
															   		'zip'     => $address->getPostcode(),
															   		'country' => $address->getCountryId(),
													);
							$company = $address->getCompany();
						}

						break;
					case 'date_of_purchase':

						$last_order = Mage::getResourceModel('sales/order_collection')
                        	->addFieldToFilter('customer_email', $customer->getEmail())
                        	->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                        	->setOrder('created_at', 'desc')
                        	->getFirstItem();
	                    if ( $last_order->getId() ){
	                    	$merge_vars[$key] = Mage::helper('core')->formatDate($last_order->getCreatedAt());
	                    }

						break;
					case 'ee_customer_balance':

						$merge_vars[$key] = '';

						if($this->isEnterprise() && $customer->getId()){

							$_customer = Mage::getModel('customer/customer')->load($customer->getId());
							if($_customer->getId()){
								if (Mage::app()->getStore()->isAdmin()) {
									$websiteId = is_null($websiteId) ? Mage::app()->getStore()->getWebsiteId() : $websiteId;
								}

								$balance = Mage::getModel('enterprise_customerbalance/balance')
										  ->setWebsiteId($websiteId)
										  ->setCustomerId($_customer->getId())
										  ->loadByCustomer();

								$merge_vars[$key] = $balance->getAmount();
							}

						}

						break;
					case 'group_id':
					case 'customer_group':
							$grp = Mage::helper('customer')->getGroups()->toOptionHash();
							if($groupId == 0){
								$merge_vars[$key] = 'NOT LOGGED IN';
							}else{
								$merge_vars[$key] = $grp[$groupId];
							}
						break;
                                        case 'group_name':
                                        case 'group_title':
                                        case 'group_owner':
                                            $merge_vars[$key] = $customerGroup->getData($customAtt);
                                            break;
                                        case 'group_code_pdf':
                                        case 'group_code_url':
                                        case 'group_sales_first_name':
                                        case 'group_sales_last_name':
                                        case 'group_sales_role':
                                        case 'group_sales_email':
                                        case 'group_sales_phone':
                                            $merge_vars[$key] = $customerGroup->getData(str_replace('group_', '', $customAtt));
                                            break;
					case 'company':
						if($company){
							$merge_vars[$key] = $company;
						}
						break;
					default:
						if( ($value = (string)$customer->getData(strtolower($customAtt)))
							OR ($value = (string)$request->getPost(strtolower($customAtt))) ){
							$merge_vars[$key] = $value;
						}
						break;
				}

			}
		}

		//GUEST
		if( !$customer->getId() && (!$request->getPost('firstname') || !$request->getPost('lastname'))){
			$guestFirstName = $this->config('guest_name', $customer->getStoreId());
			$guestLastName  = $this->config('guest_lastname', $customer->getStoreId());

			if($guestFirstName){
				$merge_vars['FNAME'] = $guestFirstName;
			}
			if($guestLastName){
				$merge_vars['LNAME'] = $guestLastName;
			}
		}
		//GUEST

		if($includeEmail){
			$merge_vars['EMAIL'] = $customer->getEmail();
		}

		$groups = $customer->getListGroups();
		$groupings = array();

		if(is_array($groups) && count($groups)){
			foreach($groups as $groupId => $grupoptions){
				$groupings[] = array(
									 'id' => $groupId,
								     'groups' => (is_array($grupoptions) ? implode(', ', $grupoptions) : $grupoptions)
								    );
			}
		}

		$merge_vars['GROUPINGS'] = $groupings;

		//magemonkey_mergevars_after
		$blank = new Varien_Object;
		Mage::dispatchEvent('magemonkey_mergevars_after',
            					array('vars' => $merge_vars, 'customer' => $customer, 'newvars' => $blank));
		if($blank->hasData()){
			$merge_vars = array_merge($merge_vars, $blank->toArray());
		}
		//magemonkey_mergevars_after

		return $merge_vars;
	}

}

