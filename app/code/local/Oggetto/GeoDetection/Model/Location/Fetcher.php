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
 * Location Fetcher Model
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Location_Fetcher extends Mage_Core_Model_Abstract
{
    /**
     * Get location by IP
     *
     * @param string $ipAddress IP address
     *
     * @return mixed
     */
    public function getLocationByIp($ipAddress)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_collection');

        $collection->getLocationInIpRange($ipAddress);

        $data = $collection->getFirstItem()->getData();

        /** @var Oggetto_GeoDetection_Model_Location_Relation_Fetcher $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation_fetcher');

        if ( array_key_exists('region_name', $data) && $relationModel->isRegionConnected($data['region_name']) ) {
            return $data;
        }

        return null;
    }

    /**
     * Get regions and cities by country code
     *
     * @param string $countryCode   Country code
     * @param bool   $onlyConnected Select only connected with directory regions
     *
     * @return array
     */
    public function getRegionsAndCities($countryCode = null, $onlyConnected = false)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_collection');

        $collection->selectRegionsAndCities()
            ->groupByRegionAndCity()
            ->orderRegionNameAndByIpCount();

        if ($countryCode) {
            $collection->filterByCountryCode($countryCode);
        }

        if ($onlyConnected) {
            $collection->innerJoinWithRelations();
        }
        $locations = $collection->getData();

        $returnLocations = [];

        foreach ($locations as $location) {
            $returnLocations[$location['region_name']][] = $location['city_name'];
        }

        return $returnLocations;
    }

    /**
     * Get popular regions and cities by country code
     *
     * @param string $countryCode Country code
     *
     * @return array
     */
    public function getPopularLocations($countryCode)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_collection');

        $locations = $collection->selectRegionsAndCities(
            Oggetto_GeoDetection_Model_System_Config_Source_Location_Cities::DEFAULT_CITIES_QTY_IN_SELECT
        )->innerJoinWithRelations()
         ->filterByCountryCode($countryCode)
         ->groupByRegionAndCity()
         ->orderByIpCount()
         ->getData();

        $returnLocations = [];

        foreach ($locations as $location) {
            $returnLocations[$location['region_name']][] = $location['city_name'];
        }

        return $returnLocations;
    }

    /**
     * Get regions
     *
     * @param string $countryCode Country code
     *
     * @return array
     */
    public function getRegions($countryCode = null)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_collection');
        $collection->selectRegions();

        if ($countryCode) {
            $collection->filterByCountryCode($countryCode);
        }

        $regions = $collection->getData();

        foreach ($regions as $index => $region) {
            $regions[$index] = $region['region_name'];
        }

        return $regions;
    }

    /**
     * Get regions that not in relations
     *
     * @param string $countryCode Country code
     *
     * @return array
     */
    public function getNotConnectedRegions($countryCode)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_collection');

        /** @var Oggetto_GeoDetection_Model_Location_Relation_Fetcher $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation_fetcher');

        $iplocationRegions = $relationModel->getAllIplocationRegionNames($countryCode);

        $collection->selectRegions()->filterByCountryCode($countryCode);

        if ($iplocationRegions) {
            $collection->filterRegionsNotIn($iplocationRegions);
        }

        $regions = $collection->getData();

        foreach ($regions as $index => $region) {
            $regions[$index] = $region['region_name'];
        }

        return $regions;
    }
}
