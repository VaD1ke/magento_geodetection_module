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
 * Directory region fetcher
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Directory_Region_Fetcher
{
    /**
     * Get regions
     *
     * @param string $countryCode Country code
     * @param array  $regionIds   Region IDs
     *
     * @return array
     */
    public function getRegions($countryCode = null, $regionIds = null)
    {
        /** @var Mage_Directory_Model_Resource_Region_Collection $collection */
        $collection = Mage::getResourceModel('directory/region_collection');

        if ($countryCode) {
            $collection->addCountryFilter($countryCode);
        }

        if ($regionIds) {
            $collection->addFieldToFilter('main_table.region_id', ['in' => $regionIds]);
        }

        return $collection->getData();
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

        if (!$data) {
            return null;
        }

        return $data;
    }

}
