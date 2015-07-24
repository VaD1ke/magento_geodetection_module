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
 * Directory converter
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Directory_Converter
{
    /**
     * Convert location to directory regions
     *
     * @param array $locationsData Locations data
     *
     * @return array
     */
    public function convertLocationsToDirectoryRegions($locationsData)
    {
        /** @var Oggetto_GeoDetection_Model_Directory_Region_Fetcher $directoryFetcher */
        $directoryFetcher = Mage::getModel('oggetto_geodetection/directory_region_fetcher');

        $returnLocation = [];

        foreach ($locationsData as $region => $location) {
            $directoryRegion = $directoryFetcher->getRegionByIplocationRegionName($region);

            if ($directoryRegion) {
                foreach ($location as $city) {
                    $convertedCity = Mage::getModel('oggetto_geodetection/directory_city_converter')
                                     ->convertToDirectoryCity($city);

                    if (!$convertedCity) {
                        continue;
                    }

                    $returnLocation[$directoryRegion['default_name']]['cities'][] = $convertedCity;
                    $returnLocation[$directoryRegion['default_name']]['id'] = $directoryRegion['region_id'];
                }
            }
        }

        return $returnLocation;
    }

    /**
     * Convert location
     *
     * @param string  $city     City
     * @param integer $regionId Region ID
     *
     * @return array
     */
    public function convertLocation($city, $regionId)
    {
        /** @var Oggetto_GeoDetection_Model_Directory_City_Converter $cityConverter */
        $cityConverter = Mage::getModel('oggetto_geodetection/directory_city_converter');

        $convertedCity     = $cityConverter->convertToDirectoryCity($city);
        $convertedRegionId = $cityConverter->convertRegionId($city, $regionId);

        return [
            'city'      => $convertedCity,
            'region_id' => $convertedRegionId,
        ];
    }
}
