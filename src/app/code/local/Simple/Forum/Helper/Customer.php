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

class Simple_Forum_Helper_Customer extends Mage_Core_Helper_Abstract
{
	const CUSTOMER_ID   = 'cid';
 	const ADM_USER_ID   = 10000000;
 	const ADM_USER_NICK = 'admin';

	public function getCustomerUrl($customer = false)
	{    	if(is_object($customer))
    	{        	$id = $customer->getId();
    	}
    	else
    	{            $id = $customer?$customer:self::ADM_USER_ID;
    	}

    	if($id)
    	{
    		if($id == self::ADM_USER_ID)
    		{            	$id = self::ADM_USER_NICK;
    		}        	return $this->_getUrl('forum/customer', array(self::CUSTOMER_ID=>$id));
    	}
	}

	public function getCustomerLink($id, $nick, $class=false)
	{    	return '<a href="' . $this->getCustomerUrl($id) . '" ' . ( $class ? 'class="' . $class . '"' : '' ) . '>' . $nick . '</a>';
	}

	public function getCustomerPMUrl($id)
	{    	return $this->_getUrl('forum/myprivatemessages/add', array(self::CUSTOMER_ID=>$id));
	}

	public function getEmail($system_user_id)
	{        $customer = Mage::getModel('customer/customer')->load($system_user_id);
        return $customer->getEmail();
	}

	public function getCustomerNick($system_user_id)
	{    	$system_user_id = $system_user_id ? $system_user_id : self::ADM_USER_ID;
     	$usersettings = Mage::getModel('forum/usersettings')->load( $system_user_id, 'system_user_id');

        if($usersettings->getId())
        {        	return $usersettings->getNickname();
        }
        $customer = Mage::getModel('customer/customer')->load($system_user_id);
        return $customer->getFirstname() . ' ' . $customer->getLastname();
	}

	public function notifyCustomerPrivateMessage( $nick, $email_to, $subject )
	{    	$obj = new Varien_Object();

		$data['url']        = $this->_getUrl('forum/myprivatemessages/index');
		$data['nickname']   = $nick;
		$data['subject']   = $subject;
		$name = $this->__('User Private Message Notification');
		$obj->setData($data);
		$title = $this->__('Someone has sent you a Private Message');
		$this->sendEmail($obj, Mage::getStoreConfig('forum/forumnotification/newprivatemessage'), Mage::getStoreConfig('forum/forumnotification/senderemail'), $email_to, $name, $title);
	}

	private function sendEmail($postObject, $template_id, $sender, $email_to, $name = '', $title = '')
	{
		$mailTemplate = Mage::getModel('core/email_template');
		if($title != '')
		{
			$mailTemplate->setTemplateSubject($title);
		}
		$translate    = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);
		try {
			$mailTemplate->setDesignConfig(array('area' => 'frontend', 'type' => 'html'))
					->sendTransactional(
					$template_id,
					$sender,
					$email_to,
					$name,
					array('data' => $postObject)
			);
			$translate->setTranslateInline(true);
		} catch (Exception $e) {
			$translate->setTranslateInline(true);
		}

	}
}