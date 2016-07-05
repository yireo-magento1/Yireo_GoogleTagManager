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
 * Class Yireo_GoogleTagManager_Helper_Data
 */
class Yireo_GoogleTagManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Constant for the observer method
     */
    const METHOD_OBSERVER = 0;

    /**
     * Constant for the layout method
     */
    const METHOD_LAYOUT = 1;

    /**
     * Ecommerce data
     */
    protected $ecommerceData = array();

    /**
     * Check whether the module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if ((bool)Mage::getStoreConfig('advanced/modules_disable_output/Yireo_GoogleTagManager')) {
            return false;
        }

        return (bool)$this->getConfigValue('active', false);
    }

    /**
     * Check whether the module is in debugging mode
     *
     * @return bool
     */
    public function isDebug()
    {
        return (bool)$this->getConfigValue('debug');
    }

    /**
     * Return the GA ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->getConfigValue('id');
    }

    /**
     * Check whether the insertion method is the observer method
     *
     * @return bool
     */
    public function isMethodObserver()
    {
        return ($this->getConfigValue('method') == self::METHOD_OBSERVER);
    }

    /**
     * Check whether the insertion method is the layout method
     *
     * @return bool
     */
    public function isMethodLayout()
    {
        return ($this->getConfigValue('method') == self::METHOD_LAYOUT);
    }

    /**
     * Debugging method
     *
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
     * Return a configuration value
     *
     * @param null $key
     * @param null $default_value
     *
     * @return mixed|null
     */
    public function getConfigValue($key = null, $default_value = null)
    {
        $value = Mage::getStoreConfig('googletagmanager/settings/' . $key);
        if (empty($value)) {
            $value = $default_value;
        }

        return $value;
    }

    /**
     * Fetch a specific block
     *
     * @param $name
     * @param $type
     * @param $template
     *
     * @return bool|Mage_Core_Block_Template
     */
    public function fetchBlock($name, $type, $template)
    {
        /** @var Mage_Core_Model_Layout $layout */
        if (!($layout = Mage::app()->getFrontController()->getAction()->getLayout())) {
            return false;
        }

        /** @var Mage_Core_Block_Template $block */
        if ($block = $layout->getBlock('googletagmanager_' . $name)) {
            $block->setTemplate('googletagmanager/' . $template);
            $this->debug('Helper: Loading block from layout: ' . $name);
            return $block;
        }

        if ($block = $layout->createBlock('googletagmanager/' . $type)) {
            $block->setTemplate('googletagmanager/' . $template);
            $this->debug('Helper: Creating new block: ' . $type);
            return $block;
        }

        $this->debug('Helper: Unknown block: ' . $name);
        return false;
    }

    /**
     * Return this header script
     *
     * @return string
     */
    public function getHeaderScript()
    {
        $childScript = '';

        // Load the main script
        if (!($block = $this->fetchBlock('default', 'default', 'default.phtml'))) {
            return $childScript;
        }

        // Add customer-information
        $childScript .= $this->getCustomerScript();

        // Add product-information
        $childScript .= $this->getProductScript();

        // Add category-information
        $childScript .= $this->getCategoryScript();

        // Add search-information
        $childScript .= $this->getSearchScript();
        
        // Add tagged products
        $childScript .= $this->getTagScript();

        // Add order-information
        $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        if (!empty($lastOrderId)) {
            $childScript .= $this->getOrderScript();

            // Add quote-information
        } else {
            $childScript .= $this->getQuoteScript();
        }

        // Add custom information
        $childScript .= $this->getCustomScript();

        // Add enhanced ecommerce-information
        $childScript .= $this->getEcommerceScript();

        $block->setChildScript($childScript);
        $html = $block->toHtml();

        return $html;
    }

    /**
     * @return string
     */
    public function getCustomerScript()
    {
        $customer = Mage::getSingleton('customer/session');
        if (!empty($customer)) {
            $customerBlock = $this->fetchBlock('customer', 'customer', 'customer.phtml');

            if ($customerBlock) {
                $customerBlock->setCustomer($customer);
                $customerGroup = Mage::getSingleton('customer/group')->load($customer->getCustomerGroupId());
                $customerBlock->setCustomerGroup($customerGroup);
                $html = $customerBlock->toHtml();
                return $html;
            }
        }
    }

    /**
     * @return string
     */
    public function getProductScript()
    {
        $currentProduct = Mage::registry('current_product');
        if (!empty($currentProduct)) {
            $productBlock = $this->fetchBlock('product', 'product', 'product.phtml');

            if ($productBlock) {
                $productBlock->setProduct($currentProduct);
                $html = $productBlock->toHtml();
                return $html;
            }
        }
    }

    /**
     * @return string
     */
    public function getCategoryScript()
    {
        $currentCategory = Mage::registry('current_category');
        if (!empty($currentCategory)) {
            $categoryBlock = $this->fetchBlock('category', 'category', 'category.phtml');
            if ($categoryBlock) {
                $categoryBlock->setCategory($currentCategory);
                $html = $categoryBlock->toHtml();
                return $html;
            }
        }
    }

    /**
     * @return string
     */
    public function getSearchScript()
    {
        $searchBlock = $this->fetchBlock('search', 'search', 'search.phtml');
        if (!$searchBlock) {
            return '';
        }

        $productCollection = $searchBlock->getProductCollection();
        if (empty($productCollection) || $productCollection->count() < 1) {
            return '';
        }

        $searchBlock->setProducts($productCollection);
        $html = $searchBlock->toHtml();
        return $html;
    }

    /**
     * @return string
     */
    public function getTagScript()
    {
        $tagBlock = $this->fetchBlock('tag', 'tag', 'tag.phtml');
        if (!$tagBlock) {
            return '';
        }

        $productCollection = $tagBlock->getProductCollection();
        if (empty($productCollection) || $productCollection->count() < 1) {
            return '';
        }

        $tagBlock->setProducts($productCollection);
        $html = $tagBlock->toHtml();
        return $html;
    }

    /**
     * @return string
     */
    public function getOrderScript()
    {
        $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();

        if (!empty($lastOrderId)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($lastOrderId);
            $orderBlock = $this->fetchBlock('order', 'order', 'order.phtml');

            if ($orderBlock) {
                $orderBlock->setOrder($order);
                $html = $orderBlock->toHtml();
                return $html;
            }
        }
    }

    /**
     * @return string
     */
    public function getQuoteScript()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getModel('checkout/cart')->getQuote();

        if ($quote->getId() > 0) {
            $quoteBlock = $this->fetchBlock('quote', 'quote', 'quote.phtml');

            if ($quoteBlock) {
                $quoteBlock->setQuote($quote);
                $html = $quoteBlock->toHtml();
                return $html;
            }
        }
    }

    /**
     * @return string
     */
    public function getEcommerceScript()
    {
        $ecommerce = $this->getEcommerceData();

        if (!empty($ecommerce)) {
            $ecommerceBlock = $this->fetchBlock('ecommerce', 'ecommerce', 'ecommerce.phtml');

            if ($ecommerceBlock) {
                $ecommerceBlock->setData('ecommerce', $ecommerce);
                $html = $ecommerceBlock->toHtml();
                return $html;
            }
        }
    }

    /**
     * @return string
     */
    public function getCustomScript()
    {
        $customBlock = $this->fetchBlock('custom', 'custom', 'custom.phtml');

        if ($customBlock) {
            $html = $customBlock->toHtml();
            return $html;
        }
    }

    /**
     * @return array
     */
    protected function getEcommerceData()
    {
        if (empty($this->ecommerceData)) {
            $this->ecommerceData = array(
                'currencyCode' => $this->getCurrencyCode(),
            );
        }

        return $this->ecommerceData;
    }

    /**
     * @param $name
     * @param $value
     */
    public function addEcommerceData($name, $value)
    {
        $this->ecommerceData[$name] = $value;
    }

    /**
     * @param $product
     * @param bool $addJsEvent
     *
     * @return string
     */
    public function onClickProduct($product, $addJsEvent = true)
    {
        $block = $this->fetchBlock('custom', 'custom', 'product_click.phtml');
        $html = '';

        if ($block) {
            $block->setProduct($product);
            $html = $block->toHtml();
        }

        if ($addJsEvent && !empty($html)) {
            $html = 'onclick="' . $html . '"';
        }

        return $html;
    }

    /**
     * @param $product
     * @param bool $addJsEvent
     *
     * @return string
     */
    public function onAddToCart($product, $addJsEvent = true)
    {
        $block = $this->fetchBlock('custom', 'custom', 'product_addtocart.phtml');
        $html = '';

        if ($block) {
            $block->setProduct($product);
            $html = $block->toHtml();
        }

        if ($addJsEvent && !empty($html)) {
            $html = 'onclick="' . $html . '"';
        }

        return $html;
    }

    /**
     * @param $product
     * @param bool $addJsEvent
     *
     * @return string
     */
    public function onRemoveFromCart($product, $addJsEvent = true)
    {
        $block = $this->fetchBlock('custom', 'custom', 'product_removefromcart.phtml');
        $html = '';

        if ($block) {
            $block->setProduct($product);
            $html = $block->toHtml();
        }

        if ($addJsEvent && !empty($html)) {
            $html = 'onclick="' . $html . '"';
        }

        return $html;
    }

    /**
     * Return the current currency code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * @param $message
     */
    public function writeLog($message)
    {
        if ((bool)$this->getConfigValue('debug', 0)) {
            Mage::log('Yireo_GoogleTagManager: ' . $message);
        }
    }
}
