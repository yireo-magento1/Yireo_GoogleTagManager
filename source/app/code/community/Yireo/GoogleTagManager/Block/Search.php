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
 * Class Yireo_GoogleTagManager_Block_Search
 */
class Yireo_GoogleTagManager_Block_Search extends Yireo_GoogleTagManager_Block_Category
{
    /**
     * @return Mage_Eav_Model_Entity_Collection_Abstract|null
     */
    public function getProductCollection()
    {
        /** @var Mage_Catalog_Block_Product_List $searchListBlock */
        $searchListBlock = $this->layout->getBlock('search_result_list');

        if (empty($searchListBlock)) {
            return null;
        }

        // Fetch the current collection from the block and set pagination and order
        $collection = $searchListBlock->getLoadedProductCollection();

        // Set Limit Except for 'all' products
        if ($this->getLimit() != 'all') {
            $collection->setCurPage($this->getCurrentPage())->setPageSize($this->getLimit());
        }

        if ($this->moduleHelper->getConfigValue('category_sorting') == 'block' && $searchListBlock->getSortBy()) {
            $collection->setOrder($searchListBlock->getSortBy(), $searchListBlock->getDefaultDirection());
        } else {
            $this->applyUrlSorting($collection);
        }

        return $collection;
    }
}
