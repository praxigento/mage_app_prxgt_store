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

class Simple_Forum_Block_Top_Bookmark extends Mage_Core_Block_Template
{
	public $bookmark_url = 'bookmark';
	
	public function getIsCategory()
	{
		if(Mage::registry('current_object'))
			return Mage::registry('current_object')->getIs_category();
		else 
			return false;
	}
	
	public function isDisplayBookmark()
	{
		if(!$this->getIsCategory() && Mage::registry('current_object'))
		{
			return true;
		}
		return false;
	}
	
	public function getAddBookmarkTitle()
	{
		return $this->__('Bookmark Topic');
	}
	
	public function getPageCurrent()
	{
		return (Mage::getSingleton('forum/session')->getPageCurrentPost() 
			? Mage::getSingleton('forum/session')->getPageCurrentPost() 
			: 1);
	}
	
	public function getLimit()
	{
		return Mage::getSingleton('forum/session')->getPageLimitPost() ? Mage::getSingleton('forum/session')->getPageLimitPost() : '0';
	}
	
	public function getTopicId()
	{
		return Mage::registry('current_object')->getId();
	}
	
	public function _getBookmarsUrl()
	{
		return $this->getBaseUrl() . 'forum/' . $this->bookmark_url;
	}
}

?>