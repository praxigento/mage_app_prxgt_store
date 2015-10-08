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


class AW_Blog_Model_Status extends Varien_Object {
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    const STATUS_HIDDEN = 3;

    public function addEnabledFilterToCollection($collection) {
        $collection->addEnableFilter(array('in' => $this->getEnabledStatusIds()));
        return $this;
    }

    public function addCatFilterToCollection($collection, $cat) {
        $collection->addCatFilter($cat);
        return $this;
    }

    public function getEnabledStatusIds() {
        return array(self::STATUS_ENABLED);
    }

    public function getDisabledStatusIds() {
        return array(self::STATUS_DISABLED);
    }

    public function getHiddenStatusIds() {
        return array(self::STATUS_HIDDEN);
    }

    static public function getOptionArray() {
        return array(
            self::STATUS_ENABLED => Mage::helper('blog')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('blog')->__('Disabled'),
            self::STATUS_HIDDEN => Mage::helper('blog')->__('Hidden')
        );
    }

}
