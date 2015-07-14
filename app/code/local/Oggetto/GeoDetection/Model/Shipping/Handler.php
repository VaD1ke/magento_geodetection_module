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
 * Shipping handler Model
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Shipping_Handler
{
    /**
     * Get shipping results
     *
     * @param array $methods     Methods
     * @param array $requestData Request data
     *
     * @return array
     */
    public function getShippingResults($methods, $requestData)
    {
        $calculation = [];

        foreach ($methods as $methodCode) {
            /** @var Mage_Shipping_Model_Carrier_Abstract $carrier */
            $carrier = Mage::getModel('shipping/shipping')->getCarrierByCode($methodCode);

            $result = $carrier->collectRates($this->_prepareRequest($requestData));

            if ($result && !$result->getError()) {
                $shippingRates = $result->getAllRates();

                foreach ($shippingRates as $rate) {
                    $calculation[$rate->getCarrierTitle()][$rate->getMethodTitle()] = [
                        'price' => $rate->getPrice()
                    ];
                }
            }
        }

        return $calculation;
    }

    /**
     * Prepare request
     *
     * @param array $data Data
     *
     * @return Mage_Shipping_Model_Rate_Request
     */
    protected function _prepareRequest($data)
    {
        /** @var Mage_Shipping_Model_Rate_Request $request */
        $request = Mage::getModel('shipping/rate_request');

        $request->setDestCity($data['dest_city']);
        $request->setDestCountryId($data['dest_country_id']);
        $request->setDestRegionId($data['dest_region_id']);
        $request->setDestRegion($data['dest_region']);
        $request->setDestPostcode($data['dest_postcode']);

        $request->setPackageWeight($data['product_weight']);
        $request->setPackageQty($data['product_qty']);
        $request->setPackageValue($data['package_value']);
        $request->setFreeMethodWeight($data['freemethod_weight']);

        $request->setStoreId($data['store_id']);
        $request->setWebsiteId($data['website_id']);

        return $request;
    }
}
