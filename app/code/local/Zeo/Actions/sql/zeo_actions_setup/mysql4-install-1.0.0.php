<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer", "zeo_customer_restrict_cat_ids",  array(
		"type"     => "text",
		"backend"  => "",
		"label"    => "Restrict Categories",
		"input"    => "multiselect",
		"source"   => "actions/eav_entity_attribute_source_categories",
		"visible"  => true,
		"required" => false,
		"default" => "",
		"frontend" => "",
		"unique"     => false,
		"note"       => ""

	));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "zeo_customer_restrict_cat_ids");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
//$used_in_forms[]="checkout_register";
//$used_in_forms[]="customer_account_create";
//$used_in_forms[]="customer_account_edit";
//$used_in_forms[]="adminhtml_checkout";
$attribute->setData("used_in_forms", $used_in_forms)
->setData("is_used_for_customer_segment", true)
->setData("is_system", 0)
->setData("is_user_defined", 1)
->setData("is_visible", 0)
->setData("sort_order", 100)
;
$attribute->save();

// Add Attribute (Is activated)
$installer->addAttribute("customer", "zeo_customer_activated",  array(
    "type"     => "int",
    "backend"  => "",
    "label"    => "Is Activated",
    "input"    => "select",
    "source"   => "eav/entity_attribute_source_boolean",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));
 $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "zeo_customer_activated");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
$resource = Mage::getResourceModel('customer/customer');

// Set activation status for existing customers to true
$select = $installer->getConnection()->select()
    ->from($resource->getEntityTable(), $resource->getEntityIdField());
$customer_ids = $installer->getConnection()->fetchCol($select);

foreach (array_chunk($customer_ids,1000) as $update_ids) {
    $updated_customer_ids = Mage::getResourceModel('actions/customer')->massSetActivationStatus($update_ids, 1);
}
/////////////////////////
$installer->endSetup();
