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
 
class Simple_Forum_Block_Rss extends Mage_Rss_Block_Abstract
{
	protected $_objectsCollection   = false;
	private   $treeObject           = false;
	private   $parent_object        = false;
	private   $display_posts        = 15;
	private   $parent_topic         = false;
	
	private   $store_id             = 0;
	
	private $loaded_by_parent       = false;//flag
	
	const PAGE_VAR_NAME             = 'p';
	const LIMIT_VAR_NAME            = 'limit';
	const SORT_VAR_NAME             = 'sort';
    
    protected function _toHtml()
    {
    	$this->initStore();
    	$this->initCollection();
		$rssObj = Mage::getModel('rss/rss');
	
		$title = Mage::helper('forum/topic')->__('Forum');
		if($this->parent_object)
		{
			$title .= ' ' . $this->parent_object->getTitle();
		}
		if($this->parent_topic)
		{
			$title .= ' ' . Mage::helper('forum/topic')->__('Topic') . ' ' .  $this->parent_topic->getTitle();
		}
		$data = array('title' => $title,
			'description' => $title,
			'link'        => $this->getUrl('forum'),
			'charset'     => 'UTF-8'
		);
				
		$rssObj->_addHeader($data);			
		
		if ($this->_objectsCollection) {
			
			foreach ($this->_objectsCollection as $key=>$object) 
			{
				$data = array(
								'title'         => $object->getData('title'),
								'link'          => htmlentities($object->getData('url')),
								'description'   => $object->getData('postContent'),
								'createdTime' 	=> strtotime($object->getData('createdTime')),
							);
							
				$rssObj->_addEntry($data);
			}
		}

		try
		{
			return $rssObj->createRssXml();
		}
		catch(Exception $e)
		{
			echo $e->getMessage($this->__('Error create RSS!') . $e->getMessage());
		}
    }
    
    private function initStore()
    {
		$this->store_id = Mage::registry('store_id');
	}
    
    private function initCollection()
    {
		$parent = Mage::registry('rssParent');
		if($parent)
		{
			$object = $this->getObjectById($parent);
			if(!$object->getId()) return;	
			$this->loadData($object->getId());
		}
		else
		{
			$this->loadData();
		}
	}
	
	
	private function getObjectById($id)
	{
		return Mage::getModel('forum/topic')->load($id);
	}
	private function loadData($id = '0', $data = array())
	{
		$c = $this->getChildsByIdParent($id);	
		if($c)
		{
			foreach($c as $val)
			{
				if($this->getIsCategory($val) || $this->getIsParentTopic($val))
				{
					$this->loadData($val->getId());
				}	
				else
				{
					if($val)
					{
						$this->setPosts($val);
					}
				}
			}
		}
	}
	
	private function setPosts( $object_topic )
	{
		$posts = $this->getPostsByParentId($object_topic->getId());
		if($posts)
		{
			foreach($posts as $val)
			{
				$this->_objectsCollection[$object_topic->getId() . '_' .$val->getId()] = new Varien_Object;
				$data = array
				(
					'title'       => $this->__getTitle($val, $object_topic),
					'url'   	  => $this->__getUrl($val),
					'postContent' => $this->__getPostContent($val),
					'createdTime' => $val->getCreated_time()
				);
				$this->_objectsCollection[$object_topic->getId() . '_' .$val->getId()]->setData($data);
			}
		}
	}
	
	private function getChildsByIdParent($_parent_id)
	{
		$c = Mage::getModel('forum/topic')->getCollection();
		$c->addStoreFilterToCollection($this->store_id);
		$c->getSelect()->where('parent_id=?', $_parent_id)->where('status=1');
		
		if($c->getSize())
		{
			return $c;
		}
		else
		{
			return false;
		}
	}
	
	private function getIsCategory($obj)
	{
		if($obj->getIs_category())    return $obj->getIs_category();
	}
	
	private function getIsParentTopic($obj)
	{
		if($obj->getHave_subtopics()) return $obj->getHave_subtopics();
	}
	
	private function getPostsByParentId($_parent_id)
	{
		$c = Mage::getModel('forum/post')->getCollection();
		$c->setCurPage(1);
		$c->setPageSize($this->display_posts);
		$c->setOrder('created_time', 'desc');
		$c->getSelect()->where('parent_id=?', $_parent_id)->where('status=1');
		if($c->getSize())
		{
			return $c;
		}
	}
	
	private function __getTitle($object_post, $topic_obj)
	{
			return $this->__('Topic') . ' ' . $topic_obj->getTitle() . ' ' . $this->__('Post') . ': ' . $object_post->getId();
	}
	
	private function __getUrl( $post )
	{
		return Mage::helper('forum/rss')->__getUrl( $post );	
	}
	
	private function __getPostContent($object_post)
	{
		return $object_post->getPost();
	}
}

?>