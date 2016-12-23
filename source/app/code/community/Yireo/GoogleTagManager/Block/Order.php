<?php
/**
 * GoogleTagManager plugin for Magento
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_GoogleTagManager_Block_Order
 */
class Yireo_GoogleTagManager_Block_Order extends Yireo_GoogleTagManager_Block_Default
{
    /**
     * @var $taxHelper Mage_Tax_Helper_Data
     */
    protected $taxHelper;
    
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->taxHelper = Mage::helper('tax');

        parent::_construct();
    }
    
    /**
     * Return all items as array
     *
     * @return array
     */
    public function getItems()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->getOrder();
        if (empty($order)) {
            return array();
        }

        $data = array();

        foreach($order->getAllItems() as $item) {

            /** @var Mage_Sales_Model_Order_Item $item */

        	// Only add composed types once
        	if( $item->getParentItemId() ) {
				continue; 
			}

            /** @var Mage_Catalog_Model_Product $product */
            $product = $item->getProduct();
            $info = array(
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => $this->taxHelper->getPrice($product, $product->getFinalPrice()),
                'quantity' => $item->getQtyOrdered(),
            );
            $parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($item->getId());
            if(!empty($parent_ids)) {
                $parent_collection = Mage::getResourceModel('catalog/product_collection')
                    ->addFieldToFilter('entity_id', array('in'=>$parent_ids))
                    ->addAttributeToSelect('sku');
                $parent_skus = $parent_collection->getColumnValues('sku');
                $info['parentId'] = implode(',', $parent_ids);
                $info['parentSku'] = implode(',', $parent_skus);
            }
            $data[] = $info;
        }

        return $data;
    }

    /**
     * Return all items as JSON
     *
     * @return string
     */
    public function getItemsAsJson()
    {
        $data = $this->getItems();

        return json_encode($data);
    }
}
