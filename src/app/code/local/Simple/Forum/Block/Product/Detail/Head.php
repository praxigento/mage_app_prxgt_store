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

class Simple_Forum_Block_Product_Detail_Head extends Mage_Core_Block_Template
{
	
	private $_isGalleryDisabled = false;
		
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
	}
	
	public function getProduct()
	{
		return Mage::registry('current_product');
	}
	
	public function getGalleryImages()
    {
        if ($this->_isGalleryDisabled) {
            return array();
        }
        $collection = $this->getProduct()->getMediaGalleryImages();
        return $collection;
    }
    
    public function getProductBackLink()
    {
		$links = Mage::getModel('forum/session')->getBackProductLinks();
		$store = Mage::app()->getStore();
		if(!empty($links[$store->getId() . '_' . Mage::registry('current_product')->getid()]))
		{
			return $links[$store->getId() . '_' . Mage::registry('current_product')->getid()];
		}
		else
		{
			return $this->getBaseUrl() . Mage::registry('current_product')->getUrl_path();
		}
	}
	
	public function getForumTitle()
	{
		return Mage::getStoreConfig('forum/forumconfiguration/forum_title');
	}
	
	public function getDisplayRSS()
	{
		$action = $this->getRequest()->getActionName();
		if($action != 'edit')
		{
			return Mage::getStoreConfig('forum/forumconfiguration/allowrssfeeds');
		}
	}
	
	public function getRSSUrl()
	{
		return Mage::helper('forum/rss')->__getRSSUrl(); 
	}
    
    public function getTierPriceHtml()
    {
		
	}

}