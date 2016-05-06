<?php
class Zeo_Actions_Model_Eav_Entity_Attribute_Source_Categories extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
     var $_options;
    public  function getCategoriesTreeView() {
        // Get category collection
        $categories = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSort('path', 'asc')
            ->addFieldToFilter('is_active', array('eq'=>'1'))
            ->load()
            ->toArray();

        // Arrange categories in required array
        $categoryList = array();
        foreach ($categories as $catId => $category) {
            if (isset($category['name'])) {
                $categoryList[] = array(
                    'label' => $category['name'],
                    'level'  =>$category['level'],
                    'value' => $catId
                );
            }
        }
        return $categoryList;
    }
     
     public function  __construct(){
         $categoriesTreeView = $this->getCategoriesTreeView();

         $this->_options = array();

             foreach($categoriesTreeView as $value)
                {
                    $catName    = $value['label'];
                    $catId      = $value['value'];
                    $catLevel    = $value['level'];

                    $space = ' ';
                    for($i=1; $i<$catLevel; $i++){
                        $space = $space."-";
                    }
                    $catName = $space.$catName;
                $this->_options[]=array("label" => $catName,"value" =>  $catId);  
            
                }
      
     }
     public function toOptionArray()
     {
         // Get category collection
         $categories = Mage::getModel('catalog/category')
         ->getCollection()
         ->addAttributeToSelect('name')
         ->addAttributeToSort('path', 'asc')
         ->addFieldToFilter('is_active', array('eq'=>'1'))
         ->load()
         ->toArray();
         
         // Arrange categories in required array
         $categoryList = array("");
         foreach ($categories as $catId => $category) {
             $dash="";
             for($i=2;$i<=$category['level'];$i++){
                 $dash .="--";
             }
             
             if (isset($category['name'])) {
                 $categoryList[] = array(
                     'label' => $dash.$category['name'],
                    // 'level'  =>$category['level'],
                     'value' => $catId
                 );
             }
         }
         return $categoryList;
         
     }
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
			
                array(
                    "label" => Mage::helper("eav")->__("Category1"),
                    "value" =>  1
                ),
                 array(
                    "label" => Mage::helper("eav")->__("Category2"),
                    "value" =>  2
                ),
	
            );
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option["value"]] = $option["label"];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option["value"] == $value) {
                return $option["label"];
            }
        }
        return false;
    }

    /**
     * Retrieve Column(s) for Flat
     *
     * @return array
     */
    public function getFlatColums()
    {
        $columns = array();
        $columns[$this->getAttribute()->getAttributeCode()] = array(
            "type"      => "tinyint(1)",
            "unsigned"  => false,
            "is_null"   => true,
            "default"   => null,
            "extra"     => null
        );

        return $columns;
    }

    /**
     * Retrieve Indexes(s) for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = array();

        $index = "IDX_" . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = array(
            "type"      => "index",
            "fields"    => array($this->getAttribute()->getAttributeCode())
        );

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel("eav/entity_attribute")
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}

			 