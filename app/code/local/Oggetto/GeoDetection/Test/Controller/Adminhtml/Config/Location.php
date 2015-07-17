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
class Oggetto_GeoDetection_Test_Controller_Adminhtml_Config_Location
    extends Oggetto_Phpunit_Test_Case_Controller_Adminhtml
{
    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_setUpAdminSession();
    }

    /**
     * Load and render layout for displaying regions connection
     *
     * @return void
     */
    public function testLoadsAndRendersLayoutForDisplayingRegionsConnection()
    {
        $blockManagementMock = $this->getBlockMock('oggetto_geodetection/adminhtml_management', ['setCountryCode']);

        $blockManagementMock->expects($this->once())
            ->method('setCountryCode');

        $this->replaceByMock('block', 'oggetto_geodetection/adminhtml_management', $blockManagementMock);


        $this->dispatch('adminhtml/config_location/index');

        $this->_assertRequestsDispatchForwardRouteAndController('index');

        $this->assertLayoutHandleLoaded('adminhtml_config_location_index');
        $this->assertLayoutRendered();

        $this->assertLayoutBlockCreated('content.geodetection_management');

        $this->assertLayoutBlockInstanceOf(
            'content.geodetection_management', 'Oggetto_GeoDetection_Block_Adminhtml_Management'
        );

        $this->assertLayoutBlockParentEquals('content.geodetection_management', 'content');
        $this->assertLayoutBlockRendered('content.geodetection_management');
    }

    /**
     * Save relation between directory and iplocation regions and redirects
     *
     * @return void
     */
    public function testSavesRelationBetweenDirectoryAndIplocationRegionsAndRedirectsToIndex()
    {
        $post = [
            'data' => [
                'country_code' => 'code123',
                '1' => ['name1', 'name2'],
                '2' => ['name4'],
            ]
        ];

        $savingData = [
            '0' => [ 'directory_region_id' => '1', 'iplocation_region' => 'name1' ],
            '1' => [ 'directory_region_id' => '1', 'iplocation_region' => 'name2' ],
            '2' => [ 'directory_region_id' => '2', 'iplocation_region' => 'name4' ],
        ];


        $modelRelationMock = $this->getModelMock('oggetto_geodetection/location_relation_fetcher', [
            'clearByCountryCode', 'insertMultiple'
        ]);

        $modelRelationMock->expects($this->once())
            ->method('clearByCountryCode')
            ->with($post['data']['country_code']);

        $modelRelationMock->expects($this->once())
            ->method('insertMultiple')
            ->with($savingData);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation_fetcher', $modelRelationMock);


        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);

        $this->dispatch('adminhtml/config_location/save');

        $this->assertRequestDispatched();
        $this->assertRedirectTo('adminhtml/config_location/index');
    }

    /**
     * Throw exception when save relation between directory and iplocation regions and redirects
     *
     * @return void
     */
    public function testThrowsExceptionWhenSavesRelationBetweenDirectoryAndIplocationRegionsAndRedirectsToIndex()
    {
        $post = [
            'data' => [
                'country_code' => 'code123',
                '1' => ['name1', 'name2'],
                '2' => ['name4'],
            ]
        ];

        $savingData = [
            '0' => [ 'directory_region_id' => '1', 'iplocation_region' => 'name1' ],
            '1' => [ 'directory_region_id' => '1', 'iplocation_region' => 'name2' ],
            '2' => [ 'directory_region_id' => '2', 'iplocation_region' => 'name4' ],
        ];

        $modelRelationMock = $this->getModelMock('oggetto_geodetection/location_relation_fetcher', [
            'clearByCountryCode', 'insertMultiple'
        ]);

        $modelRelationMock->expects($this->once())
            ->method('clearByCountryCode')
            ->with($post['data']['country_code']);

        $modelRelationMock->expects($this->once())
            ->method('insertMultiple')
            ->with($savingData)
            ->willThrowException(new Exception);

        $this->replaceByMock('model', 'oggetto_geodetection/location_relation_fetcher', $modelRelationMock);


        $sessionSingletonMock = $this->getModelMock('adminhtml/session', ['addError']);

        $sessionSingletonMock->expects($this->once())
            ->method('addError');

        $this->replaceByMock('singleton', 'adminhtml/session', $sessionSingletonMock);


        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);

        $this->dispatch('adminhtml/config_location/save');

        $this->assertRequestDispatched();
        $this->assertRedirectTo('adminhtml/config_location/index');
    }


    /**
     * Case for asserting Request dispatched, not forwarded, Controller module, name and action for oggetto_faq module
     *
     * @param string $actionName Name of action
     *
     * @return void
     */
    protected function _assertRequestsDispatchForwardRouteAndController($actionName)
    {
        $this->assertRequestDispatched();
        $this->assertRequestNotForwarded();
        $this->assertRequestControllerModule('Oggetto_GeoDetection_Adminhtml');

        $this->assertRequestRouteName('adminhtml');
        $this->assertRequestControllerName('config_location');
        $this->assertRequestActionName($actionName);
    }
}
