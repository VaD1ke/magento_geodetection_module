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

function connectRegions(directoryRegion, iplocationRegion, $) {
    "use strict";

    var regionId = $(directoryRegion).data('id');

    if ($(iplocationRegion).data('regionId') == regionId) {
        return;
    }

    if ( !($(directoryRegion).hasClass('connected')) ) {
        $(directoryRegion).addClass('connected');
    }

    var ul = $(directoryRegion).find('.location-management-regions-directory_ul');
    ul.append(iplocationRegion);

    var prevRegionId = $(iplocationRegion).attr('data-region-id');
    if (prevRegionId) {
        $('#directory-region-' + prevRegionId).removeClass('connected');
    }


    var input = $('<input>').attr('type', 'hidden')
                            .attr('name', 'data[' + regionId + '][]')
                            .attr('value', $(iplocationRegion).text().trim());

    $(iplocationRegion).prepend(input);
    $(iplocationRegion).addClass('iplocation-connected').attr('data-region-id', regionId);
}

function disconnectRegions(iplocationRegion, $) {
    var regionsDiv = $(iplocationRegion).closest('.location-management-regions');

    $(iplocationRegion).removeClass('iplocation-connected');
    $(regionsDiv).find('.location-management-regions-iplocation').prepend(iplocationRegion);
    $(iplocationRegion).find('input').remove();

    var regionId = '#directory-region-' + $(iplocationRegion).attr('data-region-id');
    var regionDiv = $(regionId);

    if ( !($(regionDiv).find('.location-management-regions-directory_ul').children().length)) {
        $(regionDiv).removeClass('connected');
    }
}
