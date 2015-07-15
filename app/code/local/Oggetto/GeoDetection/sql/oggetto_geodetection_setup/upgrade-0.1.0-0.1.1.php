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

/** @var Mage_Core_Model_Resource_Setup $this */

$installer = $this;

$installer->startSetup();

$tableName           = 'oggetto_geodetection/table_region_iplocations_relation';
$regionIdField       = 'directory_region_id';
$iplocationRegion    = 'iplocation_region';
$regionTableName     = 'directory/country_region';


try {

    $table = $installer->getConnection()
        ->newTable($installer->getTable($tableName))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'ID')
        ->addColumn($regionIdField, Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
        ), 'Region ID')
        ->addColumn($iplocationRegion, Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
            'nullable' => false,
        ), 'IPlocation region')
        ->addForeignKey($installer->getFkName(
            $tableName, $regionIdField, $regionTableName, 'region_id'
            ), $regionIdField, $installer->getTable($regionTableName), 'region_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Relation table between directory_country_region and oggetto_geodetection_iplocations');

    $installer->getConnection()->createTable($table);

} catch (Exception $e) {

    Mage::logException($e);

}

$installer->endSetup();
