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
 * Location Model
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Location extends Mage_Core_Model_Abstract
{
    /**
     * Init object
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('oggetto_geodetection/location');
    }

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
        $collection = $this->getCollection();

        $collection->getLocationInIpRange($ipAddress);

        $data = $collection->getFirstItem()->getData();

        /** @var Oggetto_GeoDetection_Model_Location_Relation $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation');

        if ( $relationModel->isRegionConnected($data['region_name']) ) {
            return $data;
        }

        return null;
    }

    /**
     * Get regions and cities by country code
     *
     * @param string $countryCode Country code
     *
     * @return array
     */
    public function getRegionsAndCitiesByCountryCode($countryCode)
    {
        $locations = $this->getCollection()->selectRegionsAndCitiesByCountryCode($countryCode)->getData();

        $returnLocations = [];

        foreach ($locations as $location) {
            $returnLocations[$location['region_name']][] = $location['city_name'];
        }

        return $returnLocations;
    }

    /**
     * Get regions
     *
     * @return array
     */
    public function getRegions()
    {
        $regions = $this->getCollection()->selectRegions()->getData();

        foreach ($regions as $index => $region) {
            $regions[$index] = $region['region_name'];
        }

        return $regions;
    }

    /**
     * Get regions by country code
     *
     * @param string $countryCode Country code
     *
     * @return array
     */
    public function getRegionsByCountryCode($countryCode)
    {
        $regions = $this->getCollection()->selectRegionsByCountryCode($countryCode)->getData();

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
    public function getNotConnectedRegionsByCountryCode($countryCode)
    {
        /** @var Oggetto_GeoDetection_Model_Location_Relation $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation');

        $regions = $this->getCollection()->selectRegionsThatNotInRegionsArrayByCountryCode(
                $relationModel->getAllIplocationRegionNamesByCountryCode($countryCode), $countryCode
        )->getData();

        foreach ($regions as $index => $region) {
            $regions[$index] = $region['region_name'];
        }

        return $regions;
    }
}
