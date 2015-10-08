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


class Simple_Forum_Model_Session extends Mage_Core_Model_Session_Abstract
{	
	public function __construct()
    {
        $this->init('forum');
        $this->updateViews();
    }
    
    public function getDisplayMode()
    {
        return $this->_getData('display_mode');
    }
    
    public function updateViews()
    {
    	$current_object = Mage::registry('current_object');
    	$store_id       = Mage::app()->getStore()->getId();
    	if($current_object)
    	{
			$topicId   = $current_object->getId();
			$parentId  = $current_object->getParent_id();
		}
		else
		{
			$topicId   = NULL;
			$parentId  = NULL;
		}
		$current_user = Mage::registry('current_customer');
		if(!$current_user)
		{
			return;
		}
		$id = session_id();
		$visitor_id = $this->getVisitorIdBySession($id);
		$m = Mage::getModel('forum/visitor')->setId($visitor_id)
		->setSystem_user_id($current_user->getId())
		->setSession_id($id)
		->setTopic_id($topicId)
		->setTime_visited(now())
		->setParent_id($parentId)
		->setStore_id($store_id)
		;	
		
		$m->save();
		
		$this->unsetOldVisitorEntries();
	}
	
	public function unsetOldVisitorEntries()
	{
		$_5min_back = date('Y-m-d H:i:s', strtotime(now()) - 300);
		
		$mc = Mage::getModel('forum/visitor')->getCollection();
		$mc->getSelect()->where('time_visited<?', $_5min_back);
		foreach($mc as $del)
		{
			Mage::getModel('forum/visitor')->setId($del->getId())->delete();
		}
	}
	
	public function getVisitorIdBySession($sessId)
	{
		$id = Mage::getModel('forum/visitor')->load($sessId, 'session_id')->getId();
		return $id;
	}
}

?>