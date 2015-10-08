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

class Simple_Forum_Block_Myprivatemessages extends Mage_Core_Block_Template
{

		public $_objectsCollection = false;
		public  $limits                 = array(5, 10, 15);

		private $customer  = false;
		private $session   = false;

		private $send_to_customer = false;

		const CLASS_LINK_ACTIVE = ' class="active" ';
		const NOT_READ_STYLE    = ' style="font-weight:bold;" ';

		const INDEX_ACTION = 'index';
		const SENT_ACTION  = 'sent';
		const TRASH_ACTION = 'trash';

		const LIMIT_VAR_NAME = 'limit';
		const PAGE_VAR_NAME  = 'p';
     	protected function _prepareLayout()
		{
			$this->limits   = Mage::helper('forum/topic')->getPagesLimits();
			$this->customer = Mage::registry('current_customer');
   			$this->session  = Mage::getSingleton('forum/session');
   			$this->loadCustomer();
		    parent::_prepareLayout();
		}

		public function getBackUrl()
		{         	if ($this->getRefererUrl()) {
	            return $this->getRefererUrl();
	        }
	        return $this->getUrl('forum/myprivatemessages/');
		}

		public function getIncomingLink()
		{        	return $this->getUrl('forum/myprivatemessages/index');
		}

		public function getIsActiveInbox()
		{
			$action = Mage::app()->getRequest()->getActionName();
        	if( $action === self::INDEX_ACTION)
        	{            	return self::CLASS_LINK_ACTIVE;
        	}
		}

		public function getTypeOfMessages()
		{  			$action = Mage::app()->getRequest()->getActionName();
        	if( $action === self::INDEX_ACTION)
        	{
            	return $this->__('Inbox');
        	}
        	if( $action === self::SENT_ACTION )
        	{
                return $this->__('Sent');
        	}
        	if( $action === self::TRASH_ACTION )
        	{
                return $this->__('Trash');
        	}
		}

		public function getInboxCount()
		{        	$collection = Mage::getModel('forum/privatemessages')->getCollection();
        	$collection->getSelect()->where('is_trash=0')->where('sent_to=?', Mage::registry('current_customer')->getId())->where('is_deleteinbox=?', 0)->where('is_primary=?', 1);

        	return $collection->getSize();
		}

		public function getSentCount()
		{            $collection = Mage::getModel('forum/privatemessages')->getCollection();
        	$collection->getSelect()->where('sent_from=?', Mage::registry('current_customer')->getId())->where('is_deletesent=?', 0)->where('is_primary=?', 1);

        	return $collection->getSize();
		}

		public function getTrashCount()
		{            $collection = Mage::getModel('forum/privatemessages')->getCollection();
        	$collection->getSelect()->where('is_trash=1')->where('sent_to=?', Mage::registry('current_customer')->getId())->where('is_deleteinbox=?', 0)->where('is_primary=?', 1);

        	return $collection->getSize();
		}

		public function countAllMessages($message, $all = 0)
		{        	$parent_id = $message->getParent_id();
        	$new_message = Mage::getModel('forum/privatemessages')->load($parent_id);
        	if($new_message->getId())
        	{
        		$all++;
            	if($new_message->getParent_id())
            	{                	return $this->countAllMessages($new_message, $all);
            	}
            	else
            	{                	if($all!=0)
                	{
                        $all++;						return '(' . $all  . ')';
                	}
            	}
        	}
        	if($all!=0)
        	{            	return '(' . $all++ . ')';
        	}
		}

  		public function getSentLink()
  		{            return $this->getUrl('forum/myprivatemessages/sent');
  		}

  		public function getIsActiveSent()
  		{            $action = Mage::app()->getRequest()->getActionName();
        	if( $action === self::SENT_ACTION)
        	{
            	return self::CLASS_LINK_ACTIVE;
        	}
  		}

  		public function getTrashLink()
  		{            return $this->getUrl('forum/myprivatemessages/trash');
  		}

    	public function getIsActiveTrash()
    	{            $action = Mage::app()->getRequest()->getActionName();
        	if( $action === self::TRASH_ACTION)
        	{
            	return self::CLASS_LINK_ACTIVE;
        	}
    	}

    	public function getForumUrl()
		{
			return $this->getUrl('forum');
		}

		public function getSendToCustomer()
		{        	if(!$this->send_to_customer)
        	{            	$this->loadSendToCustomer();
        	}
        	return $this->send_to_customer;
		}

