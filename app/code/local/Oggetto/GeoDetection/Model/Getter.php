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
 * Location getter
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Getter
{
    /**
     * Get locations (regions and cities)
     *
     * @param string|null $countryCode   Country code
     * @param bool|null   $onlyConnected Select only connected with directory regions
     *
     * @return array
     */
    public function getLocations($countryCode = null, $onlyConnected = false)
    {
        /** @var Oggetto_GeoDetection_Model_Directory_Fetcher $directoryFetcher */
        $directoryFetcher = Mage::getModel('oggetto_geodetection/directory_fetcher');

        $locations = $directoryFetcher->getRegionsAndCities($countryCode, $onlyConnected);

        if (!$locations) {
            /** @var Oggetto_GeoDetection_Model_Directory_Converter $converterModel */
            $converterModel = Mage::getModel('oggetto_geodetection/directory_converter');

            /** @var Oggetto_GeoDetection_Model_Location_Fetcher $locationModel */
            $locationModel = Mage::getModel('oggetto_geodetection/location_fetcher');

            $locations = $locationModel->getRegionsAndCities($countryCode, $onlyConnected);
            $locations = $converterModel->convertLocationsToDirectoryRegions($locations);
        }

        return $locations;
    }
}
