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
 * If you wish to customize the Oggetto GeoDetection module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @copyright  Copyright (C) 2015 Oggetto Web (http://oggettoweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block class for displaying user's location
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Block
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Block_Header_Location extends Mage_Page_Block_Html_Header
{
    const LOCATION_COOKIE_NAME = 'user_location';

    /**
     * Get user location
     *
     * @return array|null
     */
    public function getUserLocation()
    {
        /** @var Mage_Core_Model_Cookie $cookieModel */
        $cookieModel = Mage::getSingleton('core/cookie');

        $locationCookie = $cookieModel->get(self::LOCATION_COOKIE_NAME);

        /** @var Oggetto_GeoDetection_Helper_Data $helper */
        $helper = Mage::helper('oggetto_geodetection');

        /** @var Oggetto_GeoDetection_Model_Directory_Converter $converterModel */
        $converterModel = Mage::getModel('oggetto_geodetection/directory_converter');

        if (!$locationCookie) {
            $ipAddress = Mage::helper('core/http')->getRemoteAddr(true);

            $location = Mage::getModel('oggetto_geodetection/location_fetcher')->getLocationByIp($ipAddress);

            if (!$location || $location['city_name'] == '-') {

                $defaultCity = $helper->getDefaultCity();
                if (!$defaultCity || !$defaultCity[0]) {
                    return null;
                }

                $convertedLocation = $converterModel->convertLocation($defaultCity[0], $defaultCity[1]);

                $cookieData = [
                    'country'   => $helper->getDefaultCountry(),
                    'region_id' => $convertedLocation['region_id'],
                    'city'      => $convertedLocation['city'],
                ];

            } else {
                $regionId = Mage::getModel('oggetto_geodetection/location_relation_fetcher')
                            ->getRegionIdByIplocationRegionName($location['region_name']);

                $convertedLocation = $converterModel->convertLocation($location['city_name'], $regionId);

                if (!$convertedLocation['city']) {
                    return null;
                }

                $cookieData = [
                    'country'   => $location['country_code'],
                    'region_id' => $convertedLocation['region_id'],
                    'city'      => $convertedLocation['city'],
                ];
            }

            $cookieModel->set(self::LOCATION_COOKIE_NAME, $helper->jsonEncode($cookieData), 0, '/', null, null, false);

            return $cookieData;
        }

        return $helper->jsonDecode($locationCookie);
    }

    /**
     * Is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        /** @var Oggetto_GeoDetection_Model_Location_Relation_Fetcher $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation_fetcher');

        return !$relationModel->isCollectionEmpty($this->getDefaultCountry());
    }

    /**
     * Get default country code
     *
     * @return mixed
     */
    public function getDefaultCountry()
    {
        /** @var Oggetto_GeoDetection_Helper_Data $helper */
        $helper = Mage::helper('oggetto_geodetection');

        return $helper->getDefaultCountry();
    }
}
