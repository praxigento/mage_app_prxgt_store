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

class Simple_Forum_Block_Object_Toolbar extends Mage_Core_Block_Template
{

	private $_limit           = 0;
	private $_sort            = 1;
	private $_collection      = array();
	private $_limit_avaiable  = array();
	private $ObjectBlockName  = 'forum/view';
	private $ForumBlockName   = 'forum/forum';
	private $ForumSearchName  = 'forum/search';

	private $MyForumTopicsBlockName = 'forum/mytopics';
	private $MyForumPostsBlockName  = 'forum/myposts';

	private $MyPrivateMessagesBlockName = 'forum/myprivatemessages';

	private $page_var_name    = false;
	private $limit_var_name   = false;
	private $sort_var_name    = false;

	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->initPager();
    }

	protected function isLimitCurrent($is_limit)
	{
		return $is_limit == $this->_limit;
	}

	protected function getLimitUrl($limit)
    {
        return $this->getPagerUrl(array(
            $this->limit_var_name    => $limit,
            $this->sort_var_name     => null,
            $this->page_var_name     => 1
        ));
    }

    protected function isSortCurrent($sort)
    {
		return $sort == $this->_sort;
	}

    protected function getSortUrl($sort)
    {
		return $this->getPagerUrl(array(
            $this->sort_var_name    => $sort,
            $this->page_var_name     => null,
            $this->limit_var_name    => null
        ));
	}

    protected function getLastNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize()*($collection->getCurPage()-1)+$collection->count();
    }

    protected function getFirstNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize()*($collection->getCurPage()-1)+1;
    }

    protected function getLastPageNum()
    {
        return $this->getCollection()->getLastPageNumber();
    }

    protected function getTotalNum()
    {
        return $this->getCollection()->getSize();
    }

	protected function getAvailableLimit()
	{
		return $this->_limit_avaiable;
	}

	protected function getCollection()
	{
		return $this->_collection;
	}

	protected function getPagerHtml()
    {
    	$this->initPager();
        $pagerBlock = $this->getChild('toolbar_pager');
		if ($pagerBlock instanceof Varien_Object) {

            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());
			$collection = $this->getCollection();
			$collection->setCurPage($this->getCurPage());
            $pagerBlock->setUseContainer(false);
            $pagerBlock->setShowPerPage(false);
            $pagerBlock->setShowAmounts(false);
            $pagerBlock->setLimitVarName($this->limit_var_name);
            $pagerBlock->setPageVarName($this->page_var_name);
            $pagerBlock->setLimit($this->getLimit());
            /*$pagerBlock->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame')); */
            $pagerBlock->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'));
			$pagerBlock->setLimit($this->getLimit());
            $pagerBlock->setCollection($collection);

            return $pagerBlock->toHtml();
        }

        return '';
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

	private function getObjectBlock($name)
	{
		if ($blockName = $this->getObjectBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($name, microtime());
        return $block;
	}

	private function getObjectBlockName()
	{
		return $this->ObjectBlockName;
	}

	private function getForumBlock($name)
	{
		if ($blockName = $this->getForumBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($name, microtime());
        return $block;
	}

	private function getForumBlockName()
	{
		return $this->ForumBlockName;
	}

	private function getSearchBlock($name)
	{
		if ($blockName = $this->getSearchBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($name, microtime());
        return $block;
	}

	private function getSearchBlockName()
	{
		return $this->ForumSearchName;
	}

	private function initPager()
	{
		if( Mage::registry('current_object') )
		{
			$object = $this->getObjectBlock($this->ObjectBlockName);
		}
		elseif(Mage::registry('search_value_page'))
		{
			$object = $this->getSearchBlock($this->ForumSearchName);
		}
		elseif(Mage::registry('myprivatemessages_inbox') || Mage::registry('myprivatemessages_sent') || Mage::registry('myprivatemessages_trash'))//private messages
		{            $object = $this->getMyprivateMessagesBlock($this->MyPrivateMessagesBlockName);
		}
		else
		{
			if(Mage::registry('myforumtopics'))
			{
				$object = $this->getForumMyTopicsBlock($this->MyForumTopicsBlockName);
			}
			elseif(Mage::registry('myforumposts'))
			{
				$object = $this->getForumMyPostsBlock($this->MyForumPostsBlockName);
			}
			else
			{
				$object = $this->getForumBlock($this->ForumBlockName);
			}
		}
		if($object)
		{
			$this->_collection     = $object->initCollection();
			$this->_limit_avaiable = $object->limits;
			$this->page_var_name   = $object->getPageVarName();
			$this->limit_var_name  = $object->getLimitVarName();
			$this->sort_var_name   = $object->getSortVarName();
			$this->_limit          = $object->_getLimit();
			$this->curPage         = $object->_getCurPage();
			$this->_sort           = $object->getSort();
			$this->_collection->setCurPage($this->curPage);
		}
	}

	public function getForumMyTopicsBlock($name)
	{
		if ($blockName = $this->getForumMyTopicsBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($name, microtime());
        return $block;
	}

	public function getForumMyTopicsBlockName()
	{
		return $this->MyForumTopicsBlockName;
	}

	public function getMyprivateMessagesBlock($name)
	{        if ($blockName = $this->getForumMyPrivateMessagesBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($name, microtime());
        return $block;
	}

	public function getForumMyPrivateMessagesBlockName()
	{    	return $this->MyPrivateMessagesBlockName;
	}

	public function getForumMyPostsBlock($name)
	{
		if ($blockName = $this->getForumMyPostsBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($name, microtime());
        return $block;
	}

	public function getForumMyPostsBlockName()
	{
		return $this->MyForumPostsBlockName;
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

	public function chooseToolbarTemplate()
	{
		if (!$this->getIsCategory() )
		{
			$this->setTemplate($this->getPostsToolbarTemplate());
		}
		else
		{
			$this->setTemplate($this->getTopicsToolbarTemplate());
		}
	}
}

?>