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
 * Block class for Geo Detection management
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Block
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Block_Adminhtml_Management extends Mage_Adminhtml_Block_Template
{
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

    /**
     * Get regions from iplocation that missing in relations
     *
     * @param string $countryCode Country code
     *
     * @return array
     */
    public function getNotConnectedIplocationRegions($countryCode)
    {
        /** @var Oggetto_GeoDetection_Model_Location_Fetcher $model */
        $model = Mage::getModel('oggetto_geodetection/location_fetcher');

        return $model->getNotConnectedRegionsByCountryCode($countryCode);
    }

    /**
     * Get directory regions by country code
     *
     * @param string $countryCode Country code
     *
     * @return array
     */
    public function getDirectoryRegions($countryCode)
    {
        /** @var Oggetto_GeoDetection_Model_Directory_Fetcher $modelFetcher */
        $modelFetcher = Mage::getModel('oggetto_geodetection/directory_fetcher');

        return $modelFetcher->getRegions($countryCode);
    }

    /**
     * Get iplocation regions by directory region ID
     *
     * @param string $directoryRegionId Directory Region ID
     *
     * @return array
     */
    public function getIplocationRegions($directoryRegionId)
    {
        /** @var Oggetto_GeoDetection_Model_Location_Relation $model */
        $model = Mage::getModel('oggetto_geodetection/location_relation');

        return $model->getIplocationRegionsByDirectoryRegionId($directoryRegionId);
    }

    /**
     * Get URL for regions relation saving
     *
     * @return mixed
     */
    public function getUrlForRelationsSaving()
    {
        return $this->getUrl('adminhtml/config_location/save');
    }

    /**
     * Get all countries
     *
     * @return mixed
     */
    public function getAllCountries()
    {
        /** @var Oggetto_GeoDetection_Model_Directory_Fetcher $modelFetcher */
        $modelFetcher = Mage::getModel('oggetto_geodetection/directory_fetcher');

        return $modelFetcher->getAllCountries();
    }
}
