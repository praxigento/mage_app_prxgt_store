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

class Simple_Forum_Block_Myaccount extends Mage_Core_Block_Template
{
		private $customer = false;
		private $session  = false;
     	protected function _prepareLayout()
		{
			$this->customer = Mage::registry('current_customer');
   			$this->session  = Mage::getSingleton('forum/session');
   			$this->loadCustomer();
		    parent::_prepareLayout();
		}

		public function getBackUrl()
	    {
	        if ($this->getRefererUrl()) {
	            return $this->getRefererUrl();
	        }
	        return $this->getUrl('forum/myaccount/');
	    }

        public function getForumUrl()
		{
			return $this->getUrl('forum');
		}

		public function getActionForm()
		{            return $this->getUrl('forum/myaccount/save');
		}

		public function getAvatarUrl()
		{
			$avatar = Mage::getStoreConfig('forum/advanced_settings/avatar_path');
			if($this->customer->getAvatar_name() && file_exists(Mage::getBaseDir() . '/' .  Mage::getStoreConfig('forum/advanced_settings/avatar_path') . '/' . $this->customer->getAvatar_name()))
            {            	$img = $this->customer->getAvatar_name();
            	if(Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') && Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') != '')
            	{                     return Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') . $this->customer->getAvatar_name();
            	}
            }
            else
            {
                if(Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') && Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') != '')
            	{
                     return Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') . Mage::getStoreConfig('forum/advanced_settings/avatar_noimage');
            	}                $img = '/' . Mage::getStoreConfig('forum/advanced_settings/avatar_noimage');
            }
            return $this->getUrl($avatar) . $img;
		}

		public function getHaveAvatar()
		{			if($this->customer->getAvatar_name() && file_exists(Mage::getBaseDir() . '/' .  Mage::getStoreConfig('forum/advanced_settings/avatar_path') . '/' . $this->customer->getAvatar_name()))
            {
            	return true;
            }
		}

		public function getDeleteAvatarUrl()
		{            return $this->getUrl('forum/myaccount/delavatar');
		}

		public function getNickName()
		{            if($data = $this->session->getPostData())
            {            	return $data['nickname'];
            }
            else
            {            	return $this->customer->getNickname();
            }
		}

		public function getSignature()
		{  			if($data = $this->session->getPostData())
            {
            	return $data['signature'];
            }
            else
            {
            	return $this->customer->getSignature();
            }
		}

		public function getSubmitTitle()
		{        	return $this->__('Submit');
		}

		public function getAvatarWidth()
		{        	return Mage::getStoreConfig('forum/advanced_settings/avatar_width');
		}

		private function loadCustomer()
		{
			$this->customer = Mage::getModel('forum/usersettings')->load(Mage::registry('current_customer')->getId(), 'system_user_id');
		}
}