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

class Simple_Forum_Block_Util_Lastposts extends Mage_Core_Block_Template
{
	const PAGE_VAR_NAME = 'p';
 	const ADM_USER_ID   = 10000000;
    private $max_posts_in_block = 5;

	protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    protected function getPosts()
    {        $collection = Mage::getModel('forum/post')->getCollection()
			->setPageSize($this->getTotalPosts());

		$table_topics = $collection->getTable('forum/topic');

		$table_topics_parent = $table_forums = $table_topics;

		$parent_id = $this->_getParent_id();
		if( $parent_id )
		{
         	$collection->setOrder('main_table.created_time', 'desc');
			//$collection->getSelect()->where('main_table.status=1');
			$collection->getSelect()->joinLeft(array('table_topics_parent_last'=>$table_topics), 'main_table.parent_id = table_topics_parent_last.topic_id', 'table_topics_parent_last.title as parent_name');
			$collection->getSelect()->joinLeft(array('table_topics_parent_last_2'=>$table_topics_parent), 'table_topics_parent_last.parent_id = table_topics_parent_last_2.topic_id', '');
			$collection->getSelect()->joinLeft(array('table_topics_parent_last_3'=>$table_forums), 'table_topics_parent_last_2.parent_id = table_topics_parent_last_3.topic_id AND table_topics_parent_last_2.parent_id != 0', '');
            $collection->getSelect()->where('(table_topics_parent_last.status=1 AND table_topics_parent_last.have_subtopics=0 AND main_table.status=1) AND (table_topics_parent_last_2.topic_id = \''.$parent_id.'\' OR table_topics_parent_last_3.topic_id = \''.$parent_id.'\')');
		}
		else
		{

			$collection->setOrder('main_table.created_time', 'desc');
			$collection->getSelect()->where('main_table.status=1');
			$collection->getSelect()->joinLeft(array('table_topics_parent_last'=>$table_topics), 'main_table.parent_id = table_topics_parent_last.topic_id', 'table_topics_parent_last.title as parent_name');
			$collection->getSelect()->where('table_topics_parent_last.status=1');
			$collection->getSelect()->where('table_topics_parent_last.have_subtopics=0');
		}

		$collection->addStoreFilterToCollection(Mage::app()->getStore()->getId());

		return $collection;
    }

    protected function getTotalPosts()
    {
    	$l = $this->getPostCount();
    	if($l)
    	{
    		return $l;
    	}    	return $this->$max_posts_in_block;
    }

    protected function _getParent_id()
	{
		$parent_id = $this->getParent_id();
    	if($parent_id)
    	{
        	return intval($parent_id);
    	}
	}

    protected function _getTitle()
    {
    	$t = $this->getTitle();    	if($t)
    	{        	return $t;
    	}
    	return $this->__('Latest Posts');
    }

    public function getPosted_by($obj)
    {        $system_user_id = $obj->getSystem_user_id() ? $obj->getSystem_user_id() : self::ADM_USER_ID;
        if($system_user_id)
        {	        $o = Mage::getModel('forum/usersettings')->load($system_user_id, 'system_user_id');
	        if($o->getId())
	        {            	$nick = $o->getNickname();
	        }
	        else
	        {         		$nick = $obj->getUser_name();
	        }
        }
        else
        {        	$nick = $obj->getUser_name();
        }
        return Mage::helper('forum/customer')->getCustomerLink($system_user_id, $nick);
    }

    public function getFormatedDate($obj)
    {
    	$date = $obj->getCreated_time();
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

	public function getViewUrlLatest($id, $obj = false)
	{
		$urlParams = array();
        $urlParams['id']             = $id;
        return $this->getUrl( 'forum/topic/viewreply', $urlParams);
	}
}

?>