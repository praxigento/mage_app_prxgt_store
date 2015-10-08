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
 * @package    AW_Blog
 * @version    1.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Blog_Model_Dateformat {

    protected $_options;
    const FORMAT_TYPE_FULL = 'full';
    const FORMAT_TYPE_LONG = 'long';
    const FORMAT_TYPE_MEDIUM= 'medium';
    const FORMAT_TYPE_SHORT = 'short';

    public function toOptionArray() {
        if (!$this->_options) {
            $this->_options[] = array(
                'value' => self::FORMAT_TYPE_FULL,
                'label' => Mage::helper('blog')->__('Full')
            );
            $this->_options[] = array(
                'value' => self::FORMAT_TYPE_LONG,
                'label' => Mage::helper('blog')->__('Long')
            );
            $this->_options[] = array(
                'value' => self::FORMAT_TYPE_MEDIUM,
                'label' => Mage::helper('blog')->__('Medium')
            );
            $this->_options[] = array(
                'value' => self::FORMAT_TYPE_SHORT,
                'label' => Mage::helper('blog')->__('Short')
            );
        }
        return $this->_options;
    }

}
