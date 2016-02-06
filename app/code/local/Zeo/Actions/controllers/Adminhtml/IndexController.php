<?php
class Zeo_Actions_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action{
	public function DeleteOrderAction(){
		
		if( Mage::helper('actions')->DeleteOrder())
		if($orderId = $this->getRequest()->getParam('order_id')){
			try{
				$order = Mage::getModel('sales/order')->load($orderId);
				//$order->delete()
				//  ->save();

				$this->_getSession()->addSuccess(
					$this->__('Order was successfully deleted.')
				);
			} catch(Mage_Core_Exception $e){
				$this->_getSession()->addError($e->getMessage());
			} catch(Exception $e){
				$this->_getSession()->addError($this->__('Unable to delete order.'));
			}
           
			Mage::app()->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/", array()));
           
		}
	}
	public function massDeleteOrderAction(){
		if( Mage::helper('actions')->DeleteOrder()){
			
			$orderIds = $this->getRequest()->getPost('order_ids', array());
			$countDeleteOrder = 0;

			foreach($orderIds as $orderId){
				$order = Mage::getModel('sales/order')->load($orderId);
				$order->delete()
				->save();
				$countDeleteOrder++;
			}

			$countNonDeleteOrder = count($orderIds) - $countDeleteOrder;

			if($countNonDeleteOrder){
				if($countDeleteOrder){
					$this->_getSession()->addError($this->__('%s order(s) were not deleted.', $countNonDeleteOrder));
					$this->_getSession()->addError($this->__('%s order(s) were not deleted.', $countNonDeleteOrder));
				} else{
					$this->_getSession()->addError($this->__('No order(s) were deleted.'));
				}
			}
			if($countDeleteOrder){
				$this->_getSession()->addSuccess($this->__('%s order(s) have been deleted.', $countDeleteOrder));
			}

			Mage::app()->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/", array()));
     
		}   
	}
    
	public function massActivteCustomerAction(){
		if( Mage::helper('actions')->CheckCustomerActivation()){
			$customersIds = $this->getRequest()->getParam('customer');
			if(!is_array($customersIds)){
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
			} else{
				try{
					$customer = Mage::getModel('customer/customer');
					foreach($customersIds as $customerId){
						$customer->reset()
						->load($customerId)
						->setZeoIsActivated("1")->save();
					}
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('adminhtml')->__('Total of %d record(s) were activated.', count($customersIds))
					);
				} catch(Exception $e){
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
			}

			Mage::app()->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("adminhtml/customer/", array()));
		}
			
	}
	public function massDeActivteCustomerAction(){
		if( Mage::helper('actions')->CheckCustomerActivation()){
			$customersIds = $this->getRequest()->getParam('customer');
			if(!is_array($customersIds)){
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
			} else{
				try{
					$customer = Mage::getModel('customer/customer');
					foreach($customersIds as $customerId){
						$customer->reset()
						->load($customerId)
						->setZeoIsActivated("0")->save();
					}
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('adminhtml')->__('Total of %d record(s) were de activated.', count($customersIds))
					);
				} catch(Exception $e){
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
			}

			Mage::app()->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("adminhtml/customer/", array()));
		}
			
	}
    
}