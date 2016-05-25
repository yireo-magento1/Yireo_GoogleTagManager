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

            $data[] = array(
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => Mage::helper('tax')->getPrice($product, $product->getFinalPrice()),
                'quantity' => $item->getQty(),
            );
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
