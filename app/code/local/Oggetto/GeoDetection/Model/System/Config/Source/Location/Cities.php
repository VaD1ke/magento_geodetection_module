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
 * Source model for choosing default city
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_System_Config_Source_Location_Cities
{
    /**
     * Default cities quantity in select
     */
    const DEFAULT_CITIES_QTY_IN_SELECT = 20;

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Oggetto_GeoDetection_Model_Location_Fetcher $locationModel */
        $locationModel = Mage::getModel('oggetto_geodetection/location_fetcher');

        /** @var Oggetto_GeoDetection_Model_Location_Relation $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation');

        /** @var Oggetto_GeoDetection_Helper_Data $helper */
        $helper = Mage::helper('oggetto_geodetection');

        $locations = $locationModel->getPopularLocations($helper->getDefaultCountry());


        $cities = [];

        foreach ($locations as $region => $location) {
            if (array_key_exists(0, $location) && $location[0]) {
                $regionId = $relationModel->getRegionIdByIplocationRegionName($region);

                $cities[] = [
                    'value' => $location[0] . ':' . $regionId,
                    'label' => $location[0],
                ];
            }
        }

        array_unshift($cities, array('value' => '', 'label' => Mage::helper('adminhtml')->__('--Please Select--')));

        return $cities;
    }
}
