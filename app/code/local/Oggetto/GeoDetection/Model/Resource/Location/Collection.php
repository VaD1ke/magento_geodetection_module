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
 * Locations Collection
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Resource_Location_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init object
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('oggetto_geodetection/location');
    }

    /**
     * Get location in IP range
     *
     * @param string $longIpAddress Long2Ip IP Address
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLocationInIpRange($longIpAddress)
    {
         $this->addFieldToFilter('ip_from', ['lteq' => $longIpAddress])
              ->addFieldToFilter('ip_to', ['gteq' => $longIpAddress])
              ->getSelect()->limit(1);
        return $this;
    }

    /**
     * Select regions
     *
     * @return $this
     */
    public function selectRegions()
    {
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('region_name')->distinct(true);

        return $this;
    }

    /**
     * Select regions and cities
     *
     * @param int|null $limit Limit
     *
     * @return $this
     */
    public function selectRegionsAndCities($limit = null)
    {
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(['region_name', 'city_name'])
            ->distinct(true)->limit($limit);

        return $this;
    }

    /**
     * Group by region and city
     *
     * @return $this
     */
    public function groupByRegionAndCity()
    {
        $this->getSelect()->group(['region_name', 'city_name']);

        return $this;
    }

    /**
     * Order by IP count
     *
     * @return $this
     */
    public function orderByIpCount()
    {
        $this->getSelect()->order(['COUNT(ip_from) DESC']);

        return $this;
    }

    /**
     * Order by IP count and region name
     *
     * @return $this
     */
    public function orderRegionNameAndByIpCount()
    {
        $this->getSelect()->order(['region_name ASC', 'COUNT(ip_from) DESC']);

        return $this;
    }

    /**
     * Filter by country code
     *
     * @param string $countryCode Country code
     *
     * @return $this
     */
    public function filterByCountryCode($countryCode)
    {
        $this->addFieldToFilter('country_code', ['eq' => $countryCode]);

        return $this;
    }

    /**
     * Filter regions not in array
     *
     * @param array $array Array
     *
     * @return $this
     */
    public function filterRegionsNotIn($array)
    {
        $this->addFieldToFilter('region_name', ['nin' => $array]);

        return $this;
    }

    /**
     * Inner join with relations
     *
     * @return $this
     */
    public function innerJoinWithRelations()
    {
        $this->getSelect()->joinInner(
            ['rel' => $this->getTable('oggetto_geodetection/table_region_iplocations_relation')],
             'main_table.region_name = rel.iplocation_region'
        );

        return $this;
    }
}
