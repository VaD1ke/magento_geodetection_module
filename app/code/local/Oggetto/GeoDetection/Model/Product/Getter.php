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
 * Catalog product getter
 *
 * @category   Oggetto
 * @package    Oggetto_GeoDetection
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_GeoDetection_Model_Product_Getter
{
    /**
     * Get cheapest simple product
     *
     * @param Mage_Catalog_Model_Product $product Product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getCheapestSimpleProduct($product)
    {
        $typeId = $product->getTypeId();

        $cheapestProduct = $product;

        if ($typeId == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            /** @var Mage_Catalog_Model_Product_Type_Configurable $modelConfigurable */
            $modelConfigurable = Mage::getModel('catalog/product_type_configurable');

            $childProducts = $modelConfigurable->getUsedProductIds($product);

            $cheapestProduct = $this->_getCheapestProduct($childProducts);
        }

        return $cheapestProduct;
    }

    /**
     * Get cheapest product
     *
     * @param array $productIds Product IDs
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getCheapestProduct($productIds)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToSelect(['price', 'weight']);

        /** @var Mage_Catalog_Model_Product $cheapestProduct */
        $cheapestProduct = $collection->getFirstItem();

        $price = $cheapestProduct->getFinalPrice();

        /** @var Mage_Catalog_Model_Product $product */
        foreach ($collection as $product) {
            if ($product->getFinalPrice() < $price || is_null($price)) {
                $cheapestProduct = $product;
            }
        }

        return $cheapestProduct;
    }
}
