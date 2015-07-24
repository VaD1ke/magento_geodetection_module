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
 * Block class for showing shipping methods results
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Block
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Block_Shipping_Calculation_Results extends Mage_Core_Block_Template
{
    /**
     * Methods data
     *
     * @var array
     */
    protected $_methodsData;

    /**
     * Set methods data
     *
     * @param array $methodsData Methods data
     *
     * @return $this
     */
    public function setMethodsData($methodsData)
    {
        $this->_methodsData = $methodsData;

        return $this;
    }

    /**
     * Get methods data
     *
     * @return array
     */
    public function getMethodsData()
    {
        return $this->_methodsData;
    }
}
