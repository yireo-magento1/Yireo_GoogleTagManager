<?php
/**
 * GoogleTagManager plugin for Magento
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_GoogleTagManager_Block_Order extends Yireo_GoogleTagManager_Block_Default
{
    /**
     * @return array
     */
    public function getItems()
    {
        $data = array();

        foreach($this->getOrder()->getAllItems() as $item) {
        	// Only add composed types once
        	if( $item->getParentItemId() ) {
				continue; 
			}

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

    public function getItemsAsJson()
    {
        $data = $this->getItems();

        return json_encode($data);
    }
}
