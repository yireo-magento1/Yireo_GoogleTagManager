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
    public function getItemsAsJson()
    {
        $data = array();
        foreach($this->getOrder()->getAllItems() as $item) {
            $data[] = array(
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQtyOrdered(),
            );
        }
        return json_encode($data);
    }
}
