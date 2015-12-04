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
 * Class Yireo_GoogleTagManager_Block_Script
 */
class Yireo_GoogleTagManager_Block_Script extends Yireo_GoogleTagManager_Block_Default
{
    /**
     * Return the JavaScript for insertion in the HTML header
     *
     * @return string
     */
    public function getScript()
    {
        return Mage::helper('googletagmanager')->getHeaderScript();
    }
}
