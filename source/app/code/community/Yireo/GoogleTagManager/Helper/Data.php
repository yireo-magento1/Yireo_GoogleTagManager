<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_GoogleTagManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfigValue($key = null, $default_value = null)
    {
        $value = Mage::getStoreConfig('googletagmanager/settings/'.$key);
        if(empty($value)) $value = $default_value;
        return $value;
    }

    /*
     * Usage: 
     *   echo Mage::helper('googletagmanager')->getHtml($arguments);
     *   $arguments is an associative array (size, count, url)
    
     */
    public function getHeaderScript()
    {
        $html = '';

        // Check for the frontend layout
        if (!($layout = Mage::app()->getFrontController()->getAction()->getLayout())) {
            return $html;
        }

        // Check for the Google Tag Manager block
        if (!($block = $layout->getBlock('googletagmanager'))) {
            return $html;
        }

        // Add order-information
        $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        if(!empty($lastOrderId)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($lastOrderId);
            $orderBlock = $layout->getBlock('googletagmanager_order');
            $orderBlock->setOrder($order);
            $html .= $orderBlock->toHtml();

        // Add quote-information
        } else {
            $quote = Mage::getModel('checkout/cart')->getQuote();
            if($quote->getId() > 0) {
                $quoteBlock = $layout->getBlock('googletagmanager_quote');
                $quoteBlock->setQuote($quote);
                $html .= $quoteBlock->toHtml();
            }
        }

        $html .= $block->toHtml();
        return $html;
    }
}
