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
     * Get directory region by iplocation region name
     *
     * @param string $regionName Region name
     *
     * @return null|string
     */
    public function getRegionByIplocationRegionName($regionName)
    {
        /** @var Oggetto_GeoDetection_Model_Location_Relation_Fetcher $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation_fetcher');

        $directoryRegionId = $relationModel->getRegionIdByIplocationRegionName($regionName);

        if (!$directoryRegionId) {
            return null;
        }

        $data = Mage::getModel('directory/region')->load($directoryRegionId)->getData();

        return $data ? $data : null;
    }

    /**
     * Get regions and cities
     *
     * @param string|null $countryCode   Country code
     * @param bool|null   $onlyConnected Select only connected with iplocations regions
     *
     * @return array
     */
    public function getRegionsAndCities($countryCode = null, $onlyConnected = null)
    {
        /** @var Oggetto_Shipping_Model_Resource_City_Collection $cityCollection */
        $cityCollection = Mage::getResourceModel('oggetto_shipping/city_collection');
        $returnLocation = [];

        if ($cityCollection) {
            /** @var Oggetto_GeoDetection_Model_Directory_Region_Fetcher $regionFetcher */
            $regionFetcher = Mage::getModel('oggetto_geodetection/directory_region_fetcher');

            $regionsIds = null;
            if ($onlyConnected) {
                $regionsIds = Mage::getModel('oggetto_geodetection/location_relation_fetcher')
                    ->getRegionsIds($countryCode);
            }

            $regions = $regionFetcher->getRegions($countryCode, $regionsIds);
            $cities  = $cityCollection->getData();

            foreach ($regions as $region) {
                foreach ($cities as $city) {
                    if ($region['region_id'] == $city['region_id']) {
                        $returnLocation[$region['default_name']]['cities'][] = $city['default_name'];
                        $returnLocation[$region['default_name']]['id'] = $region['region_id'];
                    }
                }
            }
        }

        return $returnLocation;
    }
}
