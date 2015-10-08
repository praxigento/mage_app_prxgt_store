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

class Simple_Forum_Model_Mysql4_Forum_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	private $increase_size_count  = 0;
	public  $last_page_number_tmp = 0;

    public function _construct()
    {
        parent::_construct();
        $this->_init('forum/forum');
    }

    public function addStoreFilterToCollection($store, $ret_if_null = false)
	{
		if (!Mage::app()->isSingleStoreMode())
		{
			if ($store instanceof Mage_Core_Model_Store)
			{
				$store = array($store->getId());
			}

			if($ret_if_null && !$store)
			{
				return;
			}

			$this->getSelect()->where('main_table.store_id = 0
					OR main_table.store_id = \''.$store.'\'
					OR main_table.store_id IS NULL
			');

			return $this;
		}
		return $this;
	}
}