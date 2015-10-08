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

class Simple_Forum_Model_Mysql4_User_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('forum/user');
    }
    
    public function addStoreFilterToCollection($store)
	{
		if (!Mage::app()->isSingleStoreMode()) 
		{
			if ($store instanceof Mage_Core_Model_Store) 
			{
				$store = array($store->getId());
			}
			
			$this->getSelect()->where('
					main_table.store_id = \''.$store.'\' OR main_table.store_id = NULL OR main_table.store_id = 0
			');
			return $this;
		}
		return $this;
	}
}