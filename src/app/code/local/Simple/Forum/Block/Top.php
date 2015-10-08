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

class Simple_Forum_Block_Top extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
	{
	    parent::_prepareLayout();
	}
	
	public function getSearchBlock()
	{
		if(Mage::getStoreConfig('forum/forumconfiguration/allowsearchblock') == 1)
		{
			return $this->getChildHtml('forum_top_search');
		}
	}
	
	public function getJumpBlock()
	{
		if(Mage::getStoreConfig('forum/forumconfiguration/allowjumptoblock') == 1)
		{
			return $this->getChildHtml('forum_top_jump');
		}
	}
	
	public function getBookmarkItemsBlock()
	{
		if(Mage::getStoreConfig('forum/forumconfiguration/allowbookmarkblock') == 1)
		{
			return $this->getChildHtml('forum_top_bookmarks');
		}
	}
	
	public function getCurrentUrl()
	{
		return Mage::helper('core/url')->getCurrentUrl();
	}
	
	public function getSoreId()
	{
		return Mage::app()->getStore()->getId();
	}
}
?>