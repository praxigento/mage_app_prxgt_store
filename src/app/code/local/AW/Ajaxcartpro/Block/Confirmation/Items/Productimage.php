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


class AW_Ajaxcartpro_Block_Confirmation_Items_Productimage extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $product = $this->getProduct();
        if (!$product instanceof Varien_Object || !$product->getId()) {
            return '';
        }

        $resize = $this->getResize();
        if (is_null($resize)) {
            $resize = 265;
        }
        $helper = Mage::helper('catalog/image');

        $label = $product->getData('small_image_label');
        if (empty($label)) {
            $label = $product->getName();
        }

        $img = '<img src="' . $helper->init($product, 'small_image')->resize($resize) .
               '" alt="' . $this->htmlEscape($label) .
               '" title="' . $this->htmlEscape($label) .
               '" width="' . $resize .
               '" height="' . $resize .
               '" />';
        return $img;
    }
}