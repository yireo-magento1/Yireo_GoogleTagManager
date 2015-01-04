<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_GoogleTagManager_Block_Script extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
        return Mage::helper('googletagmanager')->getHeaderScript();
    }
}
