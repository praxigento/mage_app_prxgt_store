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

class Simple_Forum_Block_Adminhtml_Head extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
    }

	protected function _prepareLayout()
	{
		return parent::_prepareLayout();
	}
	
	public function getLangDef()
	{
		$cur_locale = Mage::app()->getLocale()->getDefaultLocale();
		return Mage::helper('forum/topic')->getAvLovale($cur_locale);
	}
}

