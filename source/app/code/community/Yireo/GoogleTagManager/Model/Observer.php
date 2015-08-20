<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_GoogleTagManager_Model_Observer
{
    /**
     * Listen to the event core_block_abstract_to_html_after
     *
     * @parameter Varien_Event_Observer $observer
     * @return $this
     */
    public function coreBlockAbstractToHtmlAfter($observer)
    {
        if ($this->getHelper()->isMethodObserver() == false) {
            return $this;
        }

        $block = $observer->getEvent()->getBlock();
        if($block->getNameInLayout() == 'root') {

            $transport = $observer->getEvent()->getTransport();
            $html = $transport->getHtml();

            $script = Mage::helper('googletagmanager')->getHeaderScript();

            if (empty($script)) {
                $this->getHelper()->debug('Observer: Empty script');
                return $this;
            }

            $html = preg_replace('/\<body([^\>]+)\>/', '\0'.$script, $html);
            $this->getHelper()->debug('Observer: Replacing header');

            $transport->setHtml($html);
        }

        return $this;
    }

    /**
     * @return Yireo_GoogleGears_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('googletagmanager');
    }
}
