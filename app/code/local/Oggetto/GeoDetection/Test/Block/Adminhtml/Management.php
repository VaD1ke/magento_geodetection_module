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
 * Block test class for regions management in adminhtml
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Block_Adminhtml_Management extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Block management
     *
     * @var Oggetto_GeoDetection_Block_Adminhtml_Management
     */
    protected $_managementBlock;

    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_managementBlock = new Oggetto_GeoDetection_Block_Adminhtml_Management;
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

        $this->assertEquals($countryCode, $this->_managementBlock->getDefaultCountry());
    }

    /**
     * Return not connected with directory regions regions by country code from location model
     *
     * @return void
     */
    public function testReturnsNotConnectedWithDirectoryRegionsRegionsByCountryCodeFromLocationModel()
    {
        $regions = ['test'];
        $countryCode = 'test';

        $modelLocationMock = $this->getModelMock(
            'oggetto_geodetection/location_fetcher', ['getNotConnectedRegionsByCountryCode']
        );

        $modelLocationMock->expects($this->once())
            ->method('getNotConnectedRegionsByCountryCode')
            ->with($countryCode)
            ->willReturn($regions);

        $this->replaceByMock('model', 'oggetto_geodetection/location_fetcher', $modelLocationMock);

        $this->assertEquals(
            $regions, $this->_managementBlock->getNotConnectedIplocationRegions($countryCode)
        );
    }

    /**
     * Return directory regions by country code from helper
     *
     * @return void
     */
    public function testReturnsDirectoryRegionsByCountryCodeFromFetcherModel()
    {
        $regions = ['test'];
        $countryCode = 'test';

        $modelFetcherMock = $this->getModelMock('oggetto_geodetection/directory_fetcher', ['getRegions']);

        $modelFetcherMock->expects($this->once())
            ->method('getRegions')
            ->with($countryCode)
            ->willReturn($regions);

        $this->replaceByMock('model', 'oggetto_geodetection/directory_fetcher', $modelFetcherMock);

        $this->assertEquals($regions, $this->_managementBlock->getDirectoryRegions($countryCode));
    }

    /**
     * Return iplocation regions by directory region ID from relation model
     *
     * @return void
     */
    public function testReturnsIplocationRegionsByDirectoryRegionIdFromRelationModel()
    {
        $directoryRegionId = '123';
        $regions = ['test'];

        $modelRelationMock = $this->getModelMock(
            'oggetto_geodetection/location_relation_fetcher', ['getIplocationRegionsByDirectoryRegionId']
        );

        $modelRelationMock->expects($this->once())
            ->method('getIplocationRegionsByDirectoryRegionId')
            ->with($directoryRegionId)
            ->willReturn($regions);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation_fetcher', $modelRelationMock);

        $this->assertEquals(
            $regions, $this->_managementBlock->getIplocationRegions($directoryRegionId)
        );
    }

    /**
     * Return URL for regions relation saving
     *
     * @return void
     */
    public function testReturnsUrlForRegionsRelationsSaving()
    {
        $url = 'test_url';

        $blockManagementMock = $this->getBlockMock('oggetto_geodetection/adminhtml_management', ['getUrl']);

        $blockManagementMock->expects($this->once())
            ->method('getUrl')
            ->willReturn($url);

        $this->replaceByMock('block', 'oggetto_geodetection/adminhtml_management', $blockManagementMock);

        $this->assertEquals($url, $blockManagementMock->getUrlForRelationsSaving());
    }

    /**
     * Return all countries from helper
     *
     * @return void
     */
    public function testReturnsAllCountriesFromFetcherModel()
    {
        $countries = ['testCountry1', 'testCountry2'];

        $modelFetcherMock = $this->getModelMock('oggetto_geodetection/directory_fetcher', ['getAllCountries']);

        $modelFetcherMock->expects($this->once())
            ->method('getAllCountries')
            ->willReturn($countries);

        $this->replaceByMock('model', 'oggetto_geodetection/directory_fetcher', $modelFetcherMock);


        $this->assertEquals($countries, $this->_managementBlock->getAllCountries());
    }
}
