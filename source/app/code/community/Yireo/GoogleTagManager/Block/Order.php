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
     * @return string
     */
    public function getLastOrderId()
    {
        $lastOrderId = (string) Mage::getSingleton('checkout/session')->getLastRealOrderId();
        return $lastOrderId;
    }

    /**
     * @return Mage_Sales_Model_Order|null
     */
    public function getOrder()
    {
        $lastOrderId = $this->getLastOrderId();
        if (empty($lastOrderId)) {
            return null;
        }

        $order = Mage::getModel('sales/order')->loadByIncrementId($lastOrderId);
        return $order;
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
        $store = $this->getStore();
        if (empty($order)) {
            return array();
        }

        $data = array();

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($order->getAllVisibleItems() as $item) {

            // Only add composed types once
            if ($item->getParentItemId()) {
                continue;
            }

            /** @var Mage_Catalog_Model_Product $product */
            $product = $item->getProduct();

            $taxCalculation = Mage::getModel('tax/calculation');
            $request = $taxCalculation->getRateRequest(null, null, null, $store);
            $taxClassId = $product->getTaxClassId();
            $taxpercent = $taxCalculation->getRate($request->setProductClassId($taxClassId));

            $price = $item->getPrice();
            
            $tax = ($price / (100 + $taxpercent)) * $taxpercent;

            $data[] = array(
                'id' => $product->getId(),
                'sku' => $this->quoteEscape($item->getSku()),
                'name' => $this->quoteEscape($item->getName()),
                'price' => $this->formatPrice($price),
                'priceexcludingtax' => $this->formatPrice($price - $tax),
                'tax' => $this->formatPrice($tax),
                'taxrate' => $taxpercent,
                'type' => $item->getProductType(),
                'category' => $this->getProductCategoryTrees($product),
                'quantity' => $item->getQtyOrdered(),
            );
        }

        return $data;
    }

    /**
     * Return category trees of product
     *
     * @return string
     */
    public function getProductCategoryTrees(Mage_Catalog_Model_Product $product, $categorySeparator = ' > ', $pathSeparator = ' | ')
    {
        $allCategories = $this->loadAllProductCategories();
        $categoryPaths = [];

        foreach ($product->getCategoryIds() as $categoryId) {
            $category = $allCategories[$categoryId];
            $categoryPath = [];
            
            if(isset($category['path'])){
                foreach ($category['path'] as $pathId) {
                    if ($pathId == 1) {
                        continue;
                    }

                    if ($pathId === Mage::app()->getStore()->getRootCategoryId()) {
                        continue;
                    }

                    $categoryPath[] = $allCategories[$pathId]['name'];
                }
            }

            $categoryPaths[] = implode($categorySeparator, $categoryPath);
        }

        return implode($pathSeparator, $categoryPaths);
    }

    /**
     * @return array
     */
    protected function loadAllProductCategories()
    {
        static $listing = [];
        if (!empty($listing)) {
            return $listing;
        }

        $collection = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name');
        foreach ($collection as $category) {
            /** @var $category Mage_Catalog_Model_Category */
            $listing[$category->getId()] = ['name' => $category->getName(), 'path' => $category->getPathIds()];
        }

        return $listing;
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

    /**
     * @return string
     */
    public function getWebsiteName()
    {
        return Mage::app()->getWebsite()->getName();
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return Mage::app()->getStore()->getBaseCurrencyCode();
    }
}
