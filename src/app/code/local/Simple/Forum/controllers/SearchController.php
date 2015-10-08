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

class Simple_Forum_SearchController extends Mage_Core_Controller_Front_Action
{
	
	CONST SEARCH_VAR       = 'q_f';
	CONST RESET_VAR        = 'r';
	CONST REDIRECT_URL     = 'r_url';
	CONST REDIRECT_DEFAULT = 'forum/';
	
	
	private $search;
	private $posts_id_not;
	private $reset;
	
	public function _init()
	{
		$sess           =  Mage::getSingleton('customer/session');
		$this->customer = $sess->getCustomer(); 
		Mage::register('current_customer', $this->customer);
	}
	
	public function indexAction()
	{
		$this->_init();
		$this->search = trim($this->getRequest()->getParam(self::SEARCH_VAR)) && trim($this->getRequest()->getParam(self::SEARCH_VAR)) != '' ?strip_tags( trim($this->getRequest()->getParam(self::SEARCH_VAR))) : strip_tags(Mage::getSingleton('forum/session')->getSearchValue());
		$this->reset  = $this->getRequest()->getParam(self::RESET_VAR);
		if($this->reset || $this->search == '')
			return $this->redirectSearch();
		
		Mage::register('search_value_page', $this->search);
		Mage::getSingleton('forum/session')->setSearchValue($this->search);
		$this->_loadLayout();
	}
	
	private function _loadLayout()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('catalog/session');
		Mage::helper('forum/topic')->_isActive = true;
		$this->renderLayout();
	}
	
	private function redirectSearch()
	{
		$redirect_url = $this->getRequest()->getParam(self::REDIRECT_URL);
		$this->eraseSearchValue();
		if($redirect_url)
		{
			$this->_redirect( $redirect_url);
		}
		else
		{
			$this->_redirect(self::REDIRECT_DEFAULT);
		}
	}
	
	private function eraseSearchValue()
	{
		Mage::getSingleton('forum/session')->setSearchValue(NULL);
	}
}

?>