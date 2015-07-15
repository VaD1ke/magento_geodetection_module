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
}
