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
            $data[] = array(
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'category' => implode('|', $product->getCategoryIds()),
                'quantity' => $item->getQtyOrdered(),
            );
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
