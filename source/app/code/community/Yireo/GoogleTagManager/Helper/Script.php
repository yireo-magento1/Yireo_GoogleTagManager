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
 * Class Yireo_GoogleTagManager_Helper_Script
 */
class Yireo_GoogleTagManager_Helper_Script extends Mage_Core_Helper_Abstract
{
    /**
     * Ecommerce data
     */
    protected $ecommerceData = array();
    
    /**
     * Enhanced Ecommerce Event Name
     * @var string|null
     */
    protected $ecommerceEvent = null;

    /**
     * @var Yireo_GoogleTagManager_Helper_Data
     */
    protected $helper;

    /**
     * Yireo_GoogleTagManager_Helper_Script constructor.
     */
    public function __construct()
    {
        $this->helper = Mage::helper('googletagmanager');
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
            $this->helper->debug('Helper: Loading block from layout: ' . $name);
            return $block;
        }

        if ($block = $layout->createBlock('googletagmanager/' . $type)) {
            $block->setTemplate('googletagmanager/' . $template);
            $this->helper->debug('Helper: Creating new block: ' . $type);
            return $block;
        }

        $this->helper->debug('Helper: Unknown block: ' . $name);
        return false;
    }

    /**
     * @return array
     */
    public function getEcommerceData()
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
        $block = $this->fetchBlock('product_click', 'custom', 'product_click.phtml');
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
        $block = $this->fetchBlock('product_addtocart', 'custom', 'product_addtocart.phtml');
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
        $block = $this->fetchBlock('product_removefromcart', 'custom', 'product_removefromcart.phtml');
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
    public function onRemoveAll($product, $addJsEvent = true)
    {
        $block = $this->fetchBlock('product_removeall', 'custom', 'product_removeall.phtml');
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
     * @return null|string
     */
    public function getEcommerceEvent()
    {
        return $this->ecommerceEvent;
    }

    /**
     * @param null|string $ecommerceEvent
     */
    public function setEcommerceEvent($ecommerceEvent=null)
    {
        $this->ecommerceEvent = $ecommerceEvent;
    }
}
