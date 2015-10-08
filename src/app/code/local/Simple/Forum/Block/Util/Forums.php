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

class Simple_Forum_Block_Util_Forums extends Mage_Core_Block_Template
{
	const PAGE_VAR_NAME = 'p';
    private $max_forums_in_block = 5;

	protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    protected function getForums()
    {        $collection = Mage::getModel('forum/topic')->getCollection()
			->setPageSize($this->getTotalForums());
		$collection->setOrder('created_time', 'desc');

		$parent_id = $this->_getParent_id();
		if( $parent_id )
		{        	$collection->getSelect()->where('status=1')->where('parent_id=' . $parent_id);
		}
		else
		{
			$collection->getSelect()->where('status=1')->where('is_category=1');
		}
		$collection->addStoreFilterToCollection(Mage::app()->getStore()->getId());

		return $collection;
    }

    protected function getTotalForums()
    {
    	$l = $this->getForumCount();
    	if($l)
    	{
    		return $l;
    	}    	return $this->max_forums_in_block;
    }

    protected function _getTitle()
    {
    	$t = $this->getTitle();    	if($t)
    	{        	return $t;
    	}
    	return $this->__('Forum Menu');
    }

	protected function _getLink($obj)
	{        if($obj && $obj->getUrl_text() != '' && $obj->getUrl_text())
		{
			return $this->_getUrlrewrited( array(self::PAGE_VAR_NAME => 1), $obj->getUrl_text(), $obj->getStore_id());
		}
		return $this->_getUrl( array(self::PAGE_VAR_NAME => 1), '/view/id/' . $id);
	}

	protected function _getParent_id()
	{
		$parent_id = $this->getParent_id();    	if($parent_id)
    	{        	return intval($parent_id);
    	}
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
}

?>