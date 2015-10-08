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


class Simple_Forum_Block_Whoisonline extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
	{
	    parent::_prepareLayout();
	}
	
	
	public function getTotaUsers($is_object = false)
	{
		$store_id = Mage::app()->getStore()->getId();
		$mc = Mage::getModel('forum/visitor')->getCollection();
		$mc->addStoreFilterToCollection($store_id);
		$add  = 0;
		if($is_object)
		{
			$id = $this->getTopicId();
			if($id)
			{
				$mc->getSelect()->where('topic_id=?', $id);		
				$parentId  = $this->getParentId();
				if($parentId)
				{
					$mc2 = Mage::getModel('forum/visitor')->getCollection()->addStoreFilterToCollection($store_id);
					$mc2->getSelect()->where('parent_id=?', $parentId);		
					$add = $mc2->getSize();
				}
			}
		}
		return $mc->getSize() + $add;
	}
	
	public function getRigisteredUsers($is_object = false)
	{
		$store_id = Mage::app()->getStore()->getId();
		$mc = Mage::getModel('forum/visitor')->getCollection();
		$mc->addStoreFilterToCollection($store_id);
		$mc->getSelect()->where('system_user_id!=?', 0);
		$add  = 0;
		if($is_object)
		{
			$id = $this->getTopicId();
			if($id)
			{
				$mc->getSelect()->where('topic_id=?', $id);		
				$parentId  = $this->getParentId();
				if($parentId)
				{
					$mc2 = Mage::getModel('forum/visitor')->getCollection()->addStoreFilterToCollection($store_id);
					$mc2->getSelect()->where('system_user_id!=?', 0);
					$mc2->getSelect()->where('parent_id=?', $parentId);		
					$add = $mc2->getSize();
				}
			}
		}
		return $mc->getSize() + $add;
	}
	
	public function getGuests($is_object = false)
	{
		$store_id = Mage::app()->getStore()->getId();
		$mc = Mage::getModel('forum/visitor')->getCollection()->addStoreFilterToCollection($store_id);
		$mc->getSelect()->where('system_user_id=?', 0);
		$add  = 0;
		if($is_object)
		{
			$id = $this->getTopicId();
			if($id)
			{
				$mc->getSelect()->where('topic_id=?', $id);		
				$parentId  = $this->getParentId();
				if($parentId)
				{
					$mc2 = Mage::getModel('forum/visitor')->getCollection()->addStoreFilterToCollection($store_id);
					$mc2->getSelect()->where('system_user_id=?', 0);
					$mc2->getSelect()->where('parent_id=?', $parentId);		
					$add = $mc2->getSize();
				}
			}
		}
		return $mc->getSize() + $add;
	}
	
	private function getTopicId()
	{
		$current_object = Mage::registry('current_object');
		if($current_object)
		{
			return $current_object->getId();
		}
	}
	
	private function getParentId()
	{
		$current_object = Mage::registry('current_object');
		if($current_object && $this->getIsCategory())
		{
			return $current_object->getId();
		}
	}
	
	public function chooseWhoisonlineTemplate()
	{
		if (!$this->getIsCategory()) 
		{
			$this->setTemplate($this->getPostsWhoisonlineTemplate());
		} 
		else 
		{
			$this->setTemplate($this->getTopicsWhoisonlineTemplate());
		}
	}
	
	public function getIsCategory()
    {
    	if(Mage::registry('current_object'))
			return Mage::registry('current_object')->getIs_category();
		else 
			return false;
	}
}
?>