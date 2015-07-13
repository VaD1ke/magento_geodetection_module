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
 * Controller for getting user's location
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage controllers
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_LocationController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get locations (regions and cities)
     *
     * @return void
     */
    public function indexAction()
    {
        /** @var Oggetto_GeoDetection_Helper_Data $helper */
        $helper = Mage::helper('oggetto_geodetection');

        $countryCode = $this->getRequest()->getPost('country_code');
        if (!$countryCode) {
            $countryCode = $helper->getDefaultCountry();
        }

        /** @var Oggetto_Ajax_Model_Response $response */
        $response = Mage::getModel('ajax/response');

        /** @var Oggetto_Ajax_Helper_Data $helperAjax */
        $helperAjax = Mage::helper('ajax');

        /** @var Oggetto_GeoDetection_Model_Location $model */
        $model = Mage::getModel('oggetto_geodetection/location');

        try {
            $locations = $model->getRegionsAndCitiesByCountryCode($countryCode);
            $locations = $helper->convertLocationToDirectoryRegions($locations);
            $locations = Mage::helper('core')->jsonEncode($locations);

        } catch (Exception $e) {
            $response->error();
            $helperAjax->sendResponse($response);
            return;
        }

        $response->success()->setData(['locations' => $locations, 'status' => 'success']);

        $helperAjax->sendResponse($response);
    }
}
