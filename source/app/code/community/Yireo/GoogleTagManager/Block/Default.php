<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_GoogleTagManager_Block_Default
 */
class Yireo_GoogleTagManager_Block_Default extends Mage_Core_Block_Template
{
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
     * Get the GA helper
     *
     * @return Yireo_GoogleTagManager_Helper_Data
     */
    public function getModuleHelper()
    {
        return Mage::helper('googletagmanager');
    }

    /**
     * Get the GA container
     *
     * @return Yireo_GoogleTagManager_Model_Container
     */
    public function getContainer()
    {
        return Mage::getSingleton('googletagmanager/container');
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
     * Return a product collection
     *
     * @return bool|object
     */
    public function getProductCollection()
    {
        return false;
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function jsonEncode($data)
    {
        $string = json_encode($data);
        $string = str_replace('"', "'", $string);
        return $string;
    }

    /**
     * @param $childScript
     */
    public function setChildScript($childScript)
    {
        $this->childScript = $childScript;
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
}
