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
 * Test class for locations management Controller for admin
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Controller_Location extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * Return regions and cities and send success ajax when POST has country_code
     *
     * @return void
     */
    public function testReturnsRegionsAndCitiesFromModelAndSendSuccessAjaxWhenPostHasCountryCode()
    {
        $regionsAndCities = [
            'test1' => ['testC1', 'testC2'],
            'test2' => ['testC3']
        ];

        $convertedRegionsAndCities = [
            'conTest1' => ['testC1', 'testC2'],
            'conTest2' => ['testC3']
        ];

        $jsonLocations = 'testjson';

        $post = ['country_code' => 'code1'];

        $modelLocationMock = $this->getModelMock(
            'oggetto_geodetection/location_fetcher', ['getRegionsAndCities']
        );

        $modelLocationMock->expects($this->once())
            ->method('getRegionsAndCities')
            ->with($post['country_code'], true)
            ->willReturn($regionsAndCities);

        $this->replaceByMock('model', 'oggetto_geodetection/location_fetcher', $modelLocationMock);

        $modelConverterMock = $this->getModelMock(
            'oggetto_geodetection/directory_converter', ['convertLocationsToDirectoryRegions']
        );

        $modelConverterMock->expects($this->once())
            ->method('convertLocationsToDirectoryRegions')
            ->with($regionsAndCities)
            ->willReturn($convertedRegionsAndCities);

        $this->replaceByMock('model', 'oggetto_geodetection/directory_converter', $modelConverterMock);


        $helperDataMock = $this->getHelperMock('oggetto_geodetection', [ 'getDefaultCountry', 'jsonEncode']);

        $helperDataMock->expects($this->never())
            ->method('getDefaultCountry');

        $helperDataMock->expects($this->once())
            ->method('jsonEncode')
            ->with($convertedRegionsAndCities)
            ->willReturn($jsonLocations);

        $this->replaceByMock('helper', 'oggetto_geodetection', $helperDataMock);


        $ajaxResponseMock = $this->_getAjaxResponseMockModelWithSuccessData([
            'locations' => $jsonLocations, 'status' => 'success'
        ]);

        $this->_mockHelperAjaxForSendingResponse($ajaxResponseMock);


        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);

        $this->dispatch('geodetection/location/index');

        $this->_assertRequestsDispatchForwardAndController('index');
    }

    /**
     * Return regions and cities and send success ajax when POST has not country_code
     *
     * @return void
     */
    public function testReturnsRegionsAndCitiesFromModelAndSendSuccessAjaxWhenPostHasNotCountryCode()
    {
        $regionsAndCities = [
            'test1' => ['testC1', 'testC2'],
            'test2' => ['testC3']
        ];

        $convertedRegionsAndCities = [
            'conTest1' => ['testC1', 'testC2'],
            'conTest2' => ['testC3']
        ];

        $jsonLocations = 'testjson';

        $countryCode = 'code123';

        $modelLocationMock = $this->getModelMock(
            'oggetto_geodetection/location_fetcher', ['getRegionsAndCities']
        );

        $modelLocationMock->expects($this->once())
            ->method('getRegionsAndCities')
            ->with($countryCode)
            ->willReturn($regionsAndCities);

        $this->replaceByMock('model', 'oggetto_geodetection/location_fetcher', $modelLocationMock);

        $modelConverterMock = $this->getModelMock(
            'oggetto_geodetection/directory_converter', ['convertLocationsToDirectoryRegions']
        );

        $modelConverterMock->expects($this->once())
            ->method('convertLocationsToDirectoryRegions')
            ->with($regionsAndCities)
            ->willReturn($convertedRegionsAndCities);

        $this->replaceByMock('model', 'oggetto_geodetection/directory_converter', $modelConverterMock);

        $helperDataMock = $this->getHelperMock('oggetto_geodetection', ['getDefaultCountry', 'jsonEncode']);

        $helperDataMock->expects($this->once())
            ->method('getDefaultCountry')
            ->willReturn($countryCode);

        $helperDataMock->expects($this->once())
            ->method('jsonEncode')
            ->with($convertedRegionsAndCities)
            ->willReturn($jsonLocations);

        $this->replaceByMock('helper', 'oggetto_geodetection', $helperDataMock);


        $ajaxResponseMock = $this->_getAjaxResponseMockModelWithSuccessData([
            'locations' => $jsonLocations, 'status' => 'success'
        ]);

        $this->_mockHelperAjaxForSendingResponse($ajaxResponseMock);


        $this->dispatch('geodetection/location/index');

        $this->_assertRequestsDispatchForwardAndController('index');
    }


    /**
     * Throw exception when getting regions and cities, send error ajax when locations cache is empty
     *
     * @return void
     */
    public function testThrowsExceptionWhenGetRegionsAndCitiesAndSendErrorAjaxWhenPostHasCountryCode()
    {
        $post = ['country_code' => 'code1'];

        $modelLocationMock = $this->getModelMock('oggetto_geodetection/location_fetcher', ['getRegionsAndCities']);

        $modelLocationMock->expects($this->once())
            ->method('getRegionsAndCities')
            ->with($post['country_code'])
            ->willThrowException(new Exception);

        $this->replaceByMock('model', 'oggetto_geodetection/location_fetcher', $modelLocationMock);


        $helperDataMock = $this->getHelperMock('oggetto_geodetection', ['convertLocationsToDirectoryRegions']);

        $helperDataMock->expects($this->never())
            ->method('convertLocationsToDirectoryRegions');

        $this->replaceByMock('helper', 'oggetto_geodetection', $helperDataMock);


        $helperCoreMock = $this->getHelperMock('core', ['jsonEncode']);

        $helperCoreMock->expects($this->never())
            ->method('jsonEncode');

        $this->replaceByMock('helper', 'core', $helperCoreMock);


        $ajaxResponseMock = $this->getModelMock('ajax/response', ['success', 'error']);

        $ajaxResponseMock->expects($this->never())
            ->method('success');

        $ajaxResponseMock->expects($this->once())
            ->method('error');

        $this->replaceByMock('model', 'ajax/response', $ajaxResponseMock);


        $this->_mockHelperAjaxForSendingResponse($ajaxResponseMock);


        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);

        $this->dispatch('geodetection/location/index');

        $this->_assertRequestsDispatchForwardAndController('index');
    }


    /**
     * Get helper ajax mock for sending response
     *
     * @param EcomDev_PHPUnit_Mock_Proxy $responseMock Response model mock
     *
     * @return void
     */
    protected function _mockHelperAjaxForSendingResponse($responseMock)
    {
        $helperAjaxMock = $this->getHelperMock('ajax', ['sendResponse']);

        $helperAjaxMock->expects($this->once())
            ->method('sendResponse')
            ->with($responseMock);

        $this->replaceByMock('helper', 'ajax', $helperAjaxMock);
    }

    /**
     * Case for asserting Request dispatched, not forwarded, Controller module, name and action for oggetto_faq module
     *
     * @param string $actionName Name of action
     *
     * @return void
     */
    protected function _assertRequestsDispatchForwardAndController($actionName)
    {
        $this->assertRequestDispatched();
        $this->assertRequestNotForwarded();
        $this->assertRequestControllerModule('Oggetto_GeoDetection');

        $this->assertRequestRouteName('oggetto_geodetection');
        $this->assertRequestControllerName('location');
        $this->assertRequestActionName($actionName);
    }

    /**
     * Mock Ajax response model with success data
     *
     * @param array $data Response data
     *
     * @return EcomDev_PHPUnit_Mock_Proxy
     */
    protected function _getAjaxResponseMockModelWithSuccessData($data)
    {
        $ajaxResponseMock = $this->getModelMock('ajax/response', ['success', 'error', 'setData']);

        $ajaxResponseMock->expects($this->once())
            ->method('success')
            ->willReturnSelf();

        $ajaxResponseMock->expects($this->once())
            ->method('setData')
            ->with($data)
            ->willReturnSelf();

        $ajaxResponseMock->expects($this->never())
            ->method('error');

        $this->replaceByMock('model', 'ajax/response', $ajaxResponseMock);

        return $ajaxResponseMock;
    }
}
