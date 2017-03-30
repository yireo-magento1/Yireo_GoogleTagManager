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
 * Class Yireo_GoogleTagManager_Block_Data
 */
class Yireo_GoogleTagManager_Block_Data extends Yireo_GoogleTagManager_Block_Default
{
    /**
     * @return string
     */
    public function getPageType()
    {
        $moduleName = $this->getRequest()->getModuleName();
        $controllerName = $this->getRequest()->getControllerName();
        $actionName = $this->getRequest()->getActionName();
        $route = $moduleName . '/' . $controllerName . '/' . $actionName;
        return $route;
    }
}
