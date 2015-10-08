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
 
class Simple_Forum_Block_Search extends Mage_Core_Block_Template
{
	public  $limits                 = array(5, 10, 15);
	
	const PAGE_VAR_NAME             = 'p';
	const LIMIT_VAR_NAME            = 'limit';
	
	const SEARCH_BY_VAR_NAME        = 'search_by';
	
	private $search_by_types        = array
	(
		'search_by_post'  => 'search_by_post',
		'search_by_topic' => 'search_by_topic',
	);
	
	protected $_objectsCollection = false;
	public $search;
	
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $root = $this->getLayout()->getBlock('root');
		$root->setTemplate(Mage::helper('forum/data')->getLayout());
        $this->initCollection();
        $this->getLayout()->createBlock('forum/search_breadcrumbs');
    }
    
    protected function getSearchValue()
    {
		if(!$this->search)
		{
			$this->search = Mage::registry('search_value_page');
		}
		return $this->search;
	}
	
	protected function getSearchValueSQL()
	{
		$ret = $this->getSearchValue();
		return $ret;
	}
    
    protected function getSearchResult()
    {
		if(!$this->_objectsCollection)
		{
			return $this->initCollection();
		}
		return $this->_objectsCollection; 
	}
	
	protected function getTitleContent()
	{
		return Mage::helper('forum/topic')->__('Search Result for') . ": " . '"' . $this->getSearchValue() . '"';
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
    
    public function initCollection()
    {
		$search_type = $this->getSearchType();
		if(!$this->_objectsCollection)
		{
			if(is_null(Mage::getSingleton('forum/session')->getSearchType()) || Mage::getSingleton('forum/session')->getSearchType() == 'search_by_post')
			{
				$this->_objectsCollection = Mage::getModel('forum/post')->getCollection()
					->setPageSize($this->_getLimit())
					->setOrder('created_time', 'desc')
					->setCurPage($this->_getCurPage());
				$table_topics = $this->_objectsCollection->getTable('forum/topic');
				$this->_objectsCollection->getSelect()->where('main_table.post_orig RLIKE ?', $this->getSearchValueSQL());
				$this->_objectsCollection->getSelect()->where('main_table.status=?', 1);
				$this->_objectsCollection->getSelect()->joinLeft(array('table_topics'=>$table_topics), 'main_table.parent_id = table_topics.topic_id', 'table_topics.title as parent_title');
				$this->_objectsCollection->getSelect()->where('table_topics.status=?', 1);
				$this->_objectsCollection->getSelect()->joinLeft(array('table_forums'=>$table_topics), 'table_topics.parent_id = table_forums.topic_id', 'table_forums.title as forum_title');
				$this->_objectsCollection->getSelect()->where('table_forums.status=?', 1);
				$this->_objectsCollection->addStoreFilterToCollection(Mage::app()->getStore()->getId());
				$this->setAdditionalData();
			}
			elseif(Mage::getSingleton('forum/session')->getSearchType() == 'search_by_topic')
			{
				$this->_objectsCollection = Mage::getModel('forum/topic')->getCollection()
					->setPageSize($this->_getLimit())
					->setOrder('created_time', 'desc')
					->setCurPage($this->_getCurPage());
				
				$this->_objectsCollection->getSelect()->where('main_table.title RLIKE ?', $this->getSearchValueSQL());
				$this->_objectsCollection->getSelect()->where('main_table.status=?', 1);
				$this->_objectsCollection->addStoreFilterToCollection(Mage::app()->getStore()->getId());
				
				$this->setAdditionalDataTopic();
			}
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
			return (!is_null(Mage::getSingleton('forum/session')->getPageSearchLimit())
				? Mage::getSingleton('forum/session')->getPageSearchLimit() 
					: $this->limits[0]);
		}
	}
	
	public function _getCurPage()
	{
		$page = (int) $this->getRequest()->getParam(self::PAGE_VAR_NAME, null);
		if($page == null)
		{
			return !is_null(Mage::getSingleton('forum/session')->getPageSearchCurrent() )
				? Mage::getSingleton('forum/session')->getPageSearchCurrent() 
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
			return $this->_getUrlrewrited( array(self::PAGE_VAR_NAME => 1), $obj->getUrl_text());	
		}
		return $this->_getUrl( array(self::PAGE_VAR_NAME => 1), '/view/id/' . $id);
	}
	
	public function getSearchByVarName()
	{
		return self::SEARCH_BY_VAR_NAME;
	}
	
	public function getSearchType()
	{
		$var         = $this->getSearchByVarName();
		$search_type =  $this->getRequest()->getParam($var, null);
		if($search_type && array_key_exists( $search_type, $this->search_by_types ))
		{
			$search_type = $this->setSearchType($search_type);
			return $search_type;
		}
		
		return (!is_null(Mage::getSingleton('forum/session')->getSearchType())
				? Mage::getSingleton('forum/session')->getSearchType() 
					: $this->search_by_types['search_by_post']);
	}
	
	public function setSearchType($value)
	{
		Mage::getSingleton('forum/session')->setSearchType($value);
		return $value;
	}
	
	public function getIsSearchTypeByPost()
	{
		if(is_null(Mage::getSingleton('forum/session')->getSearchType()) || Mage::getSingleton('forum/session')->getSearchType() == 'search_by_post')
		{
			return true;
		}
	}
	
	
	public function getIsSearchTypeByTopic()
	{
		if( Mage::getSingleton('forum/session')->getSearchType() == 'search_by_topic')
		{
			return true;
		}
	}
	
	public function getSearchByPostContentUrl()
	{
		$var = $this->getSearchByVarName();
		$urlParams = array();
        $urlParams['_current']  = false;
        $urlParams[$var]        = $this->search_by_types['search_by_post'];
		//$urlParams[self::PAGE_VAR_NAME]        = 1;
		return $this->getUrl( '*/*/*', $urlParams);
	}
	
	public function getSearchByTopicTitleUrl()
	{
		$var = $this->getSearchByVarName();
		$urlParams = array();
        $urlParams['_current']  = false;
        $urlParams[$var]        = $this->search_by_types['search_by_topic'];
        //$urlParams[self::PAGE_VAR_NAME]        = 1;
		return $this->getUrl( '*/*/*', $urlParams);
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
        $urlParams['_current']  = false;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        return $this->getUrl('*/*' . $urlAddon, $urlParams);
	}
	
	private function _setCurPage($page = 1)
	{
		Mage::getSingleton('forum/session')->setPageSearchCurrent($page);
		return $page; 	
	}
	
	private function _setLimit($limit)
	{
		Mage::getSingleton('forum/session')->setPageSearchLimit($limit);
		return $limit;
	}
    
    
    private function setAdditionalData()
    {
		if($this->_objectsCollection->getSize())
		{
			foreach($this->_objectsCollection as $key=>$val)
			{
				$this->_objectsCollection->getItemById($key)->setPost_search($this->getPostForSearch($val->getPost()));
				$this->_objectsCollection->getItemById($key)->setParentTopic($this->getParentTopic($val->getParent_id()));
			}
		}
	}
	
	private function setAdditionalDataTopic()
	{
		if($this->_objectsCollection->getSize())
		{
			foreach($this->_objectsCollection as $key=>$val)
			{
				//$this->_objectsCollection->getItemById($key)->setPost_search($this->getPostForSearch($val->getPost()));
				$this->_objectsCollection->getItemById($key)->setTopicTitle($this->prepareTopicTitle($val->getTitle()));
			}
		}
	}
	
	private function prepareTopicTitle($_title)
	{
		return Mage::helper('forum/post')->preparePostBySearchValue($_title);
	}
	
	private function getPostForSearch($_post)
	{
		return Mage::helper('forum/post')->preparePostBySearchValue($_post);
	}
	
	private function getParentTopic($_id)
	{
		return Mage::getModel('forum/topic')->load($_id);
	}
}

?>