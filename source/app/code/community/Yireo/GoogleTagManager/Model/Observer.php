<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_GoogleTagManager_Model_Observer
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
     * @parameter Varien_Event_Observer $observer
     * @return $this
     */
    public function coreBlockAbstractToHtmlAfter($observer)
    {
        if ($this->helper->isEnabled() == false) {
            return $this;
        }

        if ($this->helper->isMethodObserver() == false) {
            return $this;
        }

        $block = $observer->getEvent()->getBlock();
        if($block->getNameInLayout() == 'root') {

            $transport = $observer->getEvent()->getTransport();
            $html = $transport->getHtml();

            $script = $this->helper->getHeaderScript();

            if (empty($script)) {
                $this->helper->debug('Observer: Empty script');
                return $this;
            }

            $html = preg_replace('/\<body([^\>]+)\>/', '\0'.$script, $html);
            $this->helper->debug('Observer: Replacing header');

            $transport->setHtml($html);
        }

        return $this;
    }
}
