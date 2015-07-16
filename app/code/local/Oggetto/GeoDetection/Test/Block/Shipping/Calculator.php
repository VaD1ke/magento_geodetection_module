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
 * Block test class for calculating shipping params (price and time) in product view
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Test_Block_Shipping_Calculator extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Block calculator
     *
     * @var Oggetto_GeoDetection_Block_Shipping_Calculator
     */
    protected $_calculatorBlock;

    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_calculatorBlock = new Oggetto_GeoDetection_Block_Shipping_Calculator;
    }

    /**
     * Return rate results from shipping
     *
     * @return void
     */
    public function testReturnsRatesResultsFromShipping()
    {
        $shippingMethods = ['method1', 'method2'];
        $calculation     = ['price', 'time'];

        $configurableProduct = new Mage_Catalog_Model_Product_Type_Configurable;

        $blockCalculatorMock = $this->getBlockMock('oggetto_geodetection/shipping_calculator', ['_getProduct']);

        $blockCalculatorMock->expects($this->once())
            ->method('_getProduct')
            ->willReturn($configurableProduct);

        $this->replaceByMock('block', 'oggetto_geodetection/shipping_calculator', $blockCalculatorMock);


        $helperDataMock = $this->getHelperMock('oggetto_geodetection', [
            'getSelectedShippingMethods'
        ]);

        $helperDataMock->expects($this->once())
            ->method('getSelectedShippingMethods')
            ->willReturn($shippingMethods);

        $this->replaceByMock('helper', 'oggetto_geodetection', $helperDataMock);


        $modelHandlerMock = $this->getModelMock('oggetto_geodetection/shipping_handler', ['getShippingResults']);

        $modelHandlerMock->expects($this->once())
            ->method('getShippingResults')
            ->with($shippingMethods, $configurableProduct)
            ->willReturn($calculation);

        $this->replaceByMock('model', 'oggetto_geodetection/shipping_handler', $modelHandlerMock);


        $this->assertEquals($calculation, $blockCalculatorMock->calculateShipping());
    }

    /**
     * Check is relations collection empty for established country code
     *
     * @return void
     */
    public function testChecksIsRelationsCollectionEmptyForEstablishedCountryCode()
    {
        $countryCode = 'test';

        $helperDataMock = $this->getHelperMock('oggetto_geodetection', ['getDefaultCountry']);

        $helperDataMock->expects($this->once())
            ->method('getDefaultCountry')
            ->willReturn($countryCode);

        $this->replaceByMock('helper', 'oggetto_geodetection', $helperDataMock);


        $modelRelationMock = $this->getModelMock('oggetto_geodetection/location_relation_fetcher', ['isCollectionEmpty']);

        $modelRelationMock->expects($this->once())
            ->method('isCollectionEmpty')
            ->with($countryCode)
            ->willReturn(true);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation_fetcher', $modelRelationMock);


        $this->assertEquals(false, $this->_calculatorBlock->isEnabled());
    }


}
