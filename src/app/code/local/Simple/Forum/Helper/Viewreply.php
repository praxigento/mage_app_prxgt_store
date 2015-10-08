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

class Simple_Forum_Helper_Viewreply extends Mage_Core_Helper_Abstract
{
	private $pages_limits         = array
	(
		array(5, 10, 15),
		array(10, 15, 30),
		array(20, 30, 50)
	);
	
	private $REPLY_LIMIT            = 5;

	const REPLY_SORT                = 2;
	
	const REPLY_ORDER               = 'asc';
	
	const REPLY_ID_BEGIN            = 'simple-forum-post-';
	
	private $object_id = false;
	
	public function getViewReplyUrlObj($id)
	{
		$this->REPLY_LIMIT = $this->getPagesLimits();
		$this->object_id = $id;
		$obj         = $this->getReplyObject($id);
		if($this->validateNotShow($obj))       return $this->redirectToIndex();
		$parentObj   = $this->getReplyObjectParent($obj->getParentId()); 
		if($this->validateNotShow($parentObj)) return $this->redirectToIndex();
		$collection  = $this->getParentCollection($obj->getParentId());
		
		$page_number = $this->getPageNumber($collection, $id); 
		if($page_number == 0)
		{
			return $this->redirectToIndex();
		}
		$path = $this->getPath($parentObj, $id);
		$this->setSessions($page_number);
		return $this->getObject($path, array('_escape' => true, '_use_rewrite' => true));//, array( '_query' => array(self::PAGE_VAR_NAME => $page_number, self::LIMIT_VAR_NAME => self::REPLY_LIMIT, self::SORT_VAR_NAME => self::REPLY_SORT)));
	}
	
	public function getReplyIdBegin()
	{
		return self::REPLY_ID_BEGIN;
	}
	
	private function setSessions($page)
	{
		$session = Mage::getModel('forum/session');
		$session->setPageLimitPost($this->REPLY_LIMIT);
		$session->setPageSortPost(self::REPLY_SORT);
		$session->setPageCurrentPost($page);
	}
	
	private function getPath($obj, $id)
	{
		return '' . $obj->getUrl_text();
	}
	
	private function getPageNumber($c, $id)
	{
		if(!$c->getSize())
		{
			return false;
		}
		$num   = 1;
		$count = 1;
		foreach($c as $val)
		{
			if($count > $this->REPLY_LIMIT)
			{
				$count = 1;
				$num++;	
			}
			if($val->getId() == $id)
			{
				return $num;
			}
			$count++;
		}
		return false;
	}
	
	private function validateNotShow($obj)
	{
		if(!$obj->getId())
		{
			return true;		
		}
		if($obj->getStatus() == 0)
		{
			return true;
		}
	}
	
	private function redirectToIndex()
	{
		return $this->getObject();
	}
	
	private function getObject($path = '*/', $arguments = array())
	{
		$ret = new Varien_Object();	
		$data = array
		(
			'path' => $path,
			'arguments' => $arguments,
			'url' => $this->_getUrl($path, $arguments)
		);
		if($path != '*/')
		{
			$data['identifier'] = '#' . $this->getReplyIdBegin() . $this->object_id;
		}
		$ret->setData($data);
		return $ret;
	}
	
	private function getReplyObject($id)
	{
		return Mage::getModel('forum/post')->load($id);
	}
	
	private function getReplyObjectParent($id)
	{
		return Mage::getModel('forum/topic')->load($id);
	}
	
	private function getParentCollection($id)
	{
		$c = Mage::getModel('forum/post')->getCollection();
		$c->setOrder('created_time', self::REPLY_ORDER);
		$c->getSelect()->where('status=1')->where('parent_id=?', $id);
		
		return $c;
	}

	private function getPagesLimits()
	{
		$l = Mage::getStoreConfig('forum/forumconfiguration/pageslimits');
		$l = $l ? $l : 0;
		$arr =  $this->pages_limits[$l];
		return $arr[0];
	}
}

?>