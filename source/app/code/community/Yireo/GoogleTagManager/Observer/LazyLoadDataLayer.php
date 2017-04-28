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
 * Class Yireo_GoogleTagManager_Observer_LazyLoadDataLayer
 */
class Yireo_GoogleTagManager_Observer_LazyLoadDataLayer
{
    /**
     * @var Yireo_GoogleTagManager_Helper_Data
     */
    protected $helper;

    /**
     * @var Yireo_GoogleTagManager_Helper_Script
     */
    protected $scriptHelper;

    /**
     * @var Yireo_GoogleTagManager_Model_Container
     */
    protected $container;

    /**
     * Yireo_GoogleTagManager_Model_Observer constructor.
     */
    public function __construct()
    {
        $this->helper = Mage::helper('googletagmanager');
        $this->scriptHelper = Mage::helper('googletagmanager/script');
        $this->container = Mage::getSingleton('googletagmanager/container');
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

        // Try to append the data layer to this block
        $this->appendDataLayer($block, $event);

        return $this;
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     * @param Varien_Event $event
     *
     * @return bool
     */
    protected function appendDataLayer(Mage_Core_Block_Abstract $block, Varien_Event $event)
    {
        /** @var Mage_Catalog_Block_Product_List $block */
        if (!$this->allowBlock($block)) {
            return false;
        }

        $transport = $event->getTransport();
        $html = $transport->getHtml();

        /** @var Yireo_GoogleTagManager_Block_Data $dataLayerBlock */
        $dataLayerBlock = $block->getLayout()->getBlock('googletagmanager_data');
        if (!$dataLayerBlock) {
            return false;
        }

        $dataLayerHtml = trim($dataLayerBlock->toHtml());

        $html = str_replace('var dataLayer = [];', $dataLayerHtml, $html);
        $transport->setHtml($html);

        return true;
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     *
     * @return bool
     */
    protected function allowBlock($block)
    {
        $allowedBlocks = ['root'];
        if (!in_array($block->getNameInLayout(), $allowedBlocks)) {
            return false;
        }

        return true;
    }

}
