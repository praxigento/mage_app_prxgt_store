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

class Simple_Forum_Block_Myposts extends Mage_Core_Block_Template
{
	private $customer;
	private $isModerator_checked    = false;
	private $isModer                = false;

	public  $limits                 = array(5, 10, 15);

	const PAGE_VAR_NAME             = 'p';
	const LIMIT_VAR_NAME            = 'limit';
	const APPROVED_VAR              = 'ap';
	const SORT_VAR_NAME             = 's';

	public $massactions             = array(
		array(
			'label'   => '',
			'action'  => 'no_action',
			'confirm' => false
		),
		array(
			'label'   => 'Disabled',
			'action'  => 'forum/topic/massDisable',
			'confirm' => true
		),
		array(
			'label'   => 'Enabled',
			'action'  => 'forum/topic/massEnable',
			'confirm' => true
		),
		array(
			'label'   => 'Delete',
			'action'  => 'forum/topic/massDelete',
			'confirm' => true
		)
	);

	public $statuses                = array(1, 2, 3);
	public $_objectsCollection      = false;

	public $sort_type               = array(1 => 'desc', 2=> 'asc');

	protected function _prepareLayout()
	{
		$this->limits = Mage::helper('forum/topic')->getPagesLimits();
		$this->initCollection();
	    parent::_prepareLayout();
	}

	public function getMassActions()
	{
		return $this->massactions;
	}

	public function getAllPosts()
	{
		if(!$this->_objectsCollection)
		{
			$this->initCollection();
		}
		return $this->_objectsCollection;
	}

	public function initCollection()
	{
		$isModerator = Mage::helper('forum/topic')->isModerator();
		$this->customer = Mage::registry('current_customer');
		if(!$this->customer)
		{
			return;
		}
		$sort   = $this->getSort();
		$status = false;
		if($isModerator)$status = (int)($this->getStatus() - 1);
		$customerId = $this->customer->getId();
		$this->_objectsCollection = Mage::getModel('forum/post')->getCollection()
		->setPageSize($this->_getLimit())
		->setOrder('main_table.created_time', $sort)
		->setCurPage($this->_getCurPage());
		if(!$isModerator)
		{
			$this->_objectsCollection->getSelect()->where('main_table.status=1');
			$this->_objectsCollection->getSelect()->where('main_table.system_user_id=?', $customerId);
		}
		elseif($status && $isModerator)
		{
			$status--;
			$this->_objectsCollection->getSelect()->where('main_table.status=?', $status);
		}
		$this->_objectsCollection->addStoreFilterToCollection(Mage::app()->getStore()->getId());
		$this->setAdditionalData();
		return $this->_objectsCollection;
	}

	public function isModerator()
	{
		if(!$this->isModerator_checked)
		{
			$this->isModer             =  Mage::helper('forum/topic')->isModerator();
			$this->isModerator_checked = true;
			return $this->isModer;
		}
		else
		{
			return $this->isModer;
		}
	}

	public function setAdditionalData()
	{
		$this->setParentobject();
		$this->setForumObject();
	}

	public function setForumObject()
	{
		if($this->_objectsCollection->getSize())
		{
			foreach($this->_objectsCollection as $key=>$val)
			{
				$this->_objectsCollection->getItemById($key)->setForum_object(Mage::getModel('forum/topic')->load($val->getParent_object()->getParent_id()));
			}
		}
	}

