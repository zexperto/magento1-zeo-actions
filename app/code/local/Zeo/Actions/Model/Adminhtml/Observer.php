<?php
class Zeo_Actions_Model_Adminhtml_Observer extends Varien_Event_Observer{
	public function addActionColumn(Varien_Event_Observer $observer){

		$block = $observer->getEvent()->getBlock();
		$this->_block = $block;

		if($block->getType() == 'adminhtml/sales_order_grid'
			&& Mage::helper('actions')->DeleteOrder()){
          
			$actions = array();

			if(true){
				$actions[] = array(
					'caption' => Mage::helper('sales')->__('Delete'),
					'url' => array('base' => 'actions/adminhtml_index/DeleteOrder'),
					'confirm' => Mage::helper('sales')->__('Are your sure your want to delete this order and to erase all linked data ? '),
					'field' => 'order_id'
				);

				$block->addColumnAfter('Delete', array(
						'header' => Mage::helper('sales')->__('Delete'),
						'width' => '50px',
						'type' => 'action',
						'getter' => 'getId',
						'actions' => $actions,
						'filter' => false,
						'sortable' => false,
						'is_system' => true,
					), 'status');
			}
		}

		return $observer;
	}
	public function addMassAction(Varien_Event_Observer $observer){
		$block = $observer->getEvent()->getBlock();
		$this->_block = $block;
		if(get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'){
			// add delete mass action to customer grid
			if( Mage::helper('actions')->DeleteOrder()&& $block->getRequest()->getControllerName() == 'sales_order'){
           
				$block->addItem('delete_order', array(
						'label' => Mage::helper('sales')->__('Delete'),
						'url' => $block->getUrl('actions/adminhtml_index/massDeleteOrder'),
					));
			}
			// add activate /de activate mass action to customer grid
			if( Mage::helper('actions')->CheckCustomerActivation()&& $block->getRequest()->getControllerName() == 'customer'){
           
				$block->addItem('zeo_activate_customer', array(
						'label' => Mage::helper('sales')->__('Activate'),
						'url' => $block->getUrl('actions/adminhtml_index/massActivteCustomer'),
					));
				$block->addItem('zeo_deactivate_customer', array(
						'label' => Mage::helper('sales')->__('De Activate'),
						'url' => $block->getUrl('actions/adminhtml_index/massDeActivteCustomer'),
					));
			}
		}
        
      
	}	
}
