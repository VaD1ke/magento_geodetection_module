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
 * Location Relations Collection
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Resource_Location_Relation_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init object
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('oggetto_geodetection/location_relation');
    }

    /**
     * Get region ID by IPlocation region name
     *
     * @param string $regionName IpLocation region name
     *
     * @return $this
     */
    public function getRegionIdByIplocationRegionName($regionName)
    {
        $this->addFieldToFilter('iplocation_region', ['eq' => $regionName])
             ->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('directory_region_id')->limit(1);

        return $this;
    }

    /**
     * Get region ID by IPlocation region name
     *
     * @param string $regionName IpLocation region name
     *
     * @return $this
     */
    public function getIdByIplocationRegionName($regionName)
    {
        $this->addFieldToFilter('iplocation_region', ['eq' => $regionName])
            ->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('id')->limit(1);

        return $this;
    }

    /**
     * Get iplocation regions by directory region ID
     *
     * @param mixed $directoryRegionId Directory region ID
     *
     * @return $this
     */
    public function getIplocationRegionsByDirectoryRegionId($directoryRegionId)
    {
        $this->addFieldToFilter('directory_region_id', ['eq' => $directoryRegionId])
            ->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('iplocation_region');

        return $this;
    }

    /**
     * Select by country code
     *
     * @param string $countryCode Country code
     *
     * @return $this
     */
    public function selectByCountryCode($countryCode)
    {
        $this->getSelect()->join(
            ['dir' => $this->getTable('directory/country_region')],
            'main_table.directory_region_id = dir.region_id'
        )->where("dir.country_id = '{$countryCode}'");

        return $this;
    }

    /**
     * Insert multiple
     *
     * @param array $rows Rows
     *
     * @return void
     */
    public function insertMultiple($rows)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->insertMultiple($this->getMainTable(), $rows);
    }
}
