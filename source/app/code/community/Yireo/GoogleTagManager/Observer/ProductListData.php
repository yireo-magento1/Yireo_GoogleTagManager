<?php
/**
 * GoogleTagManager plugin for Magento
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_GoogleTagManager_Observer_ProductListData
 */
class Yireo_GoogleTagManager_Observer_ProductListData
{
    /**
     * @var Yireo_GoogleTagManager_Helper_Data
     */
    protected $helper;

    /**
     * Yireo_GoogleTagManager_Model_Observer constructor.
     */
    public function __construct()
    {
        $this->helper = Mage::helper('googletagmanager');
    }

    /**
     * Listen to the event core_block_abstract_to_html_after
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        if ($this->helper->isEnabled() === false) {
            return $this;
        }

        /** @var Varien_Event $event */
        $event = $observer->getEvent();
        $block = $event->getBlock();

        // Try to extend the product-list block
        $this->extendProductListBlock($block);

        return $this;
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     * @param Varien_Event $event
     *
     * @return bool
     */
    protected function extendProductListBlock(Mage_Core_Block_Abstract $block)
    {
        /** @var Mage_Catalog_Block_Product_List $block */
        if (!$this->allowBlock($block)) {
            return false;
        }

        $i = 0;
        $categoryProducts = [];

        foreach ($block->getLoadedProductCollection() as $product) {
            $categoryProduct = $this->getProductData($product);
            $categoryProduct['position'] = $i;
            $categoryProducts[] = $categoryProduct;
            $i++;
        }

        $container = Mage::getSingleton('googletagmanager/container');
        $container->setData('categoryProducts', $categoryProducts);
        $container->setData('categorySize', count($categoryProducts));
        Mage::helper('googletagmanager/script')->addEcommerceData('impressions', $categoryProducts);

        return true;
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     *
     * @return bool
     */
    protected function allowBlock($block)
    {
        $allowedBlocks = ['product_list', 'search_result_list'];
        if (!in_array($block->getNameInLayout(), $allowedBlocks)) {
            return false;
        }

        return true;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return array
     */
    protected function getProductData(Mage_Catalog_Model_Product $product)
    {
        $price = $this->getPrice($product);
        $taxPercentage = $this->getTaxPercentage($product);
        $tax = ($price / (100 + $taxPercentage)) * $taxPercentage;

        $data = array();
        $data['id'] = $product->getId();
        $data['name'] = $this->quoteEscape($product->getName());
        $data['sku'] = $this->quoteEscape($product->getSku());
        $data['price'] = $price;
        $data['priceexcludingtax'] = number_format($price - $tax, 2);
        $data['tax'] = number_format($tax, 2);
        $data['taxrate'] = $taxPercentage;
        $data['gender'] = $this->getGender($product);

        $category = Mage::registry('current_category');
        if (!empty($category) && $category->getId() > 0) {
            $data['category'] = $this->quoteEscape($category->getName());
        }

        return $data;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return double
     */
    protected function getPrice(Mage_Catalog_Model_Product $product)
    {
        $price = $product->getPrice();
        $specialPrice = $product->getSpecialprice();

        if (($specialPrice > 0) && ($specialPrice < $price)) {
            $price = $specialPrice;
        }

        return $price;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return double
     */
    protected function getTaxPercentage(Mage_Catalog_Model_Product $product)
    {
        $store = Mage::app()->getStore();
        $taxCalculation = Mage::getModel('tax/calculation');
        $request = $taxCalculation->getRateRequest(null, null, null, $store);
        $taxClassId = $product->getTaxClassId();
        $taxPercentage = $taxCalculation->getRate($request->setProductClassId($taxClassId));
        return $taxPercentage;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    protected function getGender(Mage_Catalog_Model_Product $product)
    {
        return Mage::getModel('catalog/resource_eav_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'gender')->getSource()->getOptionText($product->getGender());
    }

    /**
     * @param $string
     *
     * @return string
     */
    protected function quoteEscape($string)
    {
        return Mage::helper('core')->quoteEscape($string);
    }
}
