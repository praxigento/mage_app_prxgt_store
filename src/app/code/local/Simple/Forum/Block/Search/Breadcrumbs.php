<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

class Simple_Forum_Block_Search_Breadcrumbs extends Mage_Core_Block_Template
{

    public function getTitleSeparator($store = null)
    {
        $separator = (string)Mage::getStoreConfig('catalog/seo/title_separator', $store);
        return ' ' . $separator . ' ';
    }

    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('forum')->__('Home'),
                'title'=>Mage::helper('forum')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));

            $title = array();
            $path  = Mage::helper('forum/topic')->getBreadcrumbPath(Mage::getBaseUrl(), false, true);

            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title[] = $breadcrumb['label'];
            }
			$breadcrumbsBlock->addCrumb('search', array(
                'label'=>Mage::helper('forum')->__('Search'),
                'title'=>Mage::helper('forum')->__('Search Result'),
            ));
            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)));
            }
        }
        return parent::_prepareLayout();
    }
}