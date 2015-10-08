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

class Simple_Forum_CustomerController extends Mage_Core_Controller_Front_Action
{

	const CUSTOMER_ID   = 'cid';
	const ADM_USER_NICK = 'admin';
    const ADM_USER_ID   = 10000000;

	public function indexAction()
	{
		$customer_id    = $this->getRequest()->getParam(self::CUSTOMER_ID);

		if(!$customer_id)
		{
            $this->_redirect('forum');
		}

		if($customer_id == self::ADM_USER_NICK)
		{        	$customer_id = self::ADM_USER_ID;
		}

    	$customer_forum = Mage::getModel('forum/usersettings')->load( $customer_id, 'system_user_id');

    	if($customer_id != self::ADM_USER_ID)
    	{
			$customer       = Mage::getModel('customer/customer')->load($customer_id);
			if(!$customer->getId())
			{            	$this->_redirect('forum');
			}
			else
			{
				Mage::register('customer', $customer);
			}
		}

    	Mage::register('customer_id', $customer_id);
    	Mage::register('customer_forum', $customer_forum);

        $this->_loadLayout();
	}

	public function _loadLayout()
	{
		$this->loadLayout();

  		$sess           =  Mage::getSingleton('customer/session');
		$this->customer = $sess->getCustomer();
		if(!Mage::registry('current_customer'))Mage::register('current_customer', $this->customer);

  		$this->_initLayoutMessages('forum/session');
		$this->renderLayout();
	}
}