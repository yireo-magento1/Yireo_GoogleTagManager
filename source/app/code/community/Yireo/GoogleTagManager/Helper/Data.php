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
    /**
     * @return bool
     */
    public function isEnabled()
    {
        if ((bool)Mage::getStoreConfig('advanced/modules_disable_output/Yireo_GoogleTagManager')) {
            return false;
        }

        return (bool)$this->gertConfigValue('enabled');
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return (bool)$this->getConfigValue('debug');
    }

    /**
     * @return bool
     */
    public function isMethodObserver()
    {
        return ($this->getConfigValue('method') == 0);
    }

    /**
     * @return bool
     */
    public function isMethodLayout()
    {
        return ($this->getConfigValue('method') == 1);
    }

    /**
     * @param $string
     * @param null $variable
     *
     * @return bool
     */
    public function debug($string, $variable = null)
    {
        if ($this->isDebug() == false) {
            return false;
        }

        if (!empty($variable)) {
            $string .= ': ' . var_export($variable, true);
        }

        Mage::log('Yireo_GoogleTagManager: ' . $string);

        return true;
    }

    /**
     * @param null $key
     * @param null $default_value
     *
     * @return mixed|null
     */
    public function getConfigValue($key = null, $default_value = null)
    {
        $value = Mage::getStoreConfig('googletagmanager/settings/' . $key);
        if (empty($value)) $value = $default_value;
        return $value;
    }

    /**
     * @param $name
     * @param $type
     * @param $template
     *
     * @return bool
     */
    public function fetchBlock($name, $type, $template)
    {
        if (!($layout = Mage::app()->getFrontController()->getAction()->getLayout())) {
            return false;
        }

        if ($block = $layout->getBlock('googletagmanager_' . $name)) {
            $this->debug('Helper: Loading block from layout: '.$name);
            return $block;
        }

        if ($block = $layout->createBlock('googletagmanager/' . $type)->setTemplate('googletagmanager/' . $template)) {
            $this->debug('Helper: Creating new block: '.$type);
            return $block;
        }

        $this->debug('Helper: Unknown block: '.$name);
        return false;
    }

    /**
     *
     */
    public function getHeaderScript()
    {
        $childScript = '';

        // Load the main script
        if (!($block = $this->fetchBlock('default', 'default', 'default.phtml'))) {
            return $childScript;
        }

        // Add customer-information
        $this->addCustomer($childScript);

        // Add product-information
        $this->addProduct($childScript);

        // Add category-information
        $this->addCategory($childScript);

        // Add order-information
        $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        if (!empty($lastOrderId)) {
            $this->addOrder($childScript);

            // Add quote-information
        } else {
            $this->addQuote($childScript);
        }

        // Add custom information
        $this->addCustom($childScript);

        $block->setChildScript($childScript);
        $html = $block->toHtml();

        return $html;
    }

    /**
     * @param $childScript string
     */
    public function addCustomer(&$childScript)
    {
        $customer = Mage::getSingleton('customer/session');
        if (!empty($customer)) {
            $customerBlock = $this->fetchBlock('customer', 'customer', 'customer.phtml');

            if ($customerBlock) {
                $customerBlock->setCustomer($customer);
                $customerGroup = Mage::getSingleton('customer/group')->load($customer->getCustomerGroupId());
                $customerBlock->setCustomerGroup($customerGroup);
                $childScript .= $customerBlock->toHtml();
            }
        }
    }

    /**
     * @param $childScript string
     */
    public function addProduct(&$childScript)
    {
        $currentProduct = Mage::registry('current_product');
        if (!empty($currentProduct)) {
            $productBlock = $this->fetchBlock('product', 'product', 'product.phtml');

            if ($productBlock) {
                $productBlock->setProduct($currentProduct);
                $childScript .= $productBlock->toHtml();
            }
        }
    }

    /**
     * @param $childScript string
     */
    public function addCategory(&$childScript)
    {
        $currentCategory = Mage::registry('current_category');
        if (!empty($currentCategory)) {
            $categoryBlock = $this->fetchBlock('category', 'category', 'category.phtml');

            if ($categoryBlock) {
                $categoryBlock->setCategory($currentCategory);
                $childScript .= $categoryBlock->toHtml();
            }
        }
    }

    /**
     * @param $childScript string
     */
    public function addOrder(&$childScript)
    {
        $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();

        if (!empty($lastOrderId)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($lastOrderId);
            $orderBlock = $this->fetchBlock('order', 'order', 'order.phtml');

            if ($orderBlock) {
                $orderBlock->setOrder($order);
                $childScript .= $orderBlock->toHtml();
            }
        }
    }

    /**
     * @param $childScript string
     */
    public function addQuote(&$childScript)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getModel('checkout/cart')->getQuote();

        if ($quote->getId() > 0) {
            $quoteBlock = $this->fetchBlock('quote', 'quote', 'quote.phtml');

            if ($quoteBlock) {
                $quoteBlock->setQuote($quote);
                $childScript .= $quoteBlock->toHtml();
            }
        }
    }

    /**
     * @param $childScript string
     */
    public function addCustom(&$childScript)
    {
        $customBlock = $this->fetchBlock('custom', 'custom', 'custom.phtml');

        if ($customBlock) {
            $childScript .= $customBlock->toHtml();
        }
    }
}
