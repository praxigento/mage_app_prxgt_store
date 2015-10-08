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

class Simple_Forum_Block_Editor extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    
    public function getLangLocaleShort()
	{
		$cur_locale = Mage::app()->getLocale()->getDefaultLocale();
		return Mage::helper('forum/topic')->getAvLovale($cur_locale);
	}
}

?>