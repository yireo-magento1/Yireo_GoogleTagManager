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
 * Class Yireo_GoogleTagManager_Helper_Data
 */
class Yireo_GoogleTagManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Ecommerce data
     */
    protected $ecommerceData = array();

    /**
     * Check whether the module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if ((bool)Mage::getStoreConfig('advanced/modules_disable_output/Yireo_GoogleTagManager')) {
            return false;
        }

        if (!(bool)$this->getConfigValue('active', false)) {
            return false;
        }

        if (!$this->getId()) {
            return false;
        }

        return true;
    }

    /**
     * Check whether the module is in debugging mode
     *
     * @return bool
     */
    public function isDebug()
    {
        return (bool)$this->getConfigValue('debug');
    }

    /**
     * Return the GA ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->getConfigValue('id');
    }

    /**
     * Debugging method
     *
     * @param $string
     * @param null $variable
     *
     * @return bool
     */
    public function debug($string, $variable = null)
    {
        if ($this->isDebug() == false) {
            return false;
        }

        if (!empty($variable)) {
            $string .= ': ' . var_export($variable, true);
        }

        Mage::log($string, NULL, 'googletagmanager.log');
        return true;
    }

    /**
     * Return a configuration value
     *
     * @param null $key
     * @param null $default_value
     *
     * @return mixed|null
     */
    public function getConfigValue($key = null, $default_value = null)
    {
        $value = Mage::getStoreConfig('googletagmanager/settings/' . $key);

        if (empty($value)) {
            $value = $default_value;
        }

        return $value;
    }

    /**
     * @param $product
     * @return float
     * @throws Exception
     * @deprecated Use Yireo_GoogleTagManager_Model_Product_Price::getPrice() instead
     */
    public function getProductPrice($product)
    {
        /** @var Yireo_GoogleTagManager_Model_Product_Price $priceModel */
        $priceModel = Mage::getModel('googletagmanager/product_price');
        $priceModel->setProduct($product);
        return $priceModel->getPrice();
    }

    /**
     * Returns whether environments is enabled
     *
     * @return bool
     */
    public function getIsEnvironmentEnabled()
    {
        return (bool) $this->getConfigValue('environment_active');
    }

    /**
     * Get the auth value
     *
     * @return string|null
     */
    public function getEnvironmentAuth()
    {
        return $this->getConfigValue('auth');
    }

    /**
     * Get the preview value
     *
     * @return string|null
     */
    public function getEnvironmentPreview()
    {
        return $this->getConfigValue('preview');
    }
}
