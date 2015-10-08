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

class Simple_Forum_Block_Forum extends Mage_Core_Block_Template
{
	public  $limits                 = array(5, 10, 15);

	const PAGE_VAR_NAME             = 'p';
	const LIMIT_VAR_NAME            = 'limit';

 	const ADM_USER_ID = 10000000;

	private $user_profiles          = array();

	protected $_objectsCollection = false;

    protected function _prepareLayout()
    {
		$this->limits = Mage::helper('forum/topic')->getPagesLimits();
        parent::_prepareLayout();
        $root = $this->getLayout()->getBlock('root');
		$root->setTemplate(Mage::helper('forum/data')->getLayout());
        ///$this->initCollection();
        //$this->getLayout()->createBlock('forum/breadcrumbs');
    }

    protected function getAllForums()
    {
		if(!$this->_objectsCollection)
		{
			return $this->initCollection();
		}
		return $this->_objectsCollection;
	}

	protected function getTitleContent()
	{
		return Mage::helper('forum/topic')->__('View All Forums');
	}

	protected function getFormatedDate($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    protected function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    protected function getHeadHtml()
    {
        return $this->getChildHtml('head');
    }

    protected function getControls()
    {
		return $this->getChildHtml('controls');
	}

	public function getTop()
	{
		return $this->getChildHtml('forum_top');
	}

	public function getBreadCrumbs()
	{
		if(Mage::getStoreConfig('forum/forumconfiguration/allowbreadcrumbs'))
		{
			return $this->getChildHtml('util_breadcrumbs');
		}
	}

    public function initCollection()
    {
		if(!$this->_objectsCollection)
		{
			$this->_objectsCollection  =  Mage::getModel('forum/topic')->getCollection()
			->setPageSize($this->_getLimit())

			->setCurPage($this->_getCurPage());
			if(Mage::getStoreConfig('forum/forumconfiguration/sortpriorityforums'))
			{
				$this->_objectsCollection->setOrder('priority', 'asc');
			}
			else
			{
				$this->_objectsCollection->setOrder('created_time', 'desc');
			}
			$this->_objectsCollection->getSelect()->where('status=1')->where('is_category=1');
			$this->_objectsCollection->addStoreFilterToCollection(Mage::app()->getStore()->getId());
			$this->setAdditionalData();
		}

		return $this->_objectsCollection;
	}


    public function _getLimit()
    {
    	$limit = (int) $this->getRequest()->getParam(self::LIMIT_VAR_NAME, false);
		if($limit && in_array($limit, $this->limits))
		{
			return $this->_setLimit($limit);
		}
		else
		{
			return (!is_null(Mage::getSingleton('forum/session')->getPageForumLimit())
				? Mage::getSingleton('forum/session')->getPageForumLimit()
					: $this->limits[0]);
		}
	}

	public function _getCurPage()
	{
		$page = (int) $this->getRequest()->getParam(self::PAGE_VAR_NAME, null);
		if($page == null)
		{
			return !is_null(Mage::getSingleton('forum/session')->getPageForumCurrent() )
				? Mage::getSingleton('forum/session')->getPageForumCurrent()
					: 1;
		}
		else
		{
			return $this->_setCurPage($page);
		}
	}

	public function getPageVarName()
	{
		return self::PAGE_VAR_NAME;
	}

	public function getLimitVarName()
	{
		return self::LIMIT_VAR_NAME;
	}

	public function getViewUrl($id, $obj = false)
	{
		if($obj && $obj->getUrl_text() != '' && $obj->getUrl_text())
		{
			return $this->_getUrlrewrited( array(self::PAGE_VAR_NAME => 1), $obj->getUrl_text(), $obj->getStore_id());
		}
		return $this->_getUrl( array(self::PAGE_VAR_NAME => 1), '/view/id/' . $id);
	}

	public function getViewUrlLatest($id, $obj = false)
	{
		$urlParams = array();
        $urlParams['id']             = $id;
        return $this->getUrl( 'forum/topic/viewreply', $urlParams);
	}


	private function _getUrlrewrited($params, $urlAddon = '', $store_id = false)
	{
		$urlParams 					 = array();
        $urlParams['_current']  = false;
        $urlParams['_escape']   = false;
        $urlParams['_query']    	 = $params;
        return $this->getUrl( $urlAddon, $urlParams);
	}

	private function _getUrl($params, $urlAddon = '')
	{
		$urlParams = array();
        $urlParams['_current']  	 = false;
        $urlParams['_escape']   	 = false;
        $urlParams['_use_rewrite']   = false;
        $urlParams['_query']    	 = $params;
        return $this->getUrl('*/*' . $urlAddon, $urlParams);
	}

	private function _setCurPage($page = 1)
	{
		Mage::getSingleton('forum/session')->setPageForumCurrent($page);
		return $page;
	}

	private function _setLimit($limit)
	{
		Mage::getSingleton('forum/session')->setPageForumLimit($limit);
		return $limit;
	}

	private function _setPostsQuantity()
	{
		foreach($this->_objectsCollection as $key=>$val)
		{
			$this->_objectsCollection->getItemById($key)->setTotal_posts( Mage::helper('forum/topic')->getPostsQuantity( $val, 0, $val ) );
		}
	}

	private function _setTopicsQuantity()
	{
		foreach($this->_objectsCollection as $key=>$val)
		{
			$this->_objectsCollection->getItemById($key)->setTotal_topics( Mage::helper('forum/topic')->getTopicsQuantity( $val->getId() ) );
		}
	}

	private function _setLastPostInfo()
	{
		foreach($this->_objectsCollection as $key=>$val)
		{
			$obj = Mage::helper('forum/topic')->latestPosts;
			if(!empty($obj[$val->getId()]))
			{
				$this->_objectsCollection->getItemById($key)->setLatest_topic($obj[$val->getId()]['topic_obj']);
				$this->_objectsCollection->getItemById($key)->setLatest_created_time($obj[$val->getId()]['created_time']);
				$this->_objectsCollection->getItemById($key)->setLatest_user_name($this->getUserNick($obj[$val->getId()]['system_user_id'], $obj[$val->getId()]));
				$this->_objectsCollection->getItemById($key)->setLatest_user_id($obj[$val->getId()]['system_user_id']);
				$this->_objectsCollection->getItemById($key)->setLatest_postId($obj[$val->getId()]['post_id']);
			}
		}
	}

	public function getUserNick($user_id, $obj = false)
	{
		$user_id = $user_id != 0 ? $user_id : self::ADM_USER_ID;
		$profile = $this->_loadUserSettings($user_id);

		if($profile->getNickname())
		{        	return $profile->getNickname();
		}
		if($obj)
		{
			if(!empty($obj['user_nick']) || !empty($obj['user_name']))
			{	    		return ($obj['user_nick'] != '' && $obj['user_nick'] ? $obj['user_nick'] : $obj['user_name']);
	    	}
	    	if(is_object($obj))
	    	{                return ($obj->getUser_nick() != '' && $obj->getUser_nick() ? $obj->getUser_nick() : $obj->getUser_name());
			}
        }
	}

    private function _loadUserSettings($user_id)
	{
       	if(!empty($this->user_profiles[$user_id]))
       	{
           	return $this->user_profiles[$user_id];
       	}
        	$this->user_profiles[$user_id] = Mage::getModel('forum/usersettings')->load($user_id, 'system_user_id');
        	return $this->user_profiles[$user_id];
	}

	private function setAdditionalData()
	{
		$this->_setPostsQuantity();
		$this->_setTopicsQuantity();
		$this->_setLastPostInfo();
	}


}

?>