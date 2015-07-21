<?php
/**
 * Oggetto Geo Detection extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto Geo Detection module to newer versions in the future.
 * If you wish to customize the Oggetto Geo Detection module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @copyright  Copyright (C) 2015 Oggetto Web (http://oggettoweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipping handler Model
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Shipping_Handler
{
    /**
     * Fake destination postcode for shipping calculation
     */
    const FAKE_DEST_POSTCODE = '12345';

    /**
     * Fake products quantity for shipping calculation
     */
    const FAKE_PRODUCTS_QTY = 1;

    /**
     * Extmage Shipcc shipping method code
     */
    const EXTMAGE_SHIPCC_METHOD_CODE = 'shipcc';


    /**
     * Get shipping results
     *
     * @param array                      $methods Methods
     * @param Mage_Catalog_Model_Product $product Product
     *
     * @return array
     */
    public function getShippingResults($methods, $product)
    {
        $requestData = $this->_prepareDataForRequest($this->_getCheapestSimpleProduct($product));

        $calculation = [];

        foreach ($methods as $methodCode) {
            /** @var Mage_Shipping_Model_Carrier_Abstract $carrier */
            $carrier = Mage::getModel('shipping/shipping')->getCarrierByCode($methodCode);

            if ($carrier) {
                if ($methodCode == self::EXTMAGE_SHIPCC_METHOD_CODE) {
                    $requestData['dest_city'] = $this->_convertToShipccCity($requestData['dest_city']);
                }

                $result = $carrier->collectRates($this->_prepareRequest($requestData));

                if ($result && !$result->getError()) {
                    $shippingRates = $result->getAllRates();

                    foreach ($shippingRates as $rate) {
                        $calculation[$rate->getCarrierTitle()][$rate->getMethodTitle()] = [
                            'price' => $rate->getPrice()
                        ];
                    }
                }
            }
        }

        return $calculation;
    }

    /**
     * Prepare request
     *
     * @param array $data Data
     *
     * @return Mage_Shipping_Model_Rate_Request
     */
    protected function _prepareRequest($data)
    {
        /** @var Mage_Shipping_Model_Rate_Request $request */
        $request = Mage::getModel('shipping/rate_request');

        $request->setDestCity($data['dest_city']);
        $request->setDestCountryId($data['dest_country_id']);
        $request->setDestRegionId($data['dest_region_id']);
        $request->setDestRegion($data['dest_region']);
        $request->setDestPostcode($data['dest_postcode']);

        $request->setPackageWeight($data['product_weight']);
        $request->setPackageQty($data['product_qty']);
        $request->setPackageValue($data['package_value']);
        $request->setFreeMethodWeight($data['freemethod_weight']);

        $request->setStoreId($data['store_id']);
        $request->setWebsiteId($data['website_id']);

        return $request;
    }

    /**
     * Get cheapest simple product
     *
     * @param Mage_Catalog_Model_Product $product Product
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getCheapestSimpleProduct($product)
    {
        $typeId = $product->getTypeId();

        $cheapestProduct = $product;

        if ($typeId == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            /** @var Mage_Catalog_Model_Product_Type_Configurable $modelConfigurable */
            $modelConfigurable = Mage::getModel('catalog/product_type_configurable');

            $childProducts = $modelConfigurable->getUsedProductIds($product);

            $cheapestProduct = $this->_getCheapestProduct($childProducts);
        }

        return $cheapestProduct;
    }

    /**
     * Prepare data for shipping request
     *
     * @param Mage_Catalog_Model_Product $product Product
     *
     * @return mixed
     */
    protected function _prepareDataForRequest($product)
    {
        /** @var Mage_Core_Model_Cookie $cookieModel */
        $cookieModel = Mage::getSingleton('core/cookie');

        $locationCookie = $cookieModel->get(Oggetto_GeoDetection_Block_Header_Location::LOCATION_COOKIE_NAME);

        $cookie = Mage::helper('oggetto_geodetection')->jsonDecode($locationCookie);

        /** @var Mage_Directory_Model_Region $region */
        $region = Mage::getModel('directory/region')->load($cookie['region_id']);

        $requestData = [
            'dest_city'         => $cookie['city'],
            'dest_region'       => $region->getName(),
            'dest_region_id'    => $cookie['region_id'],
            'dest_country_id'   => $cookie['country'],
            'product_weight'    => $product->getData('weight'),
            'product_qty'       => self::FAKE_PRODUCTS_QTY,
            'dest_postcode'     => self::FAKE_DEST_POSTCODE,
            'package_value'     => $product->getData('price'),
            'freemethod_weight' => $product->getData('weight'),
            'website_id'        => Mage::app()->getWebsite()->getId(),
            'store_id'          => Mage::app()->getStore()->getId(),
        ];

        return $requestData;
    }

    /**
     * Convert to shipcc city
     *
     * @param string $city City
     *
     * @return string
     */
    protected function _convertToShipccCity($city)
    {
        $cityCode = Mage::helper('oggetto_geodetection/translator')->convertToDirectoryCityCode($city);

        /** @var Oggetto_Shipping_Model_City $model */
        $model = Mage::getModel('oggetto_shipping/city');

        $directoryCity = $model->loadByCode($cityCode);

        return $directoryCity->getName();
    }


    /**
     * Get cheapest product
     *
     * @param array $productIds Product IDs
     *
     * @return Mage_Catalog_Model_Product
     */
    private function _getCheapestProduct($productIds)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToSelect(['price', 'weight']);

        /** @var Mage_Catalog_Model_Product $cheapestProduct */
        $cheapestProduct = $collection->getFirstItem();

        $price = $cheapestProduct->getFinalPrice();

        /** @var Mage_Catalog_Model_Product $product */
        foreach ($collection as $product) {
            if ($product->getFinalPrice() < $price || is_null($price)) {
                $cheapestProduct = $product;
            }
        }

        return $cheapestProduct;
    }
}
