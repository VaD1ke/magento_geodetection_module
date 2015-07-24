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
 * Directory city converter
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Directory_City_Converter
{
    /**
     * Convert to directory city
     *
     * @param string $city City
     *
     * @return string
     */
    public function convertToDirectoryCity($city)
    {
        /** @var Oggetto_Shipping_Model_City $model */
        $model = Mage::getModel('oggetto_shipping/city');

        if ($model) {
            $cityCode = Mage::helper('oggetto_geodetection/translator')->convertToDirectoryCityCode($city);

            $directoryCity = $model->loadByCode($cityCode);

            return $directoryCity ? $directoryCity->getName() : null;
        }

        return $city;
    }

    /**
     * Convert region id
     *
     * @param string  $city     City
     * @param integer $regionId Region ID
     *
     * @return mixed|null
     */
    public function convertRegionId($city, $regionId)
    {
        /** @var Oggetto_Shipping_Model_City $model */
        $model = Mage::getModel('oggetto_shipping/city');

        if ($model) {
            $cityCode = Mage::helper('oggetto_geodetection/translator')->convertToDirectoryCityCode($city);

            $directoryCity = $model->loadByCode($cityCode);

            if ($directoryCity) {
                return $directoryCity->getData('region_id');
            }
        }

        return $regionId;
    }
}
