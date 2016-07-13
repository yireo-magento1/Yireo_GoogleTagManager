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
 * Class Yireo_GoogleTagManager_Model_Backend_Category_Sorting
 */
class Yireo_GoogleTagManager_Model_Backend_Category_Sorting
{
    /**
     * @var Yireo_GoogleTagManager_Helper_Data
     */
    protected $helper;

    /**
     * Yireo_GoogleTagManager_Model_Observer constructor.
     */
    public function __construct()
    {
        $this->helper = Mage::helper('googletagmanager');
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'url', 'label'=> $this->helper->__('URL interpretation')),
            array('value' => 'block', 'label'=> $this->helper->__('Original block sorting')),
        );
    }

}
