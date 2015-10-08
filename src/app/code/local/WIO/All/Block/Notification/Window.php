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
 
class Wio_All_Block_Notification_Window extends Mage_Adminhtml_Block_Notification_Window
{
	
	protected function _construct()
	{
		parent::_construct();
		if(!Mage::getStoreConfig('wioall/install/run'))
		{
			$c = Mage::getModel('core/config_data');
			$c
				->setScope('default')
				->setPath('wioall/install/run')
				->setValue(time())
				->save();
			$this->setHeaderText($this->__("WebIdeaoOline.com Notifications Setup"));	
			$this->setIsFirstRun(1);
			$this->setIsHtml(1);
		
		}
    }
	
	protected function _toHtml()
	{
		 if($this->getIsHtml())
		 {
		 	$this->setTemplate('wio_all/popup.phtml');
		 }
		 return parent::_toHtml();
	}
	public function presetFirstSetup()
	{
		
	}
	public function getNoticeMessageText()
	{
		if($this->getIsFirstRun())
		{
			$child = $this->getLayout()->createBlock('core/template')->setTemplate('wio_all/popup_content.phtml')->toHtml();
			return $child;
		}
		else
		{
			return $this->getData('notice_message_text');
		}
	}
	
	
}