		public function getAvatarUrl($obj)
		{
			$avatar = Mage::getStoreConfig('forum/advanced_settings/avatar_path');
			if($obj->getAvatar_name() && file_exists(Mage::getBaseDir() . '/' .  Mage::getStoreConfig('forum/advanced_settings/avatar_path') . '/' . $obj->getAvatar_name()))
	        {
	        	$img = $obj->getAvatar_name();
	        	if(Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') && Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') != '')
	        	{
	                 return Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') . $obj->getAvatar_name();
	        	}
	        }
	        else
	        {
	            if(Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') && Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') != '')
	        	{
	                 return Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') . Mage::getStoreConfig('forum/advanced_settings/avatar_noimage');
	        	}
	            $img = '/' . Mage::getStoreConfig('forum/advanced_settings/avatar_noimage');
	        }
	        return $this->getUrl($avatar) . $img;
		}

		public function getAvatarWidth()
		{
	       	return Mage::getStoreConfig('forum/advanced_settings/avatar_width');
		}

		public function getCustomerName()
		{
			if($this->send_to_customer->getUserSettingsData()->getNickname() && $this->send_to_customer->getUserSettingsData()->getNickname() != '')
	    	{
	        	return $this->send_to_customer->getUserSettingsData()->getNickname();
	    	}
	    	elseif($this->send_to_customer->getSystemUserData()->getFirstname() && $this->send_to_customer->getSystemUserData()->getLastname())
    		{
				return $this->send_to_customer->getSystemUserData()->getFirstname() . ' ' . $this->send_to_customer->getSystemUserData()->getLastname();
            }
	    	else
	    	{
	    		if($this->send_to_customer_id == self::ADM_USER_ID)
	    		{
	            	return $this->__('Forum Admin');
	    		}
	        	return $this->__('Name Not Set');
	    	}
		}

		public function getCustomerUrl()
		{        	return Mage::helper('forum/customer')->getCustomerUrl($this->send_to_customer_id);
		}

		public function getSubmitTitle()
		{        	return $this->__('Send');
		}

		public function getSendPMAction()
		{        	return $this->getUrl('forum/myprivatemessages/save');
		}

		public function getCId()
		{        	return $this->send_to_customer_id;
		}

		public function getCollection()
		{        	if(!$this->_objectsCollection)
        	{            	$this->initCollection();
        	}

        	return $this->_objectsCollection;
		}

		public function getSubjectSend()
		{        	if($data = Mage::getSingleton('forum/session')->getPostPMData())
        	{            	if(!empty($data['subject']))
            	{                	return $data['subject'];
            	}
        	}
		}

		public function getMessageSend()
		{
        	if($data = Mage::getSingleton('forum/session')->getPostPMData())
        	{
            	if(!empty($data['privatemessage']))
            	{
                	return $data['privatemessage'];
            	}
        	}
		}

  		public function getRecaptchField()
		{
			if(Mage::getStoreConfig('forum/recaptchasetting/allowrecaptchapm'))
			{
				return $this->getChildHtml('recaptcha');
			}
		}

		public function getToolbar()
		{        	return $this->getChildHtml('toolbar');
		}

		public function initCollection()
		{        	$collection = Mage::getModel('forum/privatemessages')->getCollection();
        	if(Mage::registry('myprivatemessages_inbox'))
        	{
            	$collection->getSelect()->where('is_trash=0')->where('sent_to=?', Mage::registry('current_customer')->getId())->where('is_deleteinbox=?', 0)->where('is_primary=?', 1);
        	}
        	elseif(Mage::registry('myprivatemessages_sent'))
        	{            	$collection->getSelect()->where('sent_from=?', Mage::registry('current_customer')->getId())->where('is_deletesent=?', 0)->where('is_primary=?', 1);
        	}
        	elseif(Mage::registry('myprivatemessages_trash'))
        	{                $collection->getSelect()->where('is_trash=1')->where('sent_to=?', Mage::registry('current_customer')->getId())->where('is_deleteinbox=?', 0)->where('is_primary=?', 1);
        	}
        	$collection->setPageSize($this->_getLimit())->setCurPage($this->_getCurPage())->setOrder('date_sent', 'desc');
        	$this->_objectsCollection = $collection;
        	return $this->_objectsCollection;
		}

        public function _getLimit()
	    {
	    	$limit = (int) $this->getRequest()->getParam($this->getLimitVarName(), false);
			if($limit && in_array($limit, $this->limits))
			{
				return $this->_setLimit($limit);
			}
			else
			{
					return Mage::getSingleton('forum/session')->getPageLimitPrivateMessages()
						? Mage::getSingleton('forum/session')->getPageLimitPrivateMessages()
							: $this->limits[0];
			}
		}

		public function getLimitVarName()
		{
			return self::LIMIT_VAR_NAME;
		}

		public function _getCurPage()
		{
			$page = (int) $this->getRequest()->getParam(self::PAGE_VAR_NAME, null);
			if($page == null)
			{
				return !is_null(Mage::getSingleton('forum/session')->getPageMessagesCurrent() )
					? Mage::getSingleton('forum/session')->getPageMessagesCurrent()
						: 1;
			}
			else
			{
				return $this->_setCurPage($page);
			}
		}

  		public function getPageVarName()
		{
			return self::PAGE_VAR_NAME;
		}

		public function _getSentLink($message)
		{
			if(Mage::registry('myprivatemessages_inbox') || Mage::registry('myprivatemessages_trash'))
			{            	$id = $message->getSent_from();
			}
			else
			{                $id = $message->getSent_to();
			}            return Mage::helper('forum/customer')->getCustomerNick($id);
		}

		public function getSentId($message)
		{            if(Mage::registry('myprivatemessages_inbox') || Mage::registry('myprivatemessages_trash'))
			{
            	$id = $message->getSent_from();
			}
			else
			{
                $id = $message->getSent_to();
			}
			return $id;
		}

		public function getFormatedDate($date)
	    {
	        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
	    }

	    public function getTrahsUrl($message, $redirect = '')
	    {        	$id = $message->getId();
        	return $this->getUrl('forum/myprivatemessages/movetrash', array('mid'=>$id, 'r'=>$redirect));
	    }

	    public function getUndoTrahsUrl($message, $redirect = '')
	    {        	$id = $message->getId();
        	return $this->getUrl('forum/myprivatemessages/undotrash', array('mid'=>$id, 'r'=>$redirect));
	    }

	    public function getDeleteUrl($message, $redirect = '')
	    {        	$id = $message->getId();
        	return $this->getUrl('forum/myprivatemessages/delete', array('mid'=>$id, 'r'=>$redirect));
	    }

	    public function getViewUrl($message, $redirect = '')
	    {            $id = $message->getId();
        	return $this->getUrl('forum/myprivatemessages/view', array('mid'=>$id, 'r'=>$redirect));
	    }

	    public function getMassActionMarkRead($redirect)
	    {        	return $this->getUrl('forum/myprivatemessages/massread', array( 'r'=>$redirect ));
	    }

	    public function getMassActionTrash($redirect)
	    {        	return $this->getUrl('forum/myprivatemessages/masstrash', array( 'r'=>$redirect ));
	    }

        public function getMassActionDelete($redirect)
        {        	return $this->getUrl('forum/myprivatemessages/massdelete', array( 'r'=>$redirect ));
        }

        public function getMassActionUndoTrash($redirect)
        {        	return $this->getUrl('forum/myprivatemessages/massundo', array( 'r'=>$redirect ));
        }

		public function getIsRead($message)
		{        	if($message->getSent_to() == Mage::registry('current_customer')->getId() && $message->getIs_read() == 0 )
        	{            	return false;
        	}
        	return true;
		}

		public function getStyleMessageLink($message)
		{        	if(!$this->getIsRead($message))
        	{            	return self::NOT_READ_STYLE;
        	}
        	return '';
		}

		private function _setCurPage($page = 1)
		{
			Mage::getSingleton('forum/session')->setPageMessagesCurrent($page);
			return $page;
		}

  		private function _setLimit($limit)
		{			Mage::getSingleton('forum/session')->setPageLimitPrivateMessages($limit);
			return $limit;
		}

		private function loadSendToCustomer()
		{        	$this->send_to_customer     = new Varien_Object();
        	$this->send_to_customer_id  = $this->_getSendToCustomerId();
        	$this->send_to_customer->setUserSettingsData($this->_getCustomerData());
            $this->send_to_customer->setSystemUserData($this->_getCustomerSystemData());
		}

		private function _getSendToCustomerId()
		{			return Mage::registry('send_to_customer_id');
		}

		private function _getCustomerData()
		{        	return Mage::registry('customer_forum');
		}

		private function _getCustomerSystemData()
		{            return Mage::registry('customer_system');
		}

		private function loadCustomer()
		{
			$this->customer = Mage::getModel('forum/usersettings')->load(Mage::registry('current_customer')->getId(), 'system_user_id');
		}

}