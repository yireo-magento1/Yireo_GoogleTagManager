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
    public function isEnabled()
    {
        return (bool)$this->getConfig('active');
    }

    public function getId()
    {
        return $this->getConfig('id');
    }

    public function getConfig($key = null, $default_value = null)
    {
        return Mage::helper('googletagmanager')->getConfigValue($key, $default_value);
    }

    public function hasAttributes()
    {
        $attributes = $this->getAttributes();
        if(!empty($attributes)) {
            return true;
        }
        return false;
    }

    public function getAttributesAsJson()
    {
        $attributes = $this->getAttributes();
        return json_encode($attributes);
    }

    public function addAttribute($name, $value)
    {
        return Mage::getSingleton('googletagmanager/container')->setData($name, $value);
    }

    public function getAttributes()
    {
        return Mage::getSingleton('googletagmanager/container')->getData();
    }
}
