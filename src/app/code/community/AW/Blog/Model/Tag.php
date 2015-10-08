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


class AW_Blog_Model_Tag extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('blog/tag');
    }

    public function refreshCount($store = null) {
        //Refreshes tag count
        $postsCount = Mage::getModel('blog/blog')
                ->getCollection();
        if ($store) {
            $postsCount->addStoreFilter($store);
        }
        $postsCount = $postsCount->addTagFilter($this->getTag())
                ->count();
        //var_dump($postsCount);die();


        $this->setTagCount($postsCount)->save();
        return $this;
    }

    public function loadByName($name, $store = null) {
        $coll = Mage::getModel('blog/tag')->getCollection();

        $sel = $coll->getSelect();

        $coll->getSelect()
                ->where('tag=?', $name);
        if (!Mage::app()->isSingleStoreMode() && !is_null($store)) {
            $coll->getSelect()->where('store_id=?', $store);
        }


        foreach ($coll->load() as $item) {
            return $item;
        }

        if (!is_null($store)) {
            $this->setStoreId($store);
        }
        return $this->setTag($name);
    }

}
