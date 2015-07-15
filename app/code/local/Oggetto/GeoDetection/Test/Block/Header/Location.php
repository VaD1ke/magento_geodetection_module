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
 * Block test class for displaying questions
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Block_Header_Location extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Block location
     *
     * @var Oggetto_GeoDetection_Block_Header_Location
     */
    protected $_locationBlock;

    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_locationBlock = new Oggetto_GeoDetection_Block_Header_Location;
    }

    /**
     * Return user location from cookie
     *
     * @return void
     */
    public function testReturnsUserLocationFromCookie()
    {
        $location     = ['test'];
        $locationJson = 'test';

        $modelCookieMock = $this->getModelMock('core/cookie', ['get']);

        $modelCookieMock->expects($this->once())
            ->method('get')
            ->with(Oggetto_GeoDetection_Block_Header_Location::LOCATION_COOKIE_NAME)
            ->willReturn($locationJson);

        $this->replaceByMock('model', 'core/cookie', $modelCookieMock);

        $helperMock = $this->getHelperMock('oggetto_geodetection', ['jsonDecode']);

        $helperMock->expects($this->once())
            ->method('jsonDecode')
            ->with($locationJson)
            ->willReturn($location);

        $this->replaceByMock('helper', 'oggetto_geodetection', $helperMock);

        $this->assertEquals($location, $this->_locationBlock->getUserLocation());
    }

    /**
     * Return user location from IPlocations table if cookie is empty
     *
     * @return void
     */
    public function testReturnsUserLocationFromIplocationsTableIfCookieIsEmpty()
    {
        $ip = 123456;
        $locationData = [
            'country_code' => 'country',
            'region_name'  => 'region',
            'city_name'    => 'city'
        ];
        $returnLocation  = [
            'country'   => $locationData['country_code'],
            'region_id' => 'test123',
            'city'      => $locationData['city_name']
        ];
        $returnLocationJson = 'test';


        $modelCookieMock = $this->getModelMock('core/cookie', ['get', 'set']);

        $modelCookieMock->expects($this->once())
            ->method('get')
            ->with(Oggetto_GeoDetection_Block_Header_Location::LOCATION_COOKIE_NAME)
            ->willReturn(false);

        $modelCookieMock->expects($this->once())
            ->method('set')
            ->with(
                Oggetto_GeoDetection_Block_Header_Location::LOCATION_COOKIE_NAME,
                $returnLocationJson, 0, '/', null, null, true
            );

        $this->replaceByMock('model', 'core/cookie', $modelCookieMock);

        $helperMock = $this->getHelperMock('oggetto_geodetection', ['jsonEncode']);

        $helperMock->expects($this->once())
            ->method('jsonEncode')
            ->with($returnLocation)
            ->willReturn($returnLocationJson);

        $this->replaceByMock('helper', 'oggetto_geodetection', $helperMock);


        $relationModelMock = $this->getModelMock(
            'oggetto_geodetection/location_relation', ['getRegionIdByIplocationRegionName']
        );

        $relationModelMock->expects($this->once())
            ->method('getRegionIdByIplocationRegionName')
            ->with($locationData['region_name'])
            ->willReturn($returnLocation['region_id']);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation', $relationModelMock);

        $this->_mockCoreHttpHelperForGettingRemoteAddress($ip);


        $this->_mockLocationModelForGettingLocationDataByIp($ip, $locationData);


        $this->assertEquals($returnLocation, $this->_locationBlock->getUserLocation());
    }

    /**
     * Return null from getting user's location if iplocations collection is empty
     *
     * @return void
     */
    public function testReturnsNullFromGettingUserLocationIfIplocationsCollectionIsEmpty()
    {
        $ip = 123456;
        $locationData = [];


        $modelCookieMock = $this->getModelMock('core/cookie', ['get', 'set']);

        $modelCookieMock->expects($this->once())
            ->method('get')
            ->with(Oggetto_GeoDetection_Block_Header_Location::LOCATION_COOKIE_NAME)
            ->willReturn(false);

        $modelCookieMock->expects($this->never())
            ->method('set');

        $this->replaceByMock('model', 'core/cookie', $modelCookieMock);


        $this->_mockCoreHttpHelperForGettingRemoteAddress($ip);

        $this->_mockLocationModelForGettingLocationDataByIp($ip, $locationData);


        $this->assertNull($this->_locationBlock->getUserLocation());
    }

    /**
     * Return null from getting user's location if iplocations collection has dash value in city name
     *
     * @return void
     */
    public function testReturnsNullFromGettingUserLocationIfIplocationsCollectionHasDashValueInCityName()
    {
        $ip = 123456;
        $locationData = [
            'city_name' => '-'
        ];


        $modelCookieMock = $this->getModelMock('core/cookie', ['get', 'set']);

        $modelCookieMock->expects($this->once())
            ->method('get')
            ->with(Oggetto_GeoDetection_Block_Header_Location::LOCATION_COOKIE_NAME)
            ->willReturn(false);

        $modelCookieMock->expects($this->never())
            ->method('set');

        $this->replaceByMock('model', 'core/cookie', $modelCookieMock);


        $this->_mockCoreHttpHelperForGettingRemoteAddress($ip);

        $this->_mockLocationModelForGettingLocationDataByIp($ip, $locationData);


        $this->assertNull($this->_locationBlock->getUserLocation());
    }

    /**
     * Check if relation collection is empty
     *
     * @param string $status Status (empty or not empty)
     * @param int    $value  Value (0 or 1)
     *
     * @return void
     *
     * @dataProvider dataProvider
     */
    public function testChecksIfRelationCollectionIsEmpty($status, $value)
    {
        $countryCode = 'code';

        $helperDataMock = $this->getHelperMock('oggetto_geodetection', ['getDefaultCountry']);

        $helperDataMock->expects($this->once())
            ->method('getDefaultCountry')
            ->willReturn($countryCode);

        $this->replaceByMock('helper', 'oggetto_geodetection', $helperDataMock);

        $modelRelationMock = $this->getModelMock('oggetto_geodetection/location_relation', ['isCollectionEmpty']);

        $modelRelationMock->expects($this->once())
            ->method('isCollectionEmpty')
            ->with($countryCode)
            ->willReturn($value);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation', $modelRelationMock);

        $this->assertEquals($this->expected()->getData($status), $this->_locationBlock->isEnabled());
    }


    /**
     * Mock core/http helper for getting remote address
     *
     * @param int $ip IP
     *
     * @return void
     */
    protected function _mockCoreHttpHelperForGettingRemoteAddress($ip)
    {
        $helperHttpMock = $this->getHelperMock('core/http', ['getRemoteAddr']);

        $helperHttpMock->expects($this->once())
            ->method('getRemoteAddr')
            ->with(true)
            ->willReturn($ip);

        $this->replaceByMock('helper', 'core/http', $helperHttpMock);
    }

    /**
     * Mock location model for getting location data by IP
     *
     * @param int   $ip           IP
     * @param array $locationData Location data
     *
     * @return void
     */
    protected function _mockLocationModelForGettingLocationDataByIp($ip, $locationData)
    {
        $modelLocationMock = $this->getModelMock('oggetto_geodetection/location_fetcher', ['getLocationByIp']);

        $modelLocationMock->expects($this->once())
            ->method('getLocationByIp')
            ->with($ip)
            ->willReturn($locationData);

        $this->replaceByMock('model', 'oggetto_geodetection/location_fetcher', $modelLocationMock);
    }

    /**
     * Return default country code from helper
     *
     * @return void
     */
    public function testReturnsDefaultCountryCodeFromHelper()
    {
        $countryCode = 'test';

        $helperDataMock = $this->getHelperMock('oggetto_geodetection', ['getDefaultCountry']);

        $helperDataMock->expects($this->once())
            ->method('getDefaultCountry')
            ->willReturn($countryCode);

        $this->replaceByMock('helper', 'oggetto_geodetection', $helperDataMock);

        $this->assertEquals($countryCode, $this->_locationBlock->getDefaultCountry());
    }
}
