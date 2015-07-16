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
 * Directory fetcher
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Directory_Fetcher
{
    /**
     * Get regions
     *
     * @param string $countryCode Country code
     *
     * @return $this
     */
    public function getRegions($countryCode)
    {
        /** @var Oggetto_GeoDetection_Model_Resource_Directory_Region_Collection $collection */
        $collection = Mage::getResourceModel('oggetto_geodetection/directory_region_collection');

        return $collection->getRegions()->addCountryFilter($countryCode)->getData();
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
            $directoryRegion = $this->_getRegionByIplocationRegionName($region);

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
     * Get directory region by iplocation region name
     *
     * @param string $regionName Region name
     *
     * @return null|string
     */
    protected function _getRegionByIplocationRegionName($regionName)
    {
        /** @var Oggetto_GeoDetection_Model_Location_Relation_Fetcher $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation_fetcher');

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
}
