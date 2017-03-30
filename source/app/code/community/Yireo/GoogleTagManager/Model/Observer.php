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
 * Class Yireo_GoogleTagManager_Model_Observer
 */
class Yireo_GoogleTagManager_Model_Observer
{
    /**
     * Listen to the event core_block_abstract_to_html_after
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     * @deprecated
     */
    public function coreBlockAbstractToHtmlAfter(Varien_Event_Observer $observer)
    {
        return $this;
    }
}
