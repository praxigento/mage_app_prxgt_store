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

class Simple_Forum_MypostsController extends Mage_Core_Controller_Front_Action
{
	
	private $customer = false;
	
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }
	
	public function indexAction()
	{
		Mage::register('myforumposts', true);
		$this->_init();
		$this->_loadLayout();
	}
	
	public function _init()
	{
		$sess           =  Mage::getSingleton('customer/session');
		$this->customer = $sess->getCustomer(); 
		if(!Mage::registry('current_customer'))Mage::register('current_customer', $this->customer);
		return true;
	}
	
	public function _loadLayout()
	{
		$this->loadLayout();
		if ($block = $this->getLayout()->getBlock('forum_myposts')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Forum Posts'));
        $this->_initLayoutMessages('forum/session');
		$this->renderLayout();
	}
}

?>