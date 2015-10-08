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
 * @package    AW_Ajaxcartpro
 * @version    3.0.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Ajaxcartpro_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function hasFileOption()
    {
        $product = Mage::registry('current_product');
        if ($product) {
            foreach ($product->getOptions() as $option) {
                if ($option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FILE) {
                    return true;
                }
            }
        }
        return false;
    }

    public function extensionEnabled($extension_name)
    {
        if (!isset($this->extensionEnabled[$extension_name]))
        {
            $modules = (array)Mage::getConfig()->getNode('modules')->children();
            if (!isset($modules[$extension_name])
                || $modules[$extension_name]->descend('active')->asArray()=='false'
                || Mage::getStoreConfig('advanced/modules_disable_output/'.$extension_name)
            ) $this->extensionEnabled[$extension_name] = false;
            else $this->extensionEnabled[$extension_name] = true;
        }
        return $this->extensionEnabled[$extension_name];
    }

    public function getWysiwygVariables()
    {
        $variables = array(
            'label' => $this->__('AW Ajaxcartpro extension'),
            'value' => array(
                array(
                    'label' => $this->__('Product image'),
                    'value' => '{{block type="ajaxcartpro/confirmation_items_productimage" product="$product" resize="135"}}'
                ),
                array(
                    'label' => $this->__("'Continue' button"),
                    'value' => '{{block type="ajaxcartpro/confirmation_items_continue"}}'
                ),
                array(
                    'label' => $this->__("'Go to checkout' button"),
                    'value' => '{{block type="ajaxcartpro/confirmation_items_gotocheckout"}}'
                ),
            )
        );
        return $variables;
    }
}
