<?php
class Zeo_Actions_IndexController extends Mage_Core_Controller_Front_Action{

	public function removeAction(){
		//echo "Removed";
		//return;
		
		$sql = "DELETE FROM `core_resource` WHERE `core_resource`.`code` = 'zeo_actions_setup'";
 
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
 
		try{
			$connection->query($sql);
		} catch(Exception $e){
			echo $e->getMessage();
		}
	}
}