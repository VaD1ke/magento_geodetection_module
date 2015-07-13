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
class Oggetto_GeoDetection_Adminhtml_Config_LocationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Display management settings for geodetection
     *
     * @return void
     */
    public function indexAction()
    {
        $countryCode = $this->getRequest()->getParam('country_code');

        $this->loadLayout();
        $this->getLayout()->getBlock('content.geodetection_management')->assign([
            'countryCode' => $countryCode,
        ]);
        $this->renderLayout();
    }

    /**
     * Save regions relations action
     *
     * @return $this|Mage_Core_Controller_Varien_Action
     *
     * @throws Exception
     */
    public function saveAction()
    {
        $post = $this->getRequest()->getPost('data');


        /** @var Oggetto_GeoDetection_Model_Location_Relation $relationModel */
        $relationModel = Mage::getModel('oggetto_geodetection/location_relation');

        $relationModel->clearByCountryCode($post['country_code']);

        $data = [];
        unset($post['country_code']);

        foreach ($post as $regionId => $iplocationRegions) {
            foreach ($iplocationRegions as $iplocationRegionName) {
                $data[] = ['directory_region_id' => $regionId, 'iplocation_region' => $iplocationRegionName];
            }
        }

        try {
            $relationModel->insertMultiple($data);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        return $this->_redirect('*/*/');
    }
}
