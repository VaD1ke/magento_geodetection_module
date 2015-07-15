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

        if (!$locationCookie) {
            $ipAddress = Mage::helper('core/http')->getRemoteAddr(true);

            /** @var Oggetto_GeoDetection_Model_Location_Fetcher $locationModel */
            $locationModel = Mage::getModel('oggetto_geodetection/location_fetcher');

            $location = $locationModel->getLocationByIp($ipAddress);

            if (!$location || $location['city_name'] == '-') {
                return null;
            }

            /** @var Oggetto_GeoDetection_Model_Location_Relation $relationModel */
            $relationModel = Mage::getModel('oggetto_geodetection/location_relation');

            $cookieData = [
                'country'   => $location['country_code'],
                'region_id' => $relationModel->getRegionIdByIplocationRegionName($location['region_name']),
                'city'      => $location['city_name']
            ];

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
        /** @var Oggetto_GeoDetection_Model_Location_Relation $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation');

        return !$relationModel->isCollectionEmpty(Mage::helper('oggetto_geodetection')->getDefaultCountry());
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
