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
 * Class Yireo_GoogleTagManager_Block_Quote
 */
class Yireo_GoogleTagManager_Block_Quote extends Yireo_GoogleTagManager_Block_Default
{
    /**
     * @var $taxHelper Mage_Tax_Helper_Data
     */
    protected $taxHelper;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->taxHelper = Mage::helper('tax');

        parent::_construct();
    }

    /**
     * @return Mage_Sales_Model_Quote|null
     */
    public function getQuote()
    {
        $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        if (!empty($lastOrderId)) {
            return null;
        }

        $quote = Mage::getModel('checkout/cart')->getQuote();
        return $quote;
    }

    /**
     * Return all quote items as array
     *
     * @return array
     */
    public function getItemsAsArray()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $this->getQuote();
        $store = $this->getStore();
        if (empty($quote)) {
            return array();
        }

        $data = array();

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($quote->getAllVisibleItems() as $item) {

            $product = $item->getProduct();

            /** @var Mage_Tax_Model_Calculation $taxCalculation */
            $taxCalculation = Mage::getModel('tax/calculation');
            $request = $taxCalculation->getRateRequest(null, null, null, $store);
            $taxClassId = $product->getTaxClassId();
            $taxpercent = $taxCalculation->getRate($request->setProductClassId($taxClassId));

            $price = $product->getPrice();
            $specialPrice = $product->getSpecialprice();
            if (($specialPrice > 0) && ($specialPrice < $price)) {
                $price = $specialPrice;
            }

            $tax = ($price / (100 + $taxpercent)) * $taxpercent;

            $data[] = array(
                'id' => $product->getId(),
                'sku' => $this->quoteEscape($product->getSku()),
                'name' => $this->quoteEscape($product->getName()),
                'price' => $this->formatPrice($price),
                'priceexcludingtax' => number_format($price - $tax, 2),
                'tax' => number_format($tax, 2),
                'taxrate' => $taxpercent,
                'type' => $item->getProductType(),
                'category' => implode('|', $product->getCategoryIds()),
                'quantity' => $item->getQty(),
            );
        }

        return $data;
    }

    /**
     * Return all quote items as JSON
     *
     * @return string
     */
    public function getItemsAsJson()
    {
        return json_encode($this->getItemsAsArray());
    }

    /**
     * @return string
     */
    public function getWebsiteName()
    {
        return Mage::app()->getWebsite()->getName();
    }
}