	public function setParentobject()
	{
		if($this->_objectsCollection->getSize())
		{
			foreach($this->_objectsCollection as $key=>$val)
			{
				$this->_objectsCollection->getItemById($key)->setParent_object(Mage::getModel('forum/topic')->load($val->getParent_id()));
			}
		}
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
			return (!is_null(Mage::getSingleton('forum/session')->getPageMyPostsLimit())
				? Mage::getSingleton('forum/session')->getPageMyPostsLimit()
					: $this->limits[0]);
		}
	}

	public function getSort()
	{
		$sort = (int) $this->getRequest()->getParam($this->getSortVarName(), false);
		if($sort && array_key_exists($sort, $this->sort_type))
		{
			return $this->_setSort($sort);
		}

		$sort =  Mage::getSingleton('forum/session')->getPageSortMyPost()
			? Mage::getSingleton('forum/session')->getPageSortMyPost()
				: 1;
		if(array_key_exists($sort, $this->sort_type))
		{
			return $this->sort_type[$sort];
		}
		return $this->sort_type[1];
	}

	public function _setSort($sort)
	{
		Mage::getSingleton('forum/session')->setPageSortMyPost($sort);
		return $this->sort_type[$sort];
	}

	public function getSortUrl($sort)
	{
		return $this->getPagerUrl(array(
            self::SORT_VAR_NAME      => $sort,
            self::LIMIT_VAR_NAME     => null,
            self::PAGE_VAR_NAME      => null,
            self::APPROVED_VAR       => null,
        ));
	}

	public function getApprovedUrl($a)
	{
		return $this->getPagerUrl(array(
            self::SORT_VAR_NAME      => null,
            self::LIMIT_VAR_NAME     => null,
            self::PAGE_VAR_NAME      => null,
            self::APPROVED_VAR       => $a,
        ));
	}

	public function getFormatedDate($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_LONG);
    }

	private function getPagerUrl($params=array())
    {
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }

	public function getStatus()
	{
		$status = (int) $this->getRequest()->getParam(self::APPROVED_VAR, false);
		if($status && in_array($status, $this->statuses))
		{
			return $this->_setStatuses($status);
		}
		else
		{
			return (!is_null(Mage::getSingleton('forum/session')->getPageMyPostsStatus())
				? Mage::getSingleton('forum/session')->getPageMyPostsStatus()
					: $this->statuses[0]);
		}
	}

	public function _setStatuses($status)
	{
		Mage::getSingleton('forum/session')->setPageMyPostsStatus($status);
		return $status;
	}

	public function getSortVarName()
	{
		return self::SORT_VAR_NAME;
	}

	public function getUpproved()
	{
		return self::APPROVED_VAR;
	}

	public function getPageVarName()
	{
		return self::PAGE_VAR_NAME;
	}

	public function getLimitVarName()
	{
		return self::LIMIT_VAR_NAME;
	}

	public function _setLimit($limit)
	{
		Mage::getSingleton('forum/session')->setPageMyPostsLimit($limit);
		return $limit;
	}

	public function _getCurPage()
	{
		$page = (int) $this->getRequest()->getParam(self::PAGE_VAR_NAME, null);
		if($page == null)
		{
			return !is_null(Mage::getSingleton('forum/session')->getPageMyPostsCurrent() )
				? Mage::getSingleton('forum/session')->getPageMyPostsCurrent()
					: 1;
		}
		else
		{
			return $this->_setCurPage($page);
		}
	}

	public function _setCurPage($page = 1)
	{
		Mage::getSingleton('forum/session')->setPageMyPostsCurrent($page);
		return $page;
	}

	public function getToolbarHtml()
	{
		return $this->getChildHtml('toolbar');
	}

	public function getDeleteUrl($_id, $obj)
	{
		return $this->getBaseUrl() . 'forum/topic/deletePost/post_id/' . $_id . '?ret=' . $this->getRetUrl();
	}

	public function getViewPostUrl($_id)
	{
		return $this->getBaseUrl() . 'forum/topic/viewreply/id/' . $_id;
	}

	public function getEditUrl($_id, $obj)
	{
		return $this->getBaseUrl() . 'forum/topic/edit/id/' . $obj->getParent_id() . '/parent_id/' . Mage::getModel('forum/topic')->load($obj->getParent_id())->getParent_id() . '/post_id/' . $_id .'/?ret=' . $this->getRetUrl();
	}

	public function getDeactivatePostUrl($_id)
	{
		return $this->getBaseUrl() . 'forum/topic/disablePost/post_id/' . $_id . '?ret=' . $this->getRetUrl();

	}

	public function getActivatePostUrl($_id)
	{
		return $this->getBaseUrl() . 'forum/topic/enablePost/post_id/' . $_id . '?ret=' . $this->getRetUrl();

	}

	public function getRetUrl()
	{
		return urlencode('forum/myposts');
	}

	public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('forum/myposts/');
    }

	public function getViewUrl($id, $obj = false)
	{
		if($obj && $obj->getUrl_text() != '' && $obj->getUrl_text())
		{
			return $this->_getUrlrewrited( array(self::PAGE_VAR_NAME => 1), $obj->getUrl_text());
		}
		return $this->_getUrl( array(self::PAGE_VAR_NAME => 1), '/view/id/' . $id);
	}

	public function getForumUrl()
	{
		return $this->getUrl('forum');
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
?>