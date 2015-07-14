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
 * Block class for getting shipping params (price and time) in product view
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Block
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Block_Shipping_Calculator extends Mage_Core_Block_Template
{
    /**
     * Fake destination postcode for shipping calculation
     */
    const FAKE_DEST_POSTCODE = '12345';

    /**
     * Fake products quantity for shipping calculation
     */
    const FAKE_PRODUCTS_QTY = 1;

    /**
     * Product
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Set product
     *
     * @param Mage_Catalog_Model_Product $product Product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_product = $product;

        return $this;
    }

    /**
     * Get product weight
     *
     * @return array
     */
    public function getDeliveryPriceAndTime()
    {
        /** @var Oggetto_GeoDetection_Helper_Data $helper */
        $helper = Mage::helper('oggetto_geodetection');

        $shippingMethods = $helper->getSelectedShippingMethods();
        $product         = $helper->getCheapestSimpleProduct($this->_getProduct());
        $requestData     = $helper->prepareDataForShippingRequest($product);

        /** @var Oggetto_GeoDetection_Model_Shipping_Handler $shippingHandler */
        $shippingHandler = Mage::getModel('oggetto_geodetection/shipping_handler');

        $calculation = $shippingHandler->getShippingResults($shippingMethods, $requestData);

        return $calculation;
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
     * Get product
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return $this->_product;
    }
}
