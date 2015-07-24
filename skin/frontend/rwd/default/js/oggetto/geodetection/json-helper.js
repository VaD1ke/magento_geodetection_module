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

function JSON_stringify(s, emit_unicode)
{
    var json = JSON.stringify(s);
    return emit_unicode ? json : json.replace(/[\u007f-\uffff]/g,
        function(c) {
            return '\\u'+('0000'+c.charCodeAt(0).toString(16)).slice(-4);
        }
    );
}