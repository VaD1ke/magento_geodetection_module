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

jQuery(function($) {
    "use strict";

    var eventTimestamp;

    var draggableOptions = {
        stack : '.location-management',
        containment : '.location-management',
        revert : true,
        revertDuration : 250,
        zIndex : 3
    };

    $('.location-management-regions-iplocation_li')
        .draggable(draggableOptions);

    $('.location-management-regions-directory_li')
        .droppable({
            accept : '.location-management-regions-iplocation_li',
            hoverClass: 'droppable-hover',
            greedy: true,
            drop : function (event, ui) {
                connectRegions(this, ui.draggable, $);
                eventTimestamp = event.timeStamp;
            }
    });

    $('.location-management-regions')
        .droppable({
            accept : '.location-management-regions-iplocation_li.iplocation-connected',
            hoverClass: 'droppable-iplocations-hover',
            drop : function (event, ui) {
                if (event.timeStamp != eventTimestamp) {
                    disconnectRegions(ui.draggable, $);
                }
            }
        });
});