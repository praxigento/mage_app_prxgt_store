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

class Simple_Forum_Model_Mysql4_Post_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	private $increase_size_count  = 0;
	public  $last_page_number_tmp = 0;

	public function getSize()
	{    	return intval(parent::getSize() + $this->increase_size_count);
	}

	public function setLastPageNumberTMP($page_number)
	{    	$this->last_page_number_tmp = $page_number;
	}

	public function getLastPageNumberTMP()
	{    	if($this->last_page_number_tmp && $this->last_page_number_tmp != 0)
    	{        	return $this->last_page_number_tmp;
    	}
	}

	public function _increaseSize($total)
	{		$this->increase_size_count = $total;
	}

    public function _construct()
    {
        parent::_construct();
        $this->_init('forum/post');
    }


    public function addStoreFilterToCollection($store)
	{
		if (!Mage::app()->isSingleStoreMode())
		{
			if ($store instanceof Mage_Core_Model_Store)
			{
				$store = array($store->getId());
			}

			$table_topics = $this->getTable('forum/topic');
        	$this->getSelect()->joinLeft(array('table_topics_store'=>$table_topics), 'main_table.parent_id = table_topics_store.topic_id', 'table_topics_store.status as topic_status');
			$this->getSelect()->where
			('
					table_topics_store.store_id = \''.$store.'\'
					OR table_topics_store.store_id = NULL
					OR table_topics_store.store_id = 0
			');
			return $this;
		}
		return $this;
	}
}