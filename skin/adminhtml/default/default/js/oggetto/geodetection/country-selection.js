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

jQuery( function($) {
    "use strict";

    $('#country-selection').on('change', function(){
        var optionSelected = $(this).find('option:selected');

        var url = window.location.href;
        var urlWithCountryCode = getUrlWithCountryCode(url, optionSelected.val());

        if (url != urlWithCountryCode) {
            location.href = urlWithCountryCode;
        }
    });
});

function getUrlWithCountryCode(url, countryCode) {
    var urlArr = url.split('/');

    var codePos = urlArr.indexOf('country_code');

    if (codePos != -1) {
        urlArr[codePos + 1] = countryCode;
    } else {
        var indexPos = urlArr.indexOf('index');

        if (indexPos != -1) {
            urlArr.splice(indexPos + 1, 0, 'country_code');
            urlArr.splice(indexPos + 2, 0, countryCode);
        } else {
            return url;
        }
    }
    url = urlArr.join('/');
    return url;
}