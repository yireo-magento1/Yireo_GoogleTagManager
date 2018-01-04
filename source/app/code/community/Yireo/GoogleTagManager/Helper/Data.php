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

    public function getProductPrice($product)
    {
        if($product->getFinalPrice()) {
            return $product->getFinalPrice();
        } else if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $optionCol= $product->getTypeInstance(true)
                ->getOptionsCollection($product);
            $selectionCol= $product->getTypeInstance(true)
                ->getSelectionsCollection(
                    $product->getTypeInstance(true)->getOptionsIds($product),
                    $product
                );
            $optionCol->appendSelections($selectionCol);
            $price = $product->getPrice();

            foreach ($optionCol as $option) {
                if($option->required) {
                    $selections = $option->getSelections();
                    $minPrice = min(array_map(function ($s) {
                        if ($s->specialPrice > 0)
                        {
                            $price = $s->specialPrice;
                        } else
                        {
                            $price = $s->price;
                        }
                        return $price;
                    }, $selections));
                    if($product->getSpecialPrice() > 0) {
                        $minPrice *= $product->getSpecialPrice()/100;
                    }

                    $price += round($minPrice,2);
                }
            }
            return  $price;
        } else {
            return "";
        }
    }
}
