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
 * the Oggetto Geo IP Location module to newer versions in the future.
 * If you wish to customize the Oggetto Detection module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @copyright  Copyright (C) 2015 Oggetto Web (http://oggettoweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

"use strict";
jQuery( function ($) {
    $('.page-header-container').on('click', '.city-trigger', function(e) {
        e.preventDefault();
        var locations = LocationSingleton.getLocationArray();

        var countryCode = $('#geo_detection-city').data('countryCode');

        if (!locations) {
            $.ajax({
                url: '/geodetection/location',
                dataType: 'json',
                data: { country_code: countryCode },
                method: 'post',
                success: function(data) {
                    if (data.status == 'success') {
                        LocationSingleton.setLocationArray(data.locations);
                        var locationArray = LocationSingleton.getLocationArray();

                        addLocationsInModal(locationArray);
                        showModal();
                    }
                }
            });
        } else {
            showModal();
        }
    });


    $('#location-modal').on('click', '.location-region-li_a', function(e) {
        e.preventDefault();

        var locationModal = $('#location-modal');
        var region = $(this).attr('data-region');
        var cities = LocationSingleton.getLocationArray()[region]['cities'];

        if (cities) {
            var citiesDiv = locationModal.find('#geo-locations-cities');
            citiesDiv.empty();

            var ul = $('<ul>'), li, a;

            for (var i in cities) {
                if (cities.hasOwnProperty(i)) {
                    a = $('<a>');
                    a.append(cities[i]).attr('href', '#').addClass('location-city-li_a');
                    li = $('<li>').addClass('location-city-li').append(a);
                    ul.append(li);
                }
            }

            citiesDiv.append(ul);
        }
    }).on('click', '.location-city-li_a', function(e) {
        e.preventDefault();

        var locationData = {};
        locationData['country'] = $('#geo_detection-city').data('countryCode');
        locationData['city']    = $(this).text().trim();

        var locationArray = LocationSingleton.getLocationArray();

        for (var region in locationArray) {
            for (var city in locationArray[region]['cities']) {
                if (locationArray[region]['cities'][city] == locationData['city']) {
                    locationData['region_id'] = locationArray[region]['id'];
                }
            }
        }

        var locationJson = JSON.stringify(locationData);

        setCookie('user_location', locationJson, { path: "/" });

        location.reload();
    });


    $('.modal .close').on('click', function(e) {
        e.preventDefault();
        $.modal().close();
    });

    function addLocationsInModal(locationArray) {
        var locationModal = $('#location-modal');

        var regionsDiv = locationModal.find('#geo-locations-regions');
        var ul = $('<ul>'), li, a;

        for (var region in locationArray) {
            if (locationArray.hasOwnProperty(region)) {
                a = $('<a>');
                a.append(region).attr('href', '#')
                    .addClass('location-region-li_a')
                    .attr('data-region', region);

                if (locationArray[region].hasOwnProperty('id')) {
                    a.attr('data-region-id', locationArray[region]['id']);
                }

                li = $('<li>').addClass('location-region-li').append(a);
                ul.append(li);
            }
        }

        regionsDiv.append(ul);
    }

    function showModal() {
        $('#location-modal').modal().open();
    }
});
