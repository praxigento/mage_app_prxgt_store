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

class Simple_Forum_Block_Top_Jump extends Mage_Core_Block_Template
{
	public $selected_url = '';
	
	protected function _prepareLayout()
	{
	    parent::_prepareLayout();
	}
	
	public function prepareData()
	{
		$data = Mage::helper('forum/topic')->getOptionsTopics(false, 0, $where = array(1=> 'status=?'), __('Forum index'), false, false,true, Mage::app()->getStore()->getId());
		return $data;
	}
	
	public function getSelected()
	{
		$o = Mage::registry('current_object');
		if($o)
		{
			return $o->getUrl_text();
		}
		return '';
	}
}
?>