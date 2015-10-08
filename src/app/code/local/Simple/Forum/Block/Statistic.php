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


class Simple_Forum_Block_Statistic extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
	{
	    parent::_prepareLayout();
	}
	
	
	public function getTotalForums()
	{
		$store_id 	= Mage::app()->getStore()->getId();
		$c          = Mage::getModel('forum/topic')->getCollection();
		$c->getSelect()->where('status=?', 1)->where('is_category=?', 1);
		$c->addStoreFilterToCollection($store_id);
		return $c->getSize();
	}
	
	public function getTotalTopics()
	{
		$store_id 	= Mage::app()->getStore()->getId();
		$c          = Mage::getModel('forum/topic')->getCollection();
		$c->addStoreFilterToCollection($store_id);
		$c->getSelect()->where('main_table.status=?', 1)->where('main_table.is_category=?', 0);
		$table_topics = $c->getTable('forum/topic');
		$c->getSelect()->joinLeft(array('table_topics'=>$table_topics), 'main_table.parent_id = table_topics.topic_id', 'table_topics.title as parent_title');
		$c->getSelect()->where('table_topics.status=?', 1);
		$c->getSelect()->where('table_topics.store_id=? OR table_topics.store_id=NULL OR table_topics.store_id=0', $store_id);
		
		return $c->getSize();
	}
	
	public function getTotalPosts()
	{
		$c = Mage::getModel('forum/post')->getCollection();
		$c->getSelect()->where('main_table.status=?', 1);	
		$table_topics = $c->getTable('forum/topic');
		$c->getSelect()->joinLeft(array('table_topics'=>$table_topics), 'main_table.parent_id = table_topics.topic_id', 'table_topics.title as parent_title');
		$c->getSelect()->where('table_topics.status=?', 1);
		
		$c->getSelect()->joinLeft(array('table_forums'=>$table_topics), 'table_topics.parent_id = table_forums.topic_id', 'table_forums.title as forum_title');
		$c->getSelect()->where('table_forums.status=?', 1);
		
		$store_id = Mage::app()->getStore()->getId();	
		$c->addStoreFilterToCollection($store_id);
		return $c->getSize();
	}
	
	public function getTotalActiveCustomers()
	{
		$c = Mage::getModel('forum/user')->getCollection();
		$store_id = Mage::app()->getStore()->getId();	
		$c->addStoreFilterToCollection($store_id);
		return $c->getSize();
	}
}
?>