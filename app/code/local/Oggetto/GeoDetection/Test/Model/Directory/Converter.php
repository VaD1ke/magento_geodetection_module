
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
 * Directory fetcher model test class
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Model_Directory_Converter extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Model directory converter
     *
     * @var Oggetto_GeoDetection_Model_Directory_Converter
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
        $this->_model = Mage::getModel('oggetto_geodetection/directory_converter');
    }

    /**
     * Convert iplocation regions to directory regions
     *
     * @return void
     */
    public function testConvertsIpLocationRegionsToDirectoryRegions()
    {
        $regionName = 'ip_region';

        $locationData = [
            $regionName => [ 'city1', 'city2', 'city3', ]
        ];

        $convertedCities = [ 'conCity1', null, 'conCity3'];

        $dirLocation = [
            'region_id'    => '123',
            'default_name' => 'name1',
        ];

        $returnLocation = [
            $dirLocation['default_name'] => [
                'cities' => [ 'conCity1', 'conCity3', ],
                'id'     => $dirLocation['region_id'],
            ]
        ];

        $modelCityMock = $this->getModelMock(
            'oggetto_geodetection/directory_city_converter', ['convertToDirectoryCity']
        );

        foreach ($locationData[$regionName] as $index => $city) {
            $modelCityMock->expects($this->at($index))
                ->method('convertToDirectoryCity')
                ->with($city)
                ->willReturn($convertedCities[$index]);
        }

        $this->replaceByMock('model', 'oggetto_geodetection/directory_city_converter', $modelCityMock);


        $modelFetcherMock = $this->getModelMock(
            'oggetto_geodetection/directory_region_fetcher', ['getRegionByIplocationRegionName']
        );

        $modelFetcherMock->expects($this->once())
            ->method('getRegionByIplocationRegionName')
            ->with($regionName)
            ->willReturn($dirLocation);

        $this->replaceByMock('model', 'oggetto_geodetection/directory_region_fetcher', $modelFetcherMock);


        $this->assertEquals($returnLocation, $this->_model->convertLocationsToDirectoryRegions($locationData));
    }
}
