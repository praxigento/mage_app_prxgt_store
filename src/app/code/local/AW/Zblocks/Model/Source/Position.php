<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Zblocks
 * @version    2.3.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Zblocks_Model_Source_Position
{
    
    const WIDGET_POSITION = 'widget';
    
    
    public function toOptionArray()
    {
        $helper = Mage::helper('zblocks');
        return array(
             array(
                'label' => $helper->__('Widget instance'),
                'value' => array(
                    array('value' => self::WIDGET_POSITION, 'label' => $helper->__('Custom Widget')),
                 ),
                '_needs_category' => false
            ),
            array(
                'label' => $helper->__('Default for using in CMS page template'),
                'value' => array(
                    array('value' => 'custom', 'label' => $helper->__('Custom')),
                ),
                '_needs_category' => true
            ),
            array(
                'label' => $helper->__('General (will be displayed on all pages)'),
                'value' => array(
                    array('value' => 'sidebar-right-top', 'label' => $helper->__('Sidebar Right Top')),
                    array('value' => 'sidebar-right-bottom', 'label' => $helper->__('Sidebar Right Bottom')),
                    array('value' => 'sidebar-left-top', 'label' => $helper->__('Sidebar Left Top')),
                    array('value' => 'sidebar-left-bottom', 'label' => $helper->__('Sidebar Left Bottom')),
                    array('value' => 'content-top', 'label' => $helper->__('Content Top')),
                    array('value' => 'menu-top', 'label' => $helper->__('Menu Top')),
                    array('value' => 'menu-bottom', 'label' => $helper->__('Menu Bottom')),
                    array('value' => 'page-bottom', 'label' => $helper->__('Page Bottom')),
                )
            ),
            array(
                'label' => $helper->__('Catalog and product'),
                'value' => array(
                    array('value' => 'catalog-sidebar-right-top', 'label' => $helper->__('Catalog Sidebar Right Top')),
                    array('value' => 'catalog-sidebar-right-bottom', 'label' => $helper->__('Catalog Sidebar Right Bottom')),
                    array('value' => 'catalog-sidebar-left-top', 'label' => $helper->__('Catalog Sidebar Left Top')),
                    array('value' => 'catalog-sidebar-left-bottom', 'label' => $helper->__('Catalog Sidebar Left Bottom')),
                    array('value' => 'catalog-content-top', 'label' => $helper->__('Catalog Content Top')),
                    array('value' => 'catalog-menu-top', 'label' => $helper->__('Catalog Menu Top')),
                    array('value' => 'catalog-menu-bottom', 'label' => $helper->__('Catalog Menu Bottom')),
                    array('value' => 'catalog-page-bottom', 'label' => $helper->__('Catalog Page Bottom')),
                ),
                '_needs_category' => true
            ),
            array(
                'label' => $helper->__('Product only'),
                'value' => array(
                    array('value' => 'product-sidebar-right-top', 'label' => $helper->__('Product Sidebar Right Top')),
                    array('value' => 'product-sidebar-right-bottom', 'label' => $helper->__('Product Sidebar Right Bottom')),
                    array('value' => 'product-sidebar-left-top', 'label' => $helper->__('Product Sidebar Left Top')),
                    array('value' => 'product-sidebar-left-bottom', 'label' => $helper->__('Product Sidebar Left Bottom')),
                    array('value' => 'product-content-top', 'label' => $helper->__('Product Content Top')),
                    array('value' => 'product-menu-top', 'label' => $helper->__('Product Menu Top')),
                    array('value' => 'product-menu-bottom', 'label' => $helper->__('Product Menu Bottom')),
                    array('value' => 'product-page-bottom', 'label' => $helper->__('Product Page Bottom')),
                ),
                '_needs_category' => true
            ),
            array(
                'label' => $helper->__('Customer'),
                'value' => array(
                    array('value' => 'customer-content-top', 'label' => $helper->__('Customer Content Top')),
                )
            ),
            array(
                'label' => $helper->__('Cart & Checkout'),
                'value' => array(
                    array('value' => 'cart-content-top', 'label' => $helper->__('Cart Content Top')),
                    array('value' => 'checkout-content-top', 'label' => $helper->__('Checkout Content Top')),
                )
            ),
        );
    }

    public function getNeedCategoryPositions()
    {
        $positions = array();
        foreach($this->toOptionArray() as $optGroup) {
            if(isset($optGroup['_needs_category']) && $optGroup['_needs_category']) {
                if(isset($optGroup['value']) && (is_array($optGroup['value']) || $optGroup['value'] instanceof Traversable)) {
                    foreach($optGroup['value'] as $option) {
                        if(isset($option['value'])) {
                            $positions[] = $option['value'];
                        }
                    }
                }
            }
        }
        return $positions;
    }
}
