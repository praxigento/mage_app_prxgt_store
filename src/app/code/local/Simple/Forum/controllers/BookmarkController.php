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

class Simple_Forum_BookmarkController extends Mage_Core_Controller_Front_Action
{
	private $customer = false;
	
	public function _init()
	{
		$postData = $this->getRequest()->getPost();
		if(empty($postData['___action']))
		{
			$this->_forward('index','topic');
			return false;
		}
		
		if($postData['___action'] != 'view')
		{
			$this->_forward('index','topic');
			return false;
		}
		if(!empty($postData['redirect']))
		{
			Mage::register('redirect', $postData['redirect']);
		}
		$sess           =  Mage::getSingleton('customer/session');
		$this->customer = $sess->getCustomer();
		if(!Mage::registry('current_customer'))Mage::register('current_customer', $this->customer);

		
		return true;
	}
	
	public function indexAction()
	{
		$init = $this->_init();
		if(!$init)
		{
			return;
		}
        $this->_loadLayout();
	}		
	
	public function _loadLayout()
	{
		$this->loadLayout();
		Mage::helper('forum/topic')->_isActive = true;
		$this->renderLayout();
	}
}