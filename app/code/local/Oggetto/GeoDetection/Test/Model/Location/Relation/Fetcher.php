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
 * Location Relation model test class
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Model_Location_Relation_Fetcher extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Model location relation fetcher
     *
     * @var Oggetto_GeoDetection_Model_Location_Relation_Fetcher
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
        $this->_model = Mage::getModel('oggetto_geodetection/location_relation_fetcher');
    }

    /**
     * Return id by iplocation region name
     *
     * @return void
     */
    public function testReturnsIdByIplocationRegionName()
    {
        $regionName = 'test';
        $id = '123';

        $firstItem = new Varien_Object;
        $firstItem->setData([ 'id' => $id ]);

        $collectionRelationMock = $this->getResourceModelMock('oggetto_geodetection/location_relation_collection', [
            'getIdByIplocationRegionName', 'getFirstItem'
        ]);

        $collectionRelationMock->expects($this->once())
            ->method('getIdByIplocationRegionName')
            ->with($regionName)
            ->willReturnSelf();

        $collectionRelationMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($firstItem);

        $this->replaceByMock(
            'resource_model', 'oggetto_geodetection/location_relation_collection', $collectionRelationMock
        );

        $this->assertEquals($id, $this->_model->getIdByIplocationRegionName($regionName));
    }

    /**
     * Return all iplocation region name
     *
     * @return void
     */
    public function testReturnsAllIplocationRegionNames()
    {
        $regions = [
            [
                'name'              => 'name1',
                'iplocation_region' => 'test1'
            ],
            [
                'name'              => 'name2',
                'iplocation_region' => 'test2'
            ],
        ];

        $countryCode = 'code';

        $regionNames = ['test1', 'test2'];

        $collectionRelationMock = $this->getResourceModelMock(
            'oggetto_geodetection/location_relation_collection', ['selectByCountryCode', 'getData']
        );

        $collectionRelationMock->expects($this->once())
            ->method('selectByCountryCode')
            ->with($countryCode)
            ->willReturnSelf();

        $collectionRelationMock->expects($this->once())
            ->method('getData')
            ->willReturn($regions);

        $this->replaceByMock(
            'resource_model', 'oggetto_geodetection/location_relation_collection', $collectionRelationMock
        );

        $this->assertEquals($regionNames, $this->_model->getAllIplocationRegionNames($countryCode));
    }

    /**
     * Return iplocation region names by directory region ID
     *
     * @return void
     */
    public function testReturnsIplocationRegionNamesByDirectoryRegionId()
    {
        $regions = [
            [
                'name'              => 'name1',
                'iplocation_region' => 'test1'
            ],
            [
                'name'              => 'name2',
                'iplocation_region' => 'test2'
            ],
        ];
        $iplocationRegions = ['test1', 'test2'];
        $directoryRegionId = '2';

        $collectionRelationMock = $this->getResourceModelMock('oggetto_geodetection/location_relation_collection', [
            'getIplocationRegionsByDirectoryRegionId', 'getData'
        ]);

        $collectionRelationMock->expects($this->once())
            ->method('getIplocationRegionsByDirectoryRegionId')
            ->with($directoryRegionId)
            ->willReturnSelf();

        $collectionRelationMock->expects($this->once())
            ->method('getData')
            ->willReturn($regions);

        $this->replaceByMock(
            'resource_model', 'oggetto_geodetection/location_relation_collection', $collectionRelationMock
        );

        $this->assertEquals(
            $iplocationRegions, $this->_model->getIplocationRegionsByDirectoryRegionId($directoryRegionId)
        );
    }

    /**
     * Return region ID by iplocation region name from relations collection
     *
     * @return void
     */
    public function testReturnsRegionIdByIplocationRegionNameFromCollection()
    {
        $iplocationRegionName = 'testName';
        $regionId = 'testID';

        $data = ['directory_region_id' => $regionId, 'test'];
        $firstItem = new Varien_Object;
        $firstItem->addData($data);

        $collectionRelationMock = $this->getResourceModelMock(
            'oggetto_geodetection/location_relation_collection', ['getRegionIdByIplocationRegionName', 'getFirstItem']
        );

        $collectionRelationMock->expects($this->once())
            ->method('getRegionIdByIplocationRegionName')
            ->with($iplocationRegionName)
            ->willReturnSelf();

        $collectionRelationMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($firstItem);

        $this->replaceByMock(
            'resource_model', 'oggetto_geodetection/location_relation_collection', $collectionRelationMock
        );

        $this->assertEquals($regionId, $this->_model->getRegionIdByIplocationRegionName($iplocationRegionName));
    }

    /**
     * Delete regions by country code from collection
     *
     * @return void
     */
    public function testDeletesRegionsByCountryCodeFromCollection()
    {
        $countryCode = 'test';

        $modelRelationMock = $this->getModelMock('oggetto_geodetection/location_relation', ['delete']);

        $modelRelationMock->expects($this->once())
            ->method('delete');

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation', $modelRelationMock);

        $collectionRelationMock = $this->getResourceModelMock(
            'oggetto_geodetection/location_relation_collection', ['selectByCountryCode']
        );

        $collectionRelationMock->addItem($modelRelationMock);

        $collectionRelationMock->expects($this->once())
            ->method('selectByCountryCode')
            ->with($countryCode)
            ->willReturnSelf();

        $this->replaceByMock(
            'resource_model', 'oggetto_geodetection/location_relation_collection', $collectionRelationMock
        );

        $this->_model->clearByCountryCode($countryCode);
    }

    /**
     * Return region ID by iplocation region name from relations collection
     *
     * @return void
     */
    public function testChecksIsRegionConnected()
    {
        $regionName  = 'testName';

        $data = ['test'];
        $firstItem = new Varien_Object;
        $firstItem->addData($data);

        $collectionRelationMock = $this->getResourceModelMock(
            'oggetto_geodetection/location_relation_collection', ['getIdByIplocationRegionName', 'getFirstItem']
        );

        $collectionRelationMock->expects($this->once())
            ->method('getIdByIplocationRegionName')
            ->with($regionName);

        $collectionRelationMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($firstItem);

        $this->replaceByMock(
            'resource_model', 'oggetto_geodetection/location_relation_collection', $collectionRelationMock
        );

        $this->assertEquals(true, $this->_model->isRegionConnected($regionName));
    }

    /**
     * Check is relations collection empty
     *
     * @param string $status Status (empty or not empty)
     * @param int    $value  Value (0 or 1)
     *
     * @return void
     *
     * @dataProvider dataProvider
     */
    public function testChecksIsRelationsCollectionEmpty($status, $value)
    {
        $countryCode = 'code';

        $collectionRelationMock = $this->getResourceModelMock(
            'oggetto_geodetection/location_relation_collection', ['selectByCountryCode', 'getSize']
        );

        $collectionRelationMock->expects($this->once())
            ->method('selectByCountryCode')
            ->with($countryCode)
            ->willReturnSelf();

        $collectionRelationMock->expects($this->once())
            ->method('getSize')
            ->willReturn($value);

        $this->replaceByMock(
            'resource_model', 'oggetto_geodetection/location_relation_collection', $collectionRelationMock
        );

        $this->assertEquals($this->expected()->getData($status), $this->_model->isCollectionEmpty($countryCode));
    }

    /**
     * Insert multiple rows in collection
     *
     * @return void
     */
    public function testInsertsMultipleRowsInCollection()
    {
        $rows = ['test', 'test1'];

        $collectionRelationMock = $this->getResourceModelMock(
            'oggetto_geodetection/location_relation_collection', ['insertMultiple']
        );

        $collectionRelationMock->expects($this->once())
            ->method('insertMultiple')
            ->with($rows);

        $this->replaceByMock(
            'resource_model', 'oggetto_geodetection/location_relation_collection', $collectionRelationMock
        );

        $this->_model->insertMultiple($rows);
    }
}
