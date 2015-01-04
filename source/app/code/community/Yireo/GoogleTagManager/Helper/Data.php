<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
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
        $childScript = '';

        // Check for the frontend layout
        if (!($layout = Mage::app()->getFrontController()->getAction()->getLayout())) {
            return $childScript;
        }

        // Check for the Google Tag Manager block
        if (!($block = $layout->getBlock('googletagmanager'))) {
            return $childScript;
        }

        // Add product-information
        $currentProduct = Mage::registry('current_product');
        if(!empty($currentProduct)) {
            $productBlock = $layout->getBlock('googletagmanager_product');
            if($productBlock) {
                $productBlock->setProduct($currentProduct);
                $childScript .= $productBlock->toHtml();
            }
        }

        // Add category-information
        $currentCategory = Mage::registry('current_category');
        if(!empty($currentCategory)) {
            $categoryBlock = $layout->getBlock('googletagmanager_category');
            if($categoryBlock) {
                $categoryBlock->setCategory($currentCategory);
                $childScript .= $categoryBlock->toHtml();
            }
        }

        // Add order-information
        $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        if(!empty($lastOrderId)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($lastOrderId);
            $orderBlock = $layout->getBlock('googletagmanager_order');
            if($orderBlock) {
                $orderBlock->setOrder($order);
                $childScript .= $orderBlock->toHtml();
            }

        // Add quote-information
        } else {
            $quote = Mage::getModel('checkout/cart')->getQuote();
            if($quote->getId() > 0) {
                $quoteBlock = $layout->getBlock('googletagmanager_quote');
                if($quoteBlock) {
                    $quoteBlock->setQuote($quote);
                    $childScript .= $quoteBlock->toHtml();
                }
            }
        }

        // Add custom information
        $customBlock = $layout->getBlock('googletagmanager_custom');
        if($customBlock) {
            $childScript .= $customBlock->toHtml();
        }

        $block->setChildScript($childScript);
        $html = $block->toHtml();
        return $html;
    }
}
