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
 * Class Yireo_GoogleTagManager_Block_Ecommerce
 */
class Yireo_GoogleTagManager_Block_Ecommerce extends Yireo_GoogleTagManager_Block_Default
{
    public function getEcommerceData()
    {
        return $this->getScriptHelper()->getEcommerceData();
    }

    public function getEcommerceEvent()
    {
        return $this->getScriptHelper()->getEcommerceEvent();
    }
}
