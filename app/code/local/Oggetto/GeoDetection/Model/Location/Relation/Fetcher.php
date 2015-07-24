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
 * Location Relation Fetcher Model
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Location_Relation_Fetcher
{
    /**
     * Get ID by iplocation region name
     *
     * @param string $regionName Region Name
     *
     * @return mixed
     */
    public function getIdByIplocationRegionName($regionName)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_relation_collection');

        return $collection->getIdByIplocationRegionName($regionName)->getFirstItem()->getData()['id'];
    }

    /**
     * Get iplocation regions by directory region ID
     *
     * @param string $directoryRegionId Directory region ID
     *
     * @return array
     */
    public function getIplocationRegionsByDirectoryRegionId($directoryRegionId)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_relation_collection');

        $regions = $collection->getIplocationRegionsByDirectoryRegionId($directoryRegionId)->getData();

        $regionNames = [];

        foreach ($regions as $region) {
            $regionNames[] = $region['iplocation_region'];
        }

        return $regionNames;
    }

    /**
     * Get region ID by iplocation region name
     *
     * @param string $iplocationRegionName Iplocation region name
     *
     * @return mixed
     */
    public function getRegionIdByIplocationRegionName($iplocationRegionName)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_relation_collection');

        $data = $collection->getRegionIdByIplocationRegionName($iplocationRegionName)->getFirstItem()->getData();


        return array_key_exists('directory_region_id', $data) ? $data['directory_region_id'] : null;
    }

    /**
     * Clear by country code
     *
     * @param string $countryCode Country code
     *
     * @return void
     */
    public function clearByCountryCode($countryCode)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_relation_collection');

        $regions = $collection->selectByCountryCode($countryCode);

        /** @var Oggetto_GeoDetection_Model_Location_Relation $region */
        foreach ($regions as $region) {
            $region->delete();
        }
    }

    /**
     * Get all iplocation region names
     *
     * @param string $countryCode Country code
     *
     * @return array
     */
    public function getAllIplocationRegionNames($countryCode)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_relation_collection');

        $relations = $collection->selectByCountryCode($countryCode)->getData();

        $regions = [];

        foreach ($relations as $relation) {
            $regions[] = $relation['iplocation_region'];
        }

        return $regions;
    }

    /**
     * Get regions IDs
     *
     * @param string $countryCode Country code
     *
     * @return array
     */
    public function getRegionsIds($countryCode)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_relation_collection');

        $relations = $collection->selectByCountryCode($countryCode)->getData();

        $regionIds = [];

        foreach ($relations as $relation) {
            $regionIds[] = $relation['region_id'];
        }

        return $regionIds;
    }

    /**
     * Is region connected
     *
     * @param string $regionName Region name
     *
     * @return bool
     */
    public function isRegionConnected($regionName)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_relation_collection');

        $collection->getIdByIplocationRegionName($regionName);

        $data = $collection->getFirstItem()->getData();

        return !empty($data);
    }

    /**
     * Is collection empty
     *
     * @param string $countryCode Country code
     *
     * @return bool
     */
    public function isCollectionEmpty($countryCode)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_relation_collection');

        $size = $collection->selectByCountryCode($countryCode)->getSize();

        return !$size;
    }

    /**
     * Insert multiple
     *
     * @param array $rows Rows
     *
     * @return void
     */
    public function insertMultiple($rows)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/location_relation_collection');

        $collection->insertMultiple($rows);
    }
}
