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
 * Location Fetcher model test class
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Model_Location_Fetcher extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Model location
     *
     * @var Oggetto_GeoDetection_Model_Location_Fetcher
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
        $this->_model = Mage::getModel('oggetto_geodetection/location_fetcher');
    }

    /**
     * Return location by IP from collection
     *
     * @return void
     */
    public function testReturnsLocationByIpFromLocationModel()
    {
        $ip = '123';
        $locationData = ['region_name' => 'test1'];

        $location = new Varien_Object;
        $location->setData($locationData);

        $this->_mockLocationModelForLoadingLocationByIp($ip, $location);

        $modelRelationMock = $this->getModelMock(
            'oggetto_geodetection/location_relation_fetcher', ['isRegionConnected']
        );

        $modelRelationMock->expects($this->once())
            ->method('isRegionConnected')
            ->with($locationData['region_name'])
            ->willReturn(true);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation_fetcher', $modelRelationMock);


        $this->assertEquals($locationData, $this->_model->getLocationByIp($ip));
    }

    /**
     * Return null from getting location by IP from collection if region is not connected
     *
     * @return void
     */
    public function testReturnsNullFromGettingLocationByIpFromModelIfRegionIsNotConnected()
    {
        $ip = '123';
        $locationData = ['region_name' => 'test1'];

        $location = new Varien_Object;
        $location->setData($locationData);

        $this->_mockLocationModelForLoadingLocationByIp($ip, $location);

        $modelRelationMock = $this->getModelMock(
            'oggetto_geodetection/location_relation_fetcher', ['isRegionConnected']
        );

        $modelRelationMock->expects($this->once())
            ->method('isRegionConnected')
            ->with($locationData['region_name'])
            ->willReturn(false);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation_fetcher', $modelRelationMock);


        $this->assertNull($this->_model->getLocationByIp($ip));
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
            'selectRegions', 'filterByCountryCode', 'getData'
        ]);

        $collectionLocationsMock->expects($this->once())
            ->method('selectRegions')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->never())
            ->method('filterByCountryCode');

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
    public function testReturnsRegionsAndCitiesFromCollection()
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

        $collectionLocationsMock = $this->getResourceModelMock('oggetto_geodetection/location_collection', [
            'selectRegionsAndCities', 'filterByCountryCode',
            'groupByRegionAndCity', 'orderRegionNameAndByIpCount',
            'getData'
        ]);

        $collectionLocationsMock->expects($this->once())
            ->method('selectRegionsAndCities')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->never())
            ->method('filterByCountryCode');

        $collectionLocationsMock->expects($this->once())
            ->method('groupByRegionAndCity')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('orderRegionNameAndByIpCount')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->replaceByMock('resource_model', 'oggetto_geodetection/location_collection', $collectionLocationsMock);

        $this->assertEquals($regionsAndCities, $this->_model->getRegionsAndCities());
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
            'selectRegionsAndCities', 'filterByCountryCode',
            'groupByRegionAndCity', 'orderRegionNameAndByIpCount',
            'innerJoinWithRelations', 'getData'
        ]);

        $collectionLocationsMock->expects($this->once())
            ->method('selectRegionsAndCities')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->never())
            ->method('innerJoinWithRelations');

        $collectionLocationsMock->expects($this->once())
            ->method('filterByCountryCode')
            ->with($countryCode)
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('groupByRegionAndCity')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('orderRegionNameAndByIpCount')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->replaceByMock('resource_model', 'oggetto_geodetection/location_collection', $collectionLocationsMock);

        $this->assertEquals($regionsAndCities, $this->_model->getRegionsAndCities($countryCode));
    }

    /**
     * Return regions and cities from collection
     *
     * @return void
     */
    public function testReturnsOnlyConnectedRegionsAndCitiesByCountryCodeFromCollection()
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
            'selectRegionsAndCities', 'filterByCountryCode',
            'groupByRegionAndCity', 'orderRegionNameAndByIpCount',
            'innerJoinWithRelations', 'getData'
        ]);

        $collectionLocationsMock->expects($this->once())
            ->method('selectRegionsAndCities')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('filterByCountryCode')
            ->with($countryCode)
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('innerJoinWithRelations')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('groupByRegionAndCity')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('orderRegionNameAndByIpCount')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->replaceByMock('resource_model', 'oggetto_geodetection/location_collection', $collectionLocationsMock);

        $this->assertEquals($regionsAndCities, $this->_model->getRegionsAndCities($countryCode, true));
    }

    /**
     * Return popular(by count of ip addresses) regions from collection
     *
     * @return void
     */
    public function testReturnsPopularRegionsByCountryCodeFromCollection()
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
            'selectRegionsAndCities', 'filterByCountryCode',
            'groupByRegionAndCity', 'orderByIpCount',
            'innerJoinWithRelations', 'getData'
        ]);

        $collectionLocationsMock->expects($this->once())
            ->method('selectRegionsAndCities')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('filterByCountryCode')
            ->with($countryCode)
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('innerJoinWithRelations')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('groupByRegionAndCity')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('orderByIpCount')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->replaceByMock('resource_model', 'oggetto_geodetection/location_collection', $collectionLocationsMock);

        $this->assertEquals($regionsAndCities, $this->_model->getPopularLocations($countryCode));
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
            'selectRegions', 'filterByCountryCode', 'getData'
        ]);

        $collectionLocationsMock->expects($this->once())
            ->method('selectRegions')
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('filterByCountryCode')
            ->with($countryCode)
            ->willReturnSelf();

        $collectionLocationsMock->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->replaceByMock('resource_model', 'oggetto_geodetection/location_collection', $collectionLocationsMock);


        $this->assertEquals($regions, $this->_model->getRegions($countryCode));
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
            'oggetto_geodetection/location_relation_fetcher', [ 'getAllIplocationRegionNames' ]
        );

        $modelRelationMock->expects($this->once())
            ->method('getAllIplocationRegionNames')
            ->with($countryCode)
            ->willReturn($allIplocationRegions);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation_fetcher', $modelRelationMock);


        $collectionLocationMock = $this->getResourceModelMock('oggetto_geodetection/location_collection', [
                'selectRegions', 'filterRegionsNotIn',
                'filterByCountryCode', 'getData'
        ]);

        $collectionLocationMock->expects($this->once())
            ->method('selectRegions')
            ->willReturnSelf();

        $collectionLocationMock->expects($this->once())
            ->method('filterRegionsNotIn')
            ->with($allIplocationRegions)
            ->willReturnSelf();

        $collectionLocationMock->expects($this->once())
            ->method('filterByCountryCode')
            ->with($countryCode)
            ->willReturnSelf();

        $collectionLocationMock->expects($this->once())
            ->method('getData')
            ->willReturn($iplocationRegions);

        $this->replaceByMock('resource_model', 'oggetto_geodetection/location_collection', $collectionLocationMock);


        $this->assertEquals($returnData, $this->_model->getRegions($countryCode, false));
    }

    /**
     * Mock location model for loading location by IP
     *
     * @param string        $ip       IP
     * @param Varien_Object $location Location
     *
     * @return void
     */
    protected function _mockLocationModelForLoadingLocationByIp($ip, $location)
    {
        $locationModelMock = $this->getModelMock('oggetto_geodetection/location', ['loadByIp']);

        $locationModelMock->expects($this->once())
            ->method('loadByIp')
            ->with($ip)
            ->willReturn($location);

        $this->replaceByMock('model', 'oggetto_geodetection/location', $locationModelMock);
    }
}
