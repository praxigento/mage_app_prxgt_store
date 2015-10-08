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

class Simple_Forum_Block_Object_Controls extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    
    protected function _isLogged()
    {
		$sess     =  Mage::getSingleton('customer/session');
		$customer = $sess->getCustomer()->getData();
		if(empty($customer['entity_id'])) return false;
		return true;
	}
	
	protected function _getLoginUrl()
	{
		return $this->getUrl('customer/account/login');
	}
	
	protected function _getAddNewTopicUrl()
	{
		$topic     = Mage::registry('current_object');
		$parent_id = $topic->getParent_id();
		return $this->getUrl('forum/topic/new/parent_id/' . $parent_id);
	}
	
	protected function _getAddNewPostUrl()
	{
		$topic = Mage::registry('current_object');
		$id = $topic->getId();
		return $this->getUrl('forum/topic/new/id/' . $id);
	}
	
	
	public function getIsCategory()
	{
		if($current_object = Mage::registry('current_object'))
		{
			if($current_object->getIs_category())
			{
				return $current_object->getIs_category();
			}
			if($current_object->getHave_subtopics())
			{
				return $current_object->getHave_subtopics();	
			}
		}
	}
	
	public function chooseControlsTemplate()
	{
		if (!$this->getIsCategory()) 
		{
			$this->setTemplate($this->getPostsControlsTemplate());
		} 
		else 
		{
			$this->setTemplate($this->getTopicsControlsTemplate());
		}
	}
	
	public function getInitTiny()
	{
		return $this->getChildHtml('bottom_tiny');
	}
	
	public function getAllowFastReplyButton()
	{
		return $this->getData('block_bottom');
	}
	
	public function _getAddNewTopicForumUrl()
	{
		$topic     = Mage::registry('current_object');
		$parent_id = $topic->getId();
		return $this->getUrl('forum/topic/new/parent_id/' . $parent_id);
	}
	
	public function getLangLocaleShort()
	{
		$cur_locale = Mage::app()->getLocale()->getDefaultLocale();
		return Mage::helper('forum/topic')->getAvLovale($cur_locale);
	}
}

?>