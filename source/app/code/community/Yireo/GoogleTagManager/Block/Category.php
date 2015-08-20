<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_GoogleTagManager_Block_Category extends Yireo_GoogleTagManager_Block_Default
{
    public function getLoadedProductCollection()
    {
        $productListBlock = Mage::app()->getLayout()->getBlock('product_list');

        if (empty($productListBlock)) {
            return null;
        }

        return $productListBlock->getLoadedProductCollection();
    }
}
