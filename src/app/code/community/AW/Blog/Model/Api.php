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


class AW_Blog_Model_Api extends Mage_Core_Model_Abstract {

    public function _construct() {

        parent::_construct();
        $this->_init('blog/blog');
    }

    public function getPostUrl($id) {

        $post = $this->load($id);

        if ($post->getId()) {

            $route = Mage::helper('blog')->getRoute();
            return Mage::getUrl("{$route}/{$post->getIdentifier()}");
        }

        return false;
    }

    public function getPostCategories($id) {

        return Mage::getModel('blog/cat')
                        ->getCollection()
                        ->addStoreFilter(Mage::app()->getStore()->getId())
                        ->addPostFilter($id);
    }

    public function getPosts($status = array(), $store = array()) {

        $collection = Mage::getModel('blog/post')->getCollection();

        if (is_array($store) && !empty($store)) {
            $collection->addStoreFilter($store);
        }

        if (!empty($status)) {
            $collection->addStatusFilter($status);
        } else {
            $collection->addStatusFilter();
        }

        return $collection;
    }

    public function getPostShortContent($post, $storeId = 0) {

        $content = $post->getPostContent();

        if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_USESHORTCONTENT, $storeId) && trim($post->getShortContent())) {

            $content = trim($post->getShortContent());
        } elseif ((int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE, $storeId)) {

            $strManager = new AW_Blog_Helper_Substring(array('input' => Mage::helper('blog')->filterWYS($post->getPostContent())));
            $content = $strManager->getHtmlSubstr((int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE));
        }

        return $content;
    }

}
