<?php
/**
 * GoogleTagManager plugin for Magento
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2019 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_GoogleTagManager_Model_Product_Price
 */
class Yireo_GoogleTagManager_Model_Product_Price
{
    /**
     * @var Mage_Tax_Model_Calculation
     */
    protected $taxCalculation;

    /**
     * @var Mage_Tax_Helper_Data
     */
    protected $taxHelper;

    /**
     * @var Mage_Core_Model_Store
     */
    protected $store;

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $product;

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $childProduct;

    /**
     * Yireo_GoogleTagManager_Model_Price constructor.
     */
    public function __construct()
    {
        $this->taxCalculation = Mage::getModel('tax/calculation');
        $this->taxHelper = Mage::helper('tax');
        $this->store = Mage::app()->getStore();
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return Yireo_GoogleTagManager_Model_Product_Price
     */
    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return Yireo_GoogleTagManager_Model_Product_Price
     */
    public function setChildProduct(Mage_Catalog_Model_Product $childProduct)
    {
        $this->childProduct = $childProduct;
        return $this;
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getPrice()
    {
        $product = $this->getProduct();
        if ($product->getFinalPrice()) {
            return (float)$product->getFinalPrice();
        }

        if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            return $this->getBundledPrice('min', true);
        }

        $price = $product->getPrice();
        $specialPrice = $product->getSpecialprice();
        if ($specialPrice > 0 && $specialPrice < $price) {
            return (float)$specialPrice;
        }

        return (float)$price;
    }

    /**
     * @return float
     * @throws Exception
     */
    protected function getBundledPrice($which = 'min', $includeTax = true)
    {
        $product = $this->getProduct();

        /** @var Mage_Bundle_Model_Product_Price $bundlePriceModel */
        $bundlePriceModel = Mage::getModel('bundle/product_price');
        return (float) $bundlePriceModel->getTotalPrices($product, $which, $includeTax);
    }

    /**
     * @return int
     * @throws Exception
     */
    protected function getConfiguredPricePerChildProduct()
    {
        if (!$this->childProduct) {
            return 0;
        }

        $product = $this->getProduct();
        $block = Mage::app()->getLayout()->createBlock('catalog/product_view_type_configurable');
        $block->setProduct($product);
        $config = json_decode($block->getJsonConfig(), true);
        if (!isset($config['attributes'])) {
            return 0;
        }

        foreach ($config['attributes'] as $attribute) {
            foreach ($attribute['options'] as $option) {
                if (in_array($this->childProduct->getId(), $option['products'])) {
                    return $option['price'];
                }
            }
        }

        return 0;
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getPriceInclTax()
    {
        $product = $this->getProduct();
        $finalPrice = $product->getFinalPrice();

        if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            return $this->getBundledPrice('min', true);
        }

        if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && $this->childProduct) {
            $finalPrice += $this->getConfiguredPricePerChildProduct();
        }

        return $this->taxHelper->getPrice($product, $finalPrice, true);
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getPriceExclTax()
    {
        $product = $this->getProduct();
        $finalPrice = $product->getFinalPrice();

        if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            return $this->getBundledPrice('min', false);
        }

        if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && $this->childProduct) {
            $finalPrice += $this->getConfiguredPricePerChildProduct();
        }

        return $this->taxHelper->getPrice($product, $finalPrice, false);
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getTax()
    {
        return (float)$this->getPriceInclTax() - $this->getPriceExclTax();
    }

    /**
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     * @throws Exception
     */
    public function getTaxRate()
    {
        $request = $this->taxCalculation->getRateRequest(null, null, null, $this->store);
        $taxClassId = $this->getProduct()->getTaxClassId();
        $taxPercent = $this->taxCalculation->getRate($request->setProductClassId($taxClassId));
        return (float)$taxPercent;
    }

    /**
     * @return Mage_Catalog_Model_Product
     * @throws Exception
     */
    protected function getProduct()
    {
        if (!$this->product instanceof Mage_Catalog_Model_Product) {
            throw new Exception('Invalid product');
        }

        if (!$this->product->getId() > 0) {
            throw new Exception('Invalid product');
        }

        return $this->product;
    }
}
