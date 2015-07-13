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
 * Location relation collection test class
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Model_Resource_Location_Relation_Collection extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Location relation collection
     *
     * @var Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection
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
        $this->_collection = Mage::getModel('oggetto_geodetection/location_relation')->getCollection();
    }

    /**
     * Checks model name of collection
     *
     * @return void
     */
    public function testChecksModelName()
    {
        $this->assertEquals('oggetto_geodetection/location_relation', $this->_collection->getModelName());
    }

    /**
     * Return region ID by Iplocation region name
     *
     * @return void
     *
     * @loadFixture testLocationRelationsCollection
     */
    public function testReturnsDirectoryCountryRegionIdByIplocationRegionName()
    {
        $regionName = 'test2';

        $this->assertEquals(
            $this->expected('region')->getData()[0],
            $this->_collection->getRegionIdByIplocationRegionName($regionName)->getData()[0]
        );
    }

    /**
     * Return ID by Iplocation region name
     *
     * @return void
     *
     * @loadFixture testLocationRelationsCollection
     */
    public function testReturnsIdByIplocationRegionName()
    {
        $regionName = 'test3';

        $this->assertEquals(
            $this->expected('region')->getData()[0],
            $this->_collection->getIdByIplocationRegionName($regionName)->getData()[0]
        );
    }

    /**
     * Return ID by Iplocation region name
     *
     * @return void
     *
     * @loadFixture testLocationRelationsCollection
     */
    public function testReturnsIplocationRegionsByRegionId()
    {
        $regionId = '3';

        $this->assertEquals(
            $this->expected('region')->getData()[0],
            $this->_collection->getIplocationRegionsByDirectoryRegionId($regionId)->getData()[0]
        );
    }
}
