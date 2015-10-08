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

class Simple_Forum_Block_Bookmark extends Mage_Core_Block_Template
{	
	protected $_objects = false;
	
	const PAGE_VAR_NAME             = 'p';
	const LIMIT_VAR_NAME            = 'limit';
	
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->initCollection();
        $root = $this->getLayout()->getBlock('root');
        $root->setTemplate(Mage::helper('forum/data')->getLayout());
        Mage::helper('forum/topic')->breadCrumbBlock = Mage::helper('forum/topic')->__('My Bookmarks');
        //$this->getLayout()->createBlock('forum/breadcrumbs');
    }
    
    protected function getAllBookmarks()
    {
		if(!$this->_objects)
		{
			return $this->initCollection();
		}
		return $this->_objects; 
	}
	
	protected function getTitleContent()
	{
		return Mage::helper('forum/topic')->__('My Bookmarks');
	}
	
    public function initCollection()
    {
		if(!$this->_objects)
		{
			$this->_objects  = array();
			$post = $this->getRequest()->getPost();
			
			if(empty($post['bookmark_forum']['_id']))
			{
				return;
			}
			if(!is_array($post['bookmark_forum']['_id']))
			{
				return;
			}
			
			foreach($post['bookmark_forum']['_id'] as $id)
			{
				$obj = Mage::getModel('forum/topic')->load($id);
				if($obj->getId() && $obj->getStatus() == 1)
				{
					$this->_objects[$id] = 	$obj;
					if($obj->getParent_id())
					{
						$parent = $this->getParentObject($obj->getParent_id());
						if($parent->getId() && $parent->getStatus())
						{	
							$this->_objects[$id]['parent'] = $parent ? $parent : null;
						}
					}
				}
			}
		}
		
		return $this->_objects;
	}
	
	public function getParentObject($id)
	{
		$parent = Mage::getModel('forum/topic')->load($id);
		if($parent->getParent_id())
		{
			return $this->getParentObject($parent->getParent_id());
		}
		else
		{
			return $parent;
		}
	}

	public function getBookmarkLimit($id)
	{
		$post = $this->getRequest()->getPost();
		if(!empty($post['bookmark_forum']['_limit']))
		{
			if(!empty($post['bookmark_forum']['_limit'][$id]))
			{
				return $post['bookmark_forum']['_limit'][$id];
			}
		}
	}
	
	public function getBookmarkPage($id)
	{
		$post = $this->getRequest()->getPost();
		if(!empty($post['bookmark_forum']['_page']))
		{
			if(!empty($post['bookmark_forum']['_page'][$id]))
			{
				return $post['bookmark_forum']['_page'][$id];
			}
		}
	}
	
	public function getTop()
	{
		return $this->getChildHtml('forum_top');
	}
	
	public function getHeadHtml()
    {
        return $this->getChildHtml('head');
    }
	
	public function getViewUrl($id, $obj = false, $page = 1)
	{
		if($obj && $obj->getUrl_text() != '' && $obj->getUrl_text())
		{
			return $this->_getUrlrewrited( array(self::PAGE_VAR_NAME => $page), $obj->getUrl_text());	
		}
		return $this->_getUrl( array(self::PAGE_VAR_NAME => $page), '/view/id/' . $id);
	}
	
	public function getBackUrl()
	{
		if(Mage::registry('redirect') && Mage::registry('redirect') != false)
    	{
			return  Mage::registry('redirect');
		}
		return $this->getUrl('forum/topic');
	}
	
	private function _getUrlrewrited($params, $urlAddon = '')
	{
		$urlParams = array();
        $urlParams['_current']  = false;
        $urlParams['_escape']   = false;
        $urlParams['_use_rewrite']   = false;
        $urlParams['_query']    = $params;
        return $this->getUrl( $urlAddon, $urlParams);
	}
	
	private function _getUrl($params, $urlAddon = '')
	{
		$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        return $this->getUrl('*/*' . $urlAddon, $urlParams);
	}
	
}