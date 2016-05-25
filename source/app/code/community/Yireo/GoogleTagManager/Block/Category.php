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
 * Class Yireo_GoogleTagManager_Block_Category
 */
class Yireo_GoogleTagManager_Block_Category extends Yireo_GoogleTagManager_Block_Default
{
    /**
     * @return Mage_Eav_Model_Entity_Collection_Abstract|null
     */
    public function getProductCollection()
    {
        /** @var Mage_Catalog_Block_Product_List $productListBlock */
        $productListBlock = Mage::app()->getLayout()->getBlock('product_list');

        if (empty($productListBlock)) {
            return null;
        }

        // Fetch the current collection from the block and set pagination
        $collection = $productListBlock->getLoadedProductCollection();
        
        // Set Limit Except for 'all' products
        if ($this->getLimit() != 'all') {
            $collection->setCurPage($this->getCurrentPage())->setPageSize($this->getLimit());
        }

        // Set default order/direction, failing to do so will prevent proper default order/direction on category views
        // ($this->_isOrdersRendered already set in resource collection but no sorting applied)
        if ($productListBlock->getSortBy()) {
            $collection->setOrder($productListBlock->getSortBy(), $productListBlock->getDefaultDirection());
        }

        return $collection;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     */
    public function setCategory(Mage_Catalog_Model_Category $category)
    {
        $this->category = $category;
    }

    /**
     * Return the current page limit, as set by the toolbar block
     *
     * @return int
     */
    protected function getLimit()
    {
        /** @var Mage_Catalog_Block_Product_List_Toolbar $productListBlockToolbar */
        $productListBlockToolbar = Mage::app()->getLayout()->getBlock('product_list_toolbar');
        if (empty($productListBlockToolbar)) {
            return 9;
        }

        return $productListBlockToolbar->getLimit();
    }

    /**
     * Return the current page as set in the URL
     *
     * @return int
     * @throws Exception
     */
    protected function getCurrentPage()
    {
        if ($page = (int) $this->getRequest()->getParam('p')) {
            return $page;
        }

        return 1;
    }
}
