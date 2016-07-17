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
     * @var $catalogConfig Mage_Catalog_Model_Config
     */
    protected $catalogConfig;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->catalogConfig = Mage::getModel('catalog/config');

        parent::_construct();
    }

    /**
     * @return Mage_Eav_Model_Entity_Collection_Abstract|null
     */
    public function getProductCollection()
    {
        /** @var Mage_Catalog_Block_Product_List $productListBlock */
        $productListBlock = $this->layout->getBlock('product_list');

        if (empty($productListBlock)) {
            return null;
        }

        // Fetch the current collection from the block and set pagination
        $collection = $productListBlock->getLoadedProductCollection();

        // Set Limit Except for 'all' products
        if ($this->getLimit() != 'all') {
            $collection->setCurPage($this->getCurrentPage())->setPageSize($this->getLimit());
        }

        if ($this->moduleHelper->getConfigValue('category_sorting') == 'block') {
            $this->applyBlockSorting($collection, $productListBlock);
        } else {
            $this->applyUrlSorting($collection);
        }

        return $collection;
    }

    /**
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param Mage_Catalog_Block_Product_List $block
     */
    public function applyBlockSorting(Mage_Eav_Model_Entity_Collection_Abstract &$collection, Mage_Catalog_Block_Product_List $productListBlock)
    {
        $toolbar = $productListBlock->getToolbarBlock();
        if ($orders = $productListBlock->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $productListBlock->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $productListBlock->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $productListBlock->getModes()) {
            $toolbar->setModes($modes);
        }
        $collection->setOrder($toolbar->getCurrentOrder(), $toolbar->getCurrentDirection());

        //$productListBlock->toHtml();
        //echo 'console.log("Block sorting: '.$order.' / '.$dir.'");';
    }

    /**
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     */
    public function applyUrlSorting(Mage_Eav_Model_Entity_Collection_Abstract &$collection)
    {
        $order = $this->getCurrentOrder();
        $dir = $this->getCurrentDirection();
        //echo 'console.log("Block sorting: '.$order.' / '.$dir.'");';

        if ($order) {
            $sortingData = $this->catalogConfig->getAttributesUsedForSortBy();

            if (isset($sortingData[$order]['attribute_code']) and $attributeCode = $sortingData[$order]['attribute_code']) {
                $collection->setOrder(
                    $attributeCode,
                    $dir == 'asc'
                        ? Varien_Data_Collection_Db::SORT_ORDER_ASC
                        : Varien_Data_Collection_Db::SORT_ORDER_DESC
                );
            }
        }
    }

    /**
     * @return string
     */
    public function getCurrentOrder()
    {
        $order = strtolower(trim($this->request->getParam('order')));

        if (!empty($order))
        {
            return $order;
        }

        $order = strtolower(trim(Mage::getSingleton('catalog/session')->getSortOrder()));

        if (!empty($order))
        {
            return $order;
        }

        $order = $this->catalogConfig->getProductListDefaultSortBy();

        return $order;
    }

    /**
     * @return string
     */
    public function getCurrentDirection()
    {
        $direction = strtolower(trim($this->request->getParam('dir')));

        if (!empty($direction))
        {
            return $direction;
        }

        $direction = strtolower(trim(Mage::getSingleton('catalog/session')->getSortDirection()));

        if (!empty($direction))
        {
            return $direction;
        }

        $direction = 'asc';

        return $direction;
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
        $productListBlockToolbar = $this->layout->getBlock('product_list_toolbar');
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
        if ($page = (int)$this->getRequest()->getParam('p')) {
            return $page;
        }

        return 1;
    }
}
