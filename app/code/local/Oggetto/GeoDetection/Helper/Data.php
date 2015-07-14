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
 * Helper Data
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Helper
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Get selected shipping methods
     *
     * @return array
     */
    public function getSelectedShippingMethods()
    {
        return explode(',', Mage::getStoreConfig('oggetto_geodetection_options/general/select_shipping'));
    }

    /**
     * Get directory regions
     *
     * @param string $countryCode Country code
     *
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    public function getDirectoryRegions($countryCode)
    {
        /** @var Mage_Directory_Model_Resource_Region_Collection $collection */
        $collection = Mage::getModel('directory/region')->getResourceCollection();
        $collection->addCountryFilter($countryCode)->addFieldToSelect(['region_id', 'default_name'])->load();
        return $collection->getData();
    }

    /**
     * Get directory region ID by name
     *
     * @param string $regionName Region Name
     *
     * @return null|mixed
     */
    public function getDirectoryRegionIdByName($regionName)
    {
        /** @var Mage_Directory_Model_Resource_Region_Collection $collection */
        $collection = Mage::getModel('directory/region')->getResourceCollection();
        $collection->addRegionNameFilter($regionName)->addFieldToSelect(['region_id'])->load();

        $data = $collection->getData();
        if (!$data) {
            return null;
        }
        return $data[0]['region_id'];
    }

    /**
     * Get directory region by iplocation region name
     *
     * @param string $regionName Region name
     *
     * @return null|string
     */
    public function getDirectoryRegionByIplocationRegionName($regionName)
    {
        /** @var Oggetto_GeoDetection_Model_Location_Relation $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation');

        $directoryRegionId = $relationModel->getRegionIdByIplocationRegionName($regionName);

        if (!$directoryRegionId) {
            return null;
        }

        $data = Mage::getModel('directory/region')->load($directoryRegionId)->getData();

        if (!$data) {
            return null;
        }

        return $data;
    }

    /**
     * Convert location to directory regions
     *
     * @param array $locationsData Locations data
     *
     * @return array
     */
    public function convertLocationToDirectoryRegions($locationsData)
    {
        $returnLocation = [];

        foreach ($locationsData as $region => $location) {
            $directoryRegion = $this->getDirectoryRegionByIplocationRegionName($region);

            if ($directoryRegion) {
                foreach ($location as $city) {
                    $returnLocation[$directoryRegion['default_name']]['cities'][] = $city;
                    $returnLocation[$directoryRegion['default_name']]['id'] = $directoryRegion['region_id'];
                }
            }
        }

        return $returnLocation;
    }

    /**
     * Prepare data for shipping request
     *
     * @param Mage_Catalog_Model_Product $product Product
     *
     * @return mixed
     */
    public function prepareDataForShippingRequest($product)
    {
        /** @var Mage_Core_Model_Cookie $cookieModel */
        $cookieModel = Mage::getSingleton('core/cookie');

        $locationCookie = $cookieModel->get(Oggetto_GeoDetection_Block_Header_Location::LOCATION_COOKIE_NAME);

        $cookie = $this->jsonDecode($locationCookie);

        /** @var Mage_Directory_Model_Region $region */
        $region = Mage::getModel('directory/region')->load($cookie['region_id']);

        $requestData = [
            'dest_city'         => $cookie['city'],
            'dest_region'       => $region->getName(),
            'dest_region_id'    => $cookie['region_id'],
            'dest_country_id'   => $cookie['country'],
            'product_weight'    => $product->getData('weight'),
            'product_qty'       => Oggetto_GeoDetection_Block_Shipping_Calculator::FAKE_PRODUCTS_QTY,
            'dest_postcode'     => Oggetto_GeoDetection_Block_Shipping_Calculator::FAKE_DEST_POSTCODE,
            'package_value'     => $product->getData('price'),
            'freemethod_weight' => $product->getData('weight'),
            'website_id'        => Mage::app()->getWebsite()->getId(),
            'store_id'          => Mage::app()->getStore()->getId(),
        ];

        return $requestData;
    }

    /**
     * Get all countries
     *
     * @return mixed
     */
    public function getAllCountries()
    {
        $countryList = Mage::getModel('directory/country')->getResourceCollection()
            ->loadByStore()
            ->toOptionArray(false);

        return $countryList;
    }

    /**
     * Get cheapest simple product
     *
     * @param Mage_Catalog_Model_Product $product Product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getCheapestSimpleProduct($product)
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
     * Get cheapest product
     *
     * @param array $productIds Product IDs
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getCheapestProduct($productIds)
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
