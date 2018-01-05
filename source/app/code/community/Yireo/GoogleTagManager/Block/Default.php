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
 * Class Yireo_GoogleTagManager_Block_Default
 */
class Yireo_GoogleTagManager_Block_Default extends Mage_Core_Block_Template
{
    /**
     * @var $moduleHelper Yireo_GoogleTagManager_Helper_Data
     */
    protected $moduleHelper;

    /**
     * @var $scriptHelper Yireo_GoogleTagManager_Helper_Script
     */
    protected $scriptHelper;

    /**
     * @var $container Yireo_GoogleTagManager_Model_Container
     */
    protected $container;

    /**
     * @var $layout Mage_Core_Model_Layout
     */
    protected $layout;

    /**
     * @var $request Mage_Core_Controller_Request_Http
     */
    protected $request;
    
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->moduleHelper = Mage::helper('googletagmanager');
        $this->scriptHelper = Mage::helper('googletagmanager/script');
        $this->container = Mage::getSingleton('googletagmanager/container');
        $this->layout = Mage::app()->getLayout();
        $this->catalogConfig = Mage::getModel('catalog/config');
        $this->request     = Mage::app()->getRequest();
        
        parent::_construct();
    }
    
    /**
     * Return whether this module is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getModuleHelper()->isEnabled();
    }

    /**
     * Check whether this module is in debugging mode
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->getModuleHelper()->isDebug();
    }

    /**
     * Get the GA ID
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getModuleHelper()->getId();
    }

    /**
     * Get current store details
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Return a configuration value
     *
     * @param null $key
     * @param null $default_value
     *
     * @return mixed
     */
    public function getConfig($key = null, $default_value = null)
    {
        return $this->getModuleHelper()->getConfigValue($key, $default_value);
    }

    /**
     * Get the main helper
     *
     * @return Yireo_GoogleTagManager_Helper_Data
     */
    public function getModuleHelper()
    {
        return $this->moduleHelper;
    }

    /**
     * Get the script helper
     *
     * @return Yireo_GoogleTagManager_Helper_Script
     */
    public function getScriptHelper()
    {
        return $this->scriptHelper;
    }

    /**
     * Get the GA container
     *
     * @return Yireo_GoogleTagManager_Model_Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Determine whether this GA configuration has any attributes
     *
     * @return bool
     */
    public function hasAttributes()
    {
        $attributes = $this->getAttributes();
        if(!empty($attributes)) {
            return true;
        }
        return false;
    }

    /**
     * Return all attributes as JSON
     *
     * @return string
     */
    public function getAttributesAsJson()
    {
        $attributes = $this->getAttributes();
        return json_encode($attributes);
    }

    /**
     * Add a new attribute to the GA container
     *
     * @param $name
     * @param $value
     *
     * @return Varien_Object
     */
    public function addAttribute($name, $value)
    {
        return $this->getContainer()->setData($name, $value);
    }

    /**
     * Get the configured attributes for a GA container
     *
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->getContainer()->getData();
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function jsonEncode($data)
    {
        $string = json_encode($data, JSON_HEX_APOS);
        $string = str_replace('"', "'", $string);
        return $string;
    }

    /**
     * @param string $price
     * @return string
     */
    public function formatPrice($price)
    {
        return number_format((float) $price, 2);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        // Bypass a bug that causes an empty HTML block to be skipped
        $html = parent::_toHtml();
        if (empty($html)) {
            $html = ' ';
        }

        return $html;
    }

    /**
     * Return current page contains
     * * checkout
     * * onestepcheckout
     *
     * @return bool
     */
    public function isCheckoutPage()
    {
        if (stripos($this->request->getControllerModule(), 'checkout') !== false ) {
            return true;
        }
        return false;
    }
}
