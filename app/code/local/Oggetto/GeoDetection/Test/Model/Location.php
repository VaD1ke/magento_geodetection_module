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
 * Location model test class
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Model_Location extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Model location
     *
     * @var Oggetto_GeoDetection_Model_Location
     */
    protected $_model = null;

    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('oggetto_geodetection/location');
    }

    /**
     * Check resource model name
     *
     * @return void
     */
    public function testChecksResourceModelName()
    {
        $this->assertEquals('oggetto_geodetection/location', $this->_model->getResourceName());
    }

    /**
     * Return location by IP from collection
     *
     * @return void
     */
    public function testReturnsLocationByIpFromCollection()
    {
        $ip = '123';
        $locationData = ['region_name' => 'test1'];

        $firstItem = new Varien_Object;
        $firstItem->setData($locationData);


        $collectionLocationMock = $this->getResourceModelMock('oggetto_geodetection/location_collection', [
            'getLocationInIpRange', 'getFirstItem'
        ]);

        $collectionLocationMock->expects($this->once())
            ->method('getLocationInIpRange')
            ->with($ip)
            ->willReturnSelf();

        $collectionLocationMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($firstItem);

        $this->replaceByMock(
            'resource_model', 'oggetto_geodetection/location_collection', $collectionLocationMock
        );


        $modelRelationMock = $this->getModelMock('oggetto_geodetection/location_relation', ['isRegionConnected']);

        $modelRelationMock->expects($this->once())
            ->method('isRegionConnected')
            ->with($locationData['region_name'])
            ->willReturn(true);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation', $modelRelationMock);


        $this->assertEquals($locationData, $this->_model->getLocationByIp($ip));
    }

    /**
     * Return regions from collection
     *
     * @return void
     */
    public function testReturnsRegionsFromCollection()
    {
        $data = [
            ['region_name' => 'test1'],
            ['region_name' => 'test2']
        ];
        $regions = ['test1', 'test2'];

        $collectionLocationsMock = $this->getResourceModelMock('oggetto_geodetection/location_collection', [
            'selectRegions', 'getData'
        ]);

        $collectionLocationsMock->expects($this->once())
            ->method('selectRegions')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->replaceByMock('resource_model', 'oggetto_geodetection/location_collection', $collectionLocationsMock);

        $this->assertEquals($regions, $this->_model->getRegions());
    }

    /**
     * Return regions and cities from collection
     *
     * @return void
     */
    public function testReturnsRegionsAndCitiesByCountryCodeFromCollection()
    {
        $data = [
            [
                'region_name' => 'test1',
                'city_name'   => 'testC1'
            ],
            [
                'region_name' => 'test2',
                'city_name'   => 'testC3'
            ],
            [
                'region_name' => 'test1',
                'city_name'   => 'testC2'
            ],
        ];
        $regionsAndCities = [
            'test1' => ['testC1', 'testC2'],
            'test2' => ['testC3']
        ];

        $countryCode = 'code';

        $collectionLocationsMock = $this->getResourceModelMock('oggetto_geodetection/location_collection', [
            'selectRegionsAndCitiesByCountryCode', 'getData'
        ]);

        $collectionLocationsMock->expects($this->once())
            ->method('selectRegionsAndCitiesByCountryCode')
            ->with($countryCode)
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->replaceByMock('resource_model', 'oggetto_geodetection/location_collection', $collectionLocationsMock);

        $this->assertEquals($regionsAndCities, $this->_model->getRegionsAndCitiesByCountryCode($countryCode));
    }

    /**
     * Return regions by country code from collection
     *
     * @return void
     */
    public function testReturnsRegionsByCountryCodeFromCollection()
    {
        $data = [
            ['region_name' => 'test1'],
            ['region_name' => 'test2'],
        ];

        $regions = ['test1', 'test2'];

        $countryCode = 'code';

        $collectionLocationsMock = $this->getResourceModelMock('oggetto_geodetection/location_collection', [
            'selectRegionsByCountryCode', 'getData'
        ]);

        $collectionLocationsMock->expects($this->once())
            ->method('selectRegionsByCountryCode')
            ->with($countryCode)
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->replaceByMock('resource_model', 'oggetto_geodetection/location_collection', $collectionLocationsMock);

        $this->assertEquals($regions, $this->_model->getRegionsByCountryCode($countryCode));
    }

    /**
     * Return iplocation region names that are not connected with directory regions in relation table
     *
     * @return void
     */
    public function testReturnsIplocationRegionNamesThatAreNotConnectedWithDirectoryRegionsInRelationTable()
    {
        $allIplocationRegions = ['test', 'test1', 'test2', 'test3'];

        $iplocationRegions = [
            [ 'region_name' => 'test1' ],
            [ 'region_name' => 'test2' ],
        ];

        $countryCode = 'code';

        $returnData = [ 'test1', 'test2' ];

        $modelRelationMock = $this->getModelMock(
            'oggetto_geodetection/location_relation', [ 'getAllIplocationRegionNamesByCountryCode' ]
        );

        $modelRelationMock->expects($this->once())
            ->method('getAllIplocationRegionNamesByCountryCode')
            ->with($countryCode)
            ->willReturn($allIplocationRegions);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation', $modelRelationMock);


        $collectionLocationMock = $this->getResourceModelMock('oggetto_geodetection/location_collection', [
                'selectRegionsThatNotInRegionsArrayByCountryCode', 'getData'
        ]);

        $collectionLocationMock->expects($this->once())
            ->method('selectRegionsThatNotInRegionsArrayByCountryCode')
            ->with($allIplocationRegions, $countryCode)
            ->willReturnSelf();

        $collectionLocationMock->expects($this->once())
            ->method('getData')
            ->willReturn($iplocationRegions);

        $this->replaceByMock('resource_model', 'oggetto_geodetection/location_collection', $collectionLocationMock);

        $this->assertEquals($returnData, $this->_model->getNotConnectedRegionsByCountryCode($countryCode));
    }
}
