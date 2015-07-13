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
 * Test class for helper data
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Helper Data
     *
     * @var Oggetto_GeoDetection_Helper_Data
     */
    protected $_helper;

    /**
     * Set Up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_helper = Mage::helper('oggetto_geodetection');
    }

    /**
     * Return selected shipping methods from store config
     *
     * @return void
     *
     * @loadFixture
     */
    public function testReturnsSelectedShippingMethodsFromStoreConfig()
    {
        $this->assertEquals(['test method', 'test method2'], $this->_helper->getSelectedShippingMethods());
    }

    /**
     * Return directory regions by country code
     *
     * @return void
     *
     * @loadFixture testDirectoryRegions
     */
    public function testReturnsDirectoryRegionsByCountryCode()
    {
        $countryCode = 'id1';

        $this->assertEquals($this->expected('regions')->getData(), $this->_helper->getDirectoryRegions($countryCode));
    }
}
