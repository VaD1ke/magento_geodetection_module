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
 * Shipping handler model test class
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Model_Shipping_Handler extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Model shipping handler
     *
     * @var Oggetto_GeoDetection_Model_Shipping_Handler
     */
    protected $_modelHandler = null;

    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_modelHandler = Mage::getModel('oggetto_geodetection/shipping_handler');
    }

    /**
     * Return empty calculation array when shipping methods is empty
     *
     * @return void
     */
    public function testReturnsEmptyCalculationArrayWhenShippingMethodsArrayIsEmpty()
    {
        $shippingMethods = [];

        $this->assertSame([], $this->_modelHandler->getShippingResults($shippingMethods, null));
    }

    /**
     * Return empty calculation array when carrier is false
     *
     * @return void
     */
    public function testReturnsEmptyCalculationArrayWhenCarrierIsFalse()
    {
        $shippingMethods = ['method'];

        $modelShippingMock = $this->getModelMock('shipping/shipping', ['getCarrierByCode']);

        $modelShippingMock->expects($this->once())
            ->method('getCarrierByCode')
            ->with($shippingMethods[0])
            ->willReturn(false);

        $this->replaceByMock('model', 'shipping/shipping', $modelShippingMock);

        $this->assertSame([], $this->_modelHandler->getShippingResults($shippingMethods, null));
    }

    /**
     * Return empty calculation array when collectRates has error
     *
     * @return void
     */
    public function testReturnsEmptyCalculationArrayWhenCollectRatesResultsHasError()
    {
        $shippingMethods = ['method'];
        $requestData     = ['data'];

        $request = Mage::getModel('shipping/rate_request');
        $error   = Mage::getModel('shipping/rate_result_error');

        $result = Mage::getModel('shipping/rate_result');
        $result->append($error);


        $modelHandlerMock = $this->getModelMock('oggetto_geodetection/shipping_handler', ['_prepareRequest']);

        $modelHandlerMock->expects($this->once())
            ->method('_prepareRequest')
            ->with($requestData)
            ->willReturn($request);

        $this->replaceByMock('model', 'oggetto_geodetection/shipping_handler', $modelHandlerMock);


        $modelCarrierMock = $this->getModelMock('shipping/carrier_abstract', ['collectRates']);

        $modelCarrierMock->expects($this->once())
            ->method('collectRates')
            ->with($request)
            ->willReturn($result);

        $this->replaceByMock('model', 'shipping/carrier_abstract', $modelCarrierMock);


        $modelShippingMock = $this->getModelMock('shipping/shipping', ['getCarrierByCode']);

        $modelShippingMock->expects($this->once())
            ->method('getCarrierByCode')
            ->with($shippingMethods[0])
            ->willReturn($modelCarrierMock);

        $this->replaceByMock('model', 'shipping/shipping', $modelShippingMock);

        $this->assertSame([], $modelHandlerMock->getShippingResults($shippingMethods, $requestData));
    }

    /**
     * Return empty calculation array when collectRates has error
     *
     * @return void
     */
    public function testReturnsCalculationArray()
    {
        $shippingMethods = ['method'];
        $requestData     = ['data'];

        $rateData = [
            'carrier_title' => 'c_title',
            'method_title'  => 'm_title',
            'price'         => Mage::app()->getStore()->roundPrice(123),
        ];

        $calculationData = [
            $rateData['carrier_title'] => [
                $rateData['method_title'] => [ 'price' => $rateData['price'] ],
            ],
        ];

        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrierTitle($rateData['carrier_title']);
        $rate->setMethodTitle($rateData['method_title']);
        $rate->setPrice($rateData['price']);
        $rate->setCost($rateData['price']);

        $request = Mage::getModel('shipping/rate_request');


        $modelResultMock = $this->getModelMock('shipping/rate_request', ['getAllRates', 'getError']);

        $modelResultMock->expects($this->once())
            ->method('getAllRates')
            ->willReturn([$rate]);

        $modelResultMock->expects($this->once())
            ->method('getError')
            ->willReturn(null);

        $this->replaceByMock('model', 'shipping/rate_request', $modelResultMock);

        $modelHandlerMock = $this->getModelMock('oggetto_geodetection/shipping_handler', ['_prepareRequest']);

        $modelHandlerMock->expects($this->once())
            ->method('_prepareRequest')
            ->with($requestData)
            ->willReturn($request);

        $this->replaceByMock('model', 'oggetto_geodetection/shipping_handler', $modelHandlerMock);


        $modelCarrierMock = $this->getModelMock('shipping/carrier_abstract', ['collectRates']);

        $modelCarrierMock->expects($this->once())
            ->method('collectRates')
            ->with($request)
            ->willReturn($modelResultMock);

        $this->replaceByMock('model', 'shipping/carrier_abstract', $modelCarrierMock);


        $modelShippingMock = $this->getModelMock('shipping/shipping', ['getCarrierByCode']);

        $modelShippingMock->expects($this->once())
            ->method('getCarrierByCode')
            ->with($shippingMethods[0])
            ->willReturn($modelCarrierMock);

        $this->replaceByMock('model', 'shipping/shipping', $modelShippingMock);

        $this->assertSame($calculationData, $modelHandlerMock->getShippingResults($shippingMethods, $requestData));
    }
}
