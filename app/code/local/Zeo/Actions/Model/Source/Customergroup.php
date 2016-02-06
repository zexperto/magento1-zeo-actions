<?php
class Zeo_Actions_Model_Source_Customergroup extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
	public function getAllOptions()
	{
		return $this->toOptionArray();
	}
    public function toOptionArray()
    {
       $collection = Mage::getModel('customer/group')->getCollection();

        $result = array();
        foreach ($collection as $group) {
            $result[] = array('value'=>$group['customer_group_id'], 'label'=>$group['customer_group_code']);
        }
		array_unshift($result, array('value'=>'', 'label'=> ''));
		

        return $result;
    }

	 

}