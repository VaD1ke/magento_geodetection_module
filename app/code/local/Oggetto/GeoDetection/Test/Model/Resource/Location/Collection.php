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
 * Location collection test class
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Model_Resource_Location_Collection extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Location collection
     *
     * @var Oggetto_GeoDetection_Model_Resource_Location_Collection
     */
    protected $_collection;

    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_collection = Mage::getModel('oggetto_geodetection/location')->getCollection();
    }

    /**
     * Checks model name of collection
     *
     * @return void
     */
    public function testChecksModelName()
    {
        $this->assertEquals('oggetto_geodetection/location', $this->_collection->getModelName());
    }

    /**
     * Return location searching Ip in range
     *
     * @return void
     *
     * @loadFixture testLocationsCollection
     */
    public function testReturnsLocationSearchingIpInRange()
    {
        $longIp = 12345678;

        $this->assertEquals(
            $this->expected('location')->getData()[0],
            $this->_collection->getLocationInIpRange($longIp)->getData()[0]
        );
    }

    /**
     * Return distinct regions
     *
     * @return void
     *
     * @loadFixture testLocationsCollection
     */
    public function testReturnsDistinctRegions()
    {
        $this->assertEquals(
            $this->expected('regions')->getData(),
            $this->_collection->selectRegions()->getData()
        );
    }

    /**
     * Return distinct regions and cities
     *
     * @return void
     *
     * @loadFixture testLocationsCollection
     */
    public function testReturnsDistinctRegionsAndCities()
    {
        $this->assertEquals(
            $this->expected('regions_cities')->getData(),
            $this->_collection->selectRegionsAndCities()->getData()
        );
    }

    /**
     * Return regions that are not in established regions array
     *
     * @return void
     *
     * @loadFixture testLocationsCollection
     */
    public function testReturnsRegionsThatAreNotInEstablishedRegionsArray()
    {
        $regionsArray = ['region1', 'region3'];

        $this->assertEquals(
            $this->expected('regions')->getData(),
            $this->_collection->selectRegionsThatNotInRegionsArray($regionsArray)->getData()
        );
    }
}
