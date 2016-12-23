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
 * Class Yireo_GoogleTagManager_Block_Quote
 */
class Yireo_GoogleTagManager_Block_Quote extends Yireo_GoogleTagManager_Block_Default
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
     * Return all quote items as array
     *
     * @return string
     */
    public function getItemsAsArray()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $this->getQuote();
        if (empty($quote)) {
            return array();
        }

        $data = array();
        foreach($quote->getAllItems() as $item) {
            /** @var Mage_Sales_Model_Quote_Item $item */

            $product = $item->getProduct();

            $info = array(
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => $this->taxHelper->getPrice($product, $product->getFinalPrice()),
                'quantity' => $item->getQty(),
            );
            $parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
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
     * Return all quote items as JSON
     *
     * @return string
     */
    public function getItemsAsJson()
    {   
        return json_encode($this->getItemsAsArray());
    }
}
