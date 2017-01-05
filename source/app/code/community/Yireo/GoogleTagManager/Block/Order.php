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

        foreach($order->getAllVisibleItems() as $item) { /* Changed from getAllItems to ignore configurable / simple duplicate products */

            /** @var Mage_Sales_Model_Order_Item $item */

            // Only add composed types once
            if( $item->getParentItemId() ) {
	        continue;
	    }

            /** @var Mage_Catalog_Model_Product $product */
            $product = $item->getProduct();

            $taxCalculation = Mage::getModel('tax/calculation');
            $request = $taxCalculation->getRateRequest(null, null, null, $store);
            $taxClassId = $product->getTaxClassId();
            $taxpercent = $taxCalculation->getRate($request->setProductClassId($taxClassId));

            $price = $product->getPrice();
            $specialPrice = $product->getSpecialprice();
            if (($specialPrice > 0) && ($specialPrice < $price)) {
                $price = $specialPrice;
            }
            $tax = ($price / (100 + $taxpercent)) * $taxpercent;

            $data[] = array(
                'id' => $item->getId(),
                'sku' => $this->quoteEscape($item->getSku()),
                'name' => $this->quoteEscape($item->getName()),
                'price' => $price,
                'priceexcludingtax' => number_format($price - $tax, 2),
                'tax' => number_format($tax, 2),
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
    public function getProductCategoryTrees($product, $categorySeparator = ' > ', $pathSeparator = ' | ')
    {
        $categoryIds = $product->getCategoryIds();
        $categoryTrees = '';
        foreach ($categoryIds as $categoryId) {
            $tmpId = $categoryId;
            $categories = array();
            while ($tmpId != Mage::app()->getStore()->getRootCategoryId()) {
                $category = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($tmpId);
                $categories[] = $category;
                $tmpId = $category->getParentId();
            };
            for ($i = count($categories) - 1; $i >= 0; $i--) {
                $categoryTrees .= $categories[$i]->getName();
                $categoryTrees .= $i > 0 ? $categorySeparator : $pathSeparator;
            }
        };
        return $categoryTrees;
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
