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


class AW_Blog_Block_Blog extends AW_Blog_Block_Abstract {

    public function getPosts() {

        $collection = parent::_prepareCollection();
      
        $tag = $this->getRequest()->getParam('tag');        
        if ($tag) {
            $collection->addTagFilter(urldecode($tag));
        }
       
        parent::_processCollection($collection);
         
        return $collection;
    }
  
    protected function _prepareLayout()
    {
        if ($this->isBlogPage()) {

            $breadcrumbs = $this->getCrumbs();

            parent::_prepareMetaData(self::$_helper);

            $tag = $this->getRequest()->getParam('tag', false);

            if ($tag) {
                $tag = urldecode($tag);
                $breadcrumbs->addCrumb('blog', array('label' => self::$_helper->getTitle(), 'title' => $this->__('Return to ' . self::$_helper->getTitle()), 'link' => $this->getBlogUrl()));
                $breadcrumbs->addCrumb('blog_tag', array('label' => $this->__('Tagged with "%s"', self::$_helper->convertSlashes($tag)), 'title' => $this->__('Tagged with "%s"', $tag)));
            } else {
                $breadcrumbs->addCrumb('blog', array('label' => self::$_helper->getTitle()));
            }
        }
    }
    
}
