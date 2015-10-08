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


class AW_Blog_Model_Post extends Mage_Core_Model_Abstract {
    const NOROUTE_PAGE_ID = 'no-route';

    protected function _construct() {
        $this->_init('blog/post');
    }

    public function load($id, $field=null) {
        return $post = parent::load($id, $field);
    }

    public function noRoutePage() {
        $this->setData($this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName()));
        return $this;
    }

    public function getShortContent() {
        $content = $this->getData('short_content');
        if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }

    public function getPostContent() {
        $content = $this->getData('post_content');
        if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }
    
    public function getCategoriesForPosts($posts = array())
    {        
        return $this->getResource()->getCategoriesForPost($posts);
         
    }

    public function loadByIdentifier($v) {
        return $this->load($v, 'identifier');
    }

    public function getCats() {

        $route = Mage::getStoreConfig('blog/blog/route');
        if ($route == "") {
            $route = "blog";
        }
        $route = Mage::getUrl($route);

        $cats = Mage::getModel('blog/cat')->getCollection()
                ->addPostFilter($this->getId())
                ->addStoreFilter(Mage::app()->getStore()->getId());

        $catUrls = array();
        foreach ($cats as $cat) {
            $catUrls[$cat->getTitle()] = $route . "cat/" . $cat->getIdentifier();
        }
        return $catUrls;
    }
   
}
