<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_GoogleTagManager_Block_Default extends Mage_Core_Block_Template
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->getConfig('active');
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return (bool)$this->getConfig('debug');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getConfig('id');
    }

    /**
     * @param null $key
     * @param null $default_value
     *
     * @return mixed
     */
    public function getConfig($key = null, $default_value = null)
    {
        return Mage::helper('googletagmanager')->getConfigValue($key, $default_value);
    }

    /**
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
     * @return string
     */
    public function getAttributesAsJson()
    {
        $attributes = $this->getAttributes();
        return json_encode($attributes);
    }

    /**
     * @param $name
     * @param $value
     *
     * @return Varien_Object
     */
    public function addAttribute($name, $value)
    {
        return Mage::getSingleton('googletagmanager/container')->setData($name, $value);
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return Mage::getSingleton('googletagmanager/container')->getData();
    }
}
