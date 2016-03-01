<?php
class Zeo_Actions_Helper_Data extends Mage_Core_Helper_Abstract{
	static public function IsActive(){
		return !(boolean)Mage::getStoreConfig('advanced/modules_disable_output/Zeo_Actions');
	}
	
	static public function HideAddToCart(){
		if(Mage::helper('actions')->IsActive()){
			$disable_add_to_cart=Mage::getStoreConfig('zeo_actions_setting/cart_button/disable_add_to_cart_button')=="1"? true: false;
			
			if($disable_add_to_cart==false)
			return false;
				
			$restrict_groups=Mage::getStoreConfig('zeo_actions_setting/cart_button/hide_add_to_cart_button_groups');
			$restrict_groups=trim($restrict_groups);
			$restrict_groups=trim($restrict_groups,",");
			if($restrict_groups==""){
				return $disable_add_to_cart;
			}else{
			
				$restrict_groups_array=explode(",",$restrict_groups);
				$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
			
				if($disable_add_to_cart &&in_array($groupId,$restrict_groups_array))
				return true;
				else
				return false;	
			}		
		}
		else
		return false;
	}
	static public function DisableCustomerAccountCreate(){
		if(Mage::helper('actions')->IsActive())
		return Mage::getStoreConfig('zeo_actions_setting/frontend/disable_customer_account_create')=="1"? true: false;
		else
		return false;
	}
	static public function CheckCustomerActivation(){
		if(Mage::helper('actions')->IsActive())
		return Mage::getStoreConfig('zeo_actions_setting/customer_activation/check_customer_activation')=="1"? true: false;
		else
		return false;
	}
	static public function check_customer_activation_message(){
		return Mage::getStoreConfig('zeo_actions_setting/customer_activation/check_customer_activation_message');
	}
	static public function check_customer_activation_create_message(){
		return Mage::getStoreConfig('zeo_actions_setting/customer_activation/check_customer_activation_create_message');
	}
	static public function customer_activation_alert_admin(){
		if(Mage::helper('actions')->CheckCustomerActivation())
			return Mage::getStoreConfig('zeo_actions_setting/customer_activation/alert_admin')=="1"? true: false;
		else
			return false;
	}
	static public function checkAllowedCustomerCategories(){
		if(Mage::helper('actions')->IsActive())
		return Mage::getStoreConfig('zeo_actions_setting/cart_button/check_allowed_customer_categories_add_to_cart')=="1"? true: false;
		else
		return false;
	}
	static public function notify_customer(){
		if(Mage::helper('actions')->CheckCustomerActivation())
		return Mage::getStoreConfig('zeo_actions_setting/customer_activation/notify_customer')=="1"? true: false;
		else
		return false;
	}
	static public function default_customer_value(){
		if(Mage::helper('actions')->CheckCustomerActivation())
			return Mage::getStoreConfig('zeo_actions_setting/customer_activation/default_customer_value')=="1"? "1": "0";
			else
				return "1";
	}
	public function sendAdminNotificationEmail(Mage_Customer_Model_Customer $customer){
		$name = Mage::getStoreConfig ( 'trans_email/ident_support/name' );
		$email = Mage::getStoreConfig ( 'trans_email/ident_support/email' );
		$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'action_customer_create_account_email_template' );
		$admin_email=Mage::getStoreConfig('zeo_actions_setting/customer_activation/admin_email');
		if($admin_email!=""){
			$emailTemplate->setSenderName ( $name )->setSenderEmail ( $email );
			$emailTemplate ->setTemplateSubject(Mage::helper('core') ->__("New Customer Registerd"));
			$emailTemplateVariables = array ();
			$emailTemplateVariables ['customer'] = $customer;
			$store_id=$this->getCustomerStoreId($customer);
			$emailTemplateVariables ['store'] = Mage::getModel('core/store')->load($store_id);;
			$processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
			$result = $emailTemplate->send ( $admin_email, $admin_email, $emailTemplateVariables );
		}
	}
	public function sendCustomerActivationEmail(Mage_Customer_Model_Customer $customer){
		if(Mage::helper('actions')->notify_customer()){
			
			$name = Mage::getStoreConfig ( 'trans_email/ident_support/name' );
			$email = Mage::getStoreConfig ( 'trans_email/ident_support/email' );

			if($customer->getEmail()!=""){
			    $store_id=$this->getCustomerStoreId($customer);
			    $local = Mage::getStoreConfig('general/locale/code', $store_id);
			    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'action_notify_customer_activate_email_template' ,$local);
			     
			    
				$emailTemplate->setSenderName ( $name )->setSenderEmail ( $email );
				
				$activate_subject="";
				$activate_subject=Mage::getStoreConfig('zeo_actions_setting/customer_activation/email_activate_subject',$store_id);
				if($activate_subject=="")
				    $activate_subject=Mage::helper('core') ->__("Your Account Has been activated");
				
				$emailTemplate ->setTemplateSubject($activate_subject);
				
				$emailTemplateVariables = array ();
				$emailTemplateVariables ['customer'] = $customer;
				$emailTemplateVariables ['store'] = Mage::getModel('core/store')->load($store_id);;
				$processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
				$result = $emailTemplate->send ( $customer->getEmail(), $customer->getEmail(), $emailTemplateVariables );
			}
		
		}
	}
	public function sendCustomerDeActivationEmail(Mage_Customer_Model_Customer $customer){
		if(Mage::helper('actions')->notify_customer()){
			$name = Mage::getStoreConfig ( 'trans_email/ident_support/name' );
			$email = Mage::getStoreConfig ( 'trans_email/ident_support/email' );
		
			if($customer->getEmail()!=""){
			    $store_id=$this->getCustomerStoreId($customer);
			    $local = Mage::getStoreConfig('general/locale/code', $store_id);
			    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'action_notify_customer_deactivate_email_template' ,$local);
			     
				$emailTemplate->setSenderName ( $name )->setSenderEmail ( $email );
				
				$deactivate_subject="";
				$deactivate_subject=Mage::getStoreConfig('zeo_actions_setting/customer_activation/email_deactivate_subject',$store_id);
				if($deactivate_subject=="")
				    $deactivate_subject=Mage::helper('core') ->__("Your Account Has been deactivated");
				
				$emailTemplate ->setTemplateSubject($deactivate_subject);
				$emailTemplateVariables = array ();
				$emailTemplateVariables ['customer'] = $customer;
				
				$emailTemplateVariables ['store'] = Mage::getModel('core/store')->load($store_id);;
				$processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
				$result = $emailTemplate->send ( $customer->getEmail(), $customer->getEmail(), $emailTemplateVariables );
			}
		}
	}
	public function getCustomerStoreId(Mage_Customer_Model_Customer $customer){
		/*
		* Only set in Adminhtml UI
		*/
		if(!($storeId = $customer->getSendemailStoreId())){
			/*
			* store_id might be zero if the account was created in the admin interface
			*/
			$storeId = $customer->getStoreId();
			if(!$storeId && $customer->getWebsiteId()){
				/*
				* Use the default store groups store of the customers website
				*/
				if($store = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()){
					$storeId = $store->getId();
				}
			}
			// In case the website_id is not yet set on the customer, and the
			// current store is a frontend store, use the current store ID
			if(!$storeId && !Mage::app()->getStore()->isAdmin()){
				$storeId = Mage::app()->getStore()->getId();
			}
		}
		return $storeId;
	}
	static public function DeleteOrder(){
		if(Mage::helper('actions')->IsActive())
		return Mage::getStoreConfig('zeo_actions_setting/adminhtml/delete_order')=="1"? true: false;
		else
		return false;
	}
}
