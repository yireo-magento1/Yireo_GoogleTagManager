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
 * Class Yireo_GoogleTagManager_Block_Product
 */
class Yireo_GoogleTagManager_Block_Product extends Yireo_GoogleTagManager_Block_Default
{
    /**
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        $currentProduct = Mage::registry('current_product');
        return $currentProduct;
    }

    public function getProductAttributeText(Mage_Catalog_Model_Product $product, $attributeCode)
    {
        if (!$product->getData($attributeCode)) {
            return '';
        }

        return $product->getAttributeText($attributeCode);
    }
}
