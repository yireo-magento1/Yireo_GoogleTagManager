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
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $currentProduct = Mage::registry('current_product');
        if (!empty($currentProduct)) {
            return $currentProduct;
        }
        
        return Mage::getModel('catalog/product');
    }

    /**
     * @return Yireo_GoogleTagManager_Model_Product_Price
     */
    public function getPriceModel()
    {
        /** @var Yireo_GoogleTagManager_Model_Product_Price $priceModel */
        $priceModel = Mage::getModel('googletagmanager/product_price');
        return $priceModel;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        $resourceEavAttributeModel = Mage::getModel('catalog/resource_eav_attribute');
        return (string) $resourceEavAttributeModel->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'gender')->getSource()->getOptionText($this->getProduct()->getGender());
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getProduct()->getTypeId();
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $attributeCode
     * @return string
     */
    public function getProductAttributeText(Mage_Catalog_Model_Product $product, $attributeCode)
    {
        if (!$product->getData($attributeCode)) {
            return '';
        }

        return $product->getAttributeText($attributeCode);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getChildProducts(Mage_Catalog_Model_Product $product)
    {
        if ($product->isConfigurable()) {
            return $this->getChildProductsFromConfigurable($product);
        }

        if ($product->isGrouped()) {
            return $this->getChildProductsFromGrouped($product);
        }

        return [];
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return mixed
     */
    public function getChildProductsFromConfigurable(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getModel('catalog/product_type_configurable')->getUsedProductCollection($product);
        $collection->addAttributeToSelect(['name', 'price']);

        return $collection;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return mixed
     */
    public function getChildProductsFromGrouped(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getModel('catalog/product_type_grouped')->getAssociatedProducts($product);

        return $collection;
    }

    /**
     * @param $array
     * @return mixed
     */
    public function cleanData($array)
    {
        foreach ($array as $name => $value) {
            if (empty($value)) {
                unset($array[$name]);
            }
        }

        return $array;
    }
}
