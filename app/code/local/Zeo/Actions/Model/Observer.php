<?php
class Zeo_Actions_Model_Observer{
	
	public function afterCollectionLoad(Varien_Event_Observer $observer){
		$collection = $observer->getCollection();
		if(!isset($collection)){
			return;
		}
		
		if($collection instanceof Mage_Catalog_Model_Resource_Product_Collection){        
			if(Mage::helper('actions')->HideAddToCart()){
				foreach($collection as $product){
					$product->setIsSalable(false);
				}	
			}
		}
	}
	public function customerLogin($observer){
		$helper = Mage::helper('actions');
		if(!$helper->CheckCustomerActivation()){
			return;
		}

		if($this->_isApiRequest()){
			return;
		}

		$customer = $observer->getEvent()->getCustomer();
		$session = Mage::getSingleton('customer/session');
		if(!$customer->getZeoCustomerActivated()){
			/*
			* Fake the old logout() method without deleting the session and all messages
			*/
			$session->setCustomer(Mage::getModel('customer/customer'))
			->setId(null)
			->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
		
			if($this->_checkRequestRoute('customer', 'account', 'createpost')){
				/*
				* If this is a regular registration, simply display message
				*/
				$custom_mesage= $helper->check_customer_activation_create_message();
				$message="Please wait for your account to be activated";
				if($custom_mesage=="")
				$custom_mesage=$message;
						
				$session->addNotice($custom_mesage);
			} else{
				/*
				* All other types of login
				*/
				$custom_message=$helper->check_customer_activation_message();
				$message="This account is not activated";
				if($custom_mesage=="")
				$custom_mesage=$message;
				Mage::throwException($custom_mesage);
			}
		}
	} 
	public function AfterSaveCustomer($observer){
		$oCustomer=$observer->getCustomer();
		$origin_data=$oCustomer->getOrigData("zeo_customer_activated");
		$new_data=$oCustomer->getData("zeo_customer_activated");
		
		if($origin_data!=$new_data){
			if($new_data=="1")
				Mage::helper('actions')->sendCustomerActivationEmail($oCustomer);
			if($new_data=="0")
				Mage::helper('actions')->sendCustomerDeActivationEmail($oCustomer);
		}
	}
	
	public function afterCustomerRegister($observer){
		$helper = Mage::helper('actions');
		$customer = $observer->getEvent()->getCustomer();
		$customer->setZeoCustomerActivated(Mage::helper('actions')->default_customer_value())->save();		
		if($helper->customer_activation_alert_admin()){
			$helper->sendAdminNotificationEmail($customer);
		}
		
	}
    	
	/**
	* Return true if the request is made via the api
	*
	* @return boolean
	*/
	protected function _isApiRequest(){
		return Mage::app()->getRequest()->getModuleName() === 'api';
	}

	/**
	* Check the current module, controller and action against the given values.
	*
	* @param string $module
	* @param string $controller
	* @param string $action
	* @return bool
	*/
	protected function _checkRequestRoute($module, $controller, $action){
		$req = Mage::app()->getRequest();
		if(strtolower($req->getModuleName()) == $module
			&& strtolower($req->getControllerName()) == $controller
			&& strtolower($req->getActionName()) == $action
		){
			return true;
		}
		return false;
	}
	public function functionBeforeAddProductToCart($observer){
		$this->check_customer_allowed_categories($observer);
	}
	
	private function check_customer_allowed_categories($observer){
		if(Mage::helper('actions')->checkAllowedCustomerCategories()){
			if(Mage::getSingleton('customer/session')->isLoggedIn()){
				$product_categories = $observer->getProduct()->getCategoryIds();
				$oCustomer = Mage::getSingleton('customer/session')->getCustomer();
				$restrict_categories=explode(",", $oCustomer->getZeoCustomerRestrictCatIds());
		
				$result_categories=array_intersect($restrict_categories,$product_categories);
				if(count($result_categories)>0){
					$frontend_config=Mage::getStoreConfig('actions_setting/cart_button');
					$custom_mesage=$frontend_config["check_allowed_customer_categories_add_to_cart_message"];
			
					$message="You are not allowed to add this product";
					if($custom_mesage=="")
					$custom_mesage=$message;
					Mage::throwException($custom_mesage);
				}
			}else{
				Mage::throwException(Mage::helper('core')->__("Please log in."));
			}
		}
		
	}
	
	public function catalog_product_is_salable_after($observer){
		$product = $observer->getProduct();
		//$salable = $observer->getSalable();
		if(Mage::helper('actions')->HideAddToCart())
		$product->setIsSalable(false);
		return $this;
	}
	public function customer_account_create_action($observer){
		$action_name=$observer->getEvent()->getControllerAction()->getFullActionName();
		if(Mage::helper('actions')->DisableCustomerAccountCreate()){
			if(($action_name == 'customer_account_create') || ($action_name=="customer_account_createpost")){
				$customer_account_create_message="Customer Account Creation is Disabled, Please Contact the Administrator to create new account.";
				$customer_account_create_custom_message= Mage::getStoreConfig('actions_setting/frontend/disable_customer_account_create_message');
				if($customer_account_create_custom_message=="")
				$customer_account_create_custom_message=$customer_account_create_message;
					
				Mage::getSingleton('core/session')->addError($customer_account_create_custom_message);
				$loginUrl = Mage::helper('customer')->getLoginUrl();
				//Mage::app()->getFrontController()->getResponse()->setRedirect($loginUrl)->sendResponse();
				Mage::app()->getResponse()->setRedirect($loginUrl)->sendResponse(); 
				//Mage::app()->getResponse()->setRedirct($loginUrl);
				exit;
			}
		}
	}
}
