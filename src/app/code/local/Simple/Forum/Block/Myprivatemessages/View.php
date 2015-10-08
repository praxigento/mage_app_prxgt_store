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

class Simple_Forum_Block_Myprivatemessages_View extends Mage_Core_Block_Template
{

     	protected function _prepareLayout()
		{
			$this->customer = Mage::registry('current_customer');
   			$this->session  = Mage::getSingleton('forum/session');
		    parent::_prepareLayout();
		}

		public function getBackUrl()
		{         	if ($this->getRefererUrl()) {
	            return $this->getRefererUrl();
	        }
	        return $this->getUrl('forum/myprivatemessages/');
		}

		public function getIsActiveInbox()
		{
			$action = Mage::app()->getRequest()->getActionName();
        	if( $action === self::INDEX_ACTION)
        	{            	return self::CLASS_LINK_ACTIVE;
        	}
		}

    	public function getForumUrl()
		{
			return $this->getUrl('forum');
		}

		public function getBackLinkUrl()
		{         	if(in_array(Mage::registry('r'), Mage::registry('r_arr')))
         	{
            	if(Mage::registry('r') == 'index')
            	{
                	return '<a href="' . $this->getIncomingLink() . '">' . $this->__('Back to Inbox') . '</a>';
            	}
            	if(Mage::registry('r') == 'sent')
            	{
                	return '<a href="' . $this->getSentLink() . '">' . $this->__('Back to Sent') . '</a>';
            	}
            	if(Mage::registry('r') == 'trash')
            	{
                    return '<a href="' . $this->getTrashLink() . '">' . $this->__('Back to Trash') . '</a>';
            	}
         	}
         	else
         	{            	return '<a href="' . $this->getIncomingLink() . '">' . $this->__('Back to Inbox') . '</a>';
         	}
		}

		public function getIncomingLink()
		{
        	return $this->getUrl('forum/myprivatemessages/index');
		}

		public function getSentLink()
  		{
            return $this->getUrl('forum/myprivatemessages/sent');
  		}

  		public function getTrashLink()
  		{
            return $this->getUrl('forum/myprivatemessages/trash');
  		}

		public function getAvatarUrl()
		{
            if(Mage::registry('message')->getSent_from() == Mage::registry('current_customer')->getId())
            {            	$id = Mage::registry('message')->getSent_to();
            }
            if(Mage::registry('message')->getSent_to() == Mage::registry('current_customer')->getId())
            {
                $id = Mage::registry('message')->getSent_from();
            }
            $obj    = Mage::getModel('forum/usersettings')->load($id, 'system_user_id');
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

		public function getMessageTitle()
		{        	return Mage::registry('message')->getSubject();
		}

		public function getTypeAvatar()
		{        	if(Mage::registry('message')->getSent_from() == Mage::registry('current_customer')->getId())
        	{            	return $this->__('Recipient');
        	}
        	if(Mage::registry('message')->getSent_to() == Mage::registry('current_customer')->getId())
        	{
            	return $this->__('Sender');
        	}
		}

		public function getSentFrom($message = false)
		{
			if($message)
			{            	$object = $message;
			}
			else
			{                $object = Mage::registry('message');
			}        	if($object->getSent_from() == Mage::registry('current_customer')->getId())
        	{            	return $this->__('me') . ' (' . Mage::registry('current_customer')->getFirstname() . ' ' . Mage::registry('current_customer')->getLastname() .')' ;
        	}
        	else
        	{            	$id = $object->getSent_from();
            	$c  = Mage::getModel('forum/usersettings')->load($id, 'system_user_id');
            	if($c->getId())
            	{                	return Mage::helper('forum/customer')->getCustomerLink($id, $c->getNickname());
            	}
             	else
             	{                	$c = Mage::getModel('customer/customer')->load($id);
					return Mage::helper('forum/customer')->getCustomerLink($id, $c->getFirstname() . ' ' . $c->getLastname());
             	}
        	}
		}

		public function getSentTo($message = false)
		{
			if($message)
			{
            	$object = $message;
			}
			else
			{
                $object = Mage::registry('message');
			}  			if($object->getSent_to() == Mage::registry('current_customer')->getId())
        	{
            	return $this->__('me') . ' (' . Mage::registry('current_customer')->getFirstname() . ' ' . Mage::registry('current_customer')->getLastname() .')' ;
        	}
        	else
        	{
            	$id = $object->getSent_to();
            	$c  = Mage::getModel('forum/usersettings')->load($id, 'system_user_id');
            	if($c->getId())
            	{
                	return Mage::helper('forum/customer')->getCustomerLink($id, $c->getNickname());
            	}
             	else
             	{
                	$c = Mage::getModel('customer/customer')->load($id);
					return Mage::helper('forum/customer')->getCustomerLink($id, $c->getFirstname() . ' ' . $c->getLastname());
             	}
        	}
		}

		public function getMessageId()
		{        	return Mage::registry('message')->getId();
		}

		public function getMessageText($message = false)
		{
			if($message)
			{
            	$object = $message;
			}
			else
			{
                $object = Mage::registry('message');
			}        	return $object->getMessage();
		}

		public function getSentDate($message = false)
		{
            if($message)
			{
            	$object = $message;
			}
			else
			{
                $object = Mage::registry('message');
			}        	return $this->getFormatedDate($object->getDate_sent());
		}

		public function getAvatarWidth()
		{
	       	return Mage::getStoreConfig('forum/advanced_settings/avatar_width');
		}

		public function getSubmitTitle()
		{        	return $this->__('Send');
		}

		public function getMoveTrashTitle()
		{        	return $this->__('Move to Trash');
		}

		public function getUndotrashTitle()
		{        	return $this->__('Move to Inbox');
		}

		public function getDeleteTitle()
		{        	return $this->__('Delete');
		}

		public function getSendPMAction()
		{        	return $this->getUrl('forum/myprivatemessages/save');
		}

		public function getCId()
		{        	if(Mage::registry('message')->getSent_to() == Mage::registry('current_customer')->getId())
        	{            	return Mage::registry('message')->getSent_from();
        	}
        	if(Mage::registry('message')->getSent_from() == Mage::registry('current_customer')->getId())
        	{            	return Mage::registry('message')->getSent_to();
        	}
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
	    {
	    	if($redirect == '')
	    	{
            	$redirect = Mage::registry('r');
	    	}        	$id = $message->getId();
        	return $this->getUrl('forum/myprivatemessages/movetrash', array('mid'=>$id, 'r'=>$redirect));
	    }

	    public function getUndoTrahsUrl($message, $redirect = '')
	    {
	    	if($redirect == '')
	    	{
            	$redirect = Mage::registry('r');
	    	}        	$id = $message->getId();
        	return $this->getUrl('forum/myprivatemessages/undotrash', array('mid'=>$id, 'r'=>$redirect));
	    }

	    public function getDeleteUrl($message, $redirect = '')
	    {
	    	if($redirect == '')
	    	{            	$redirect = Mage::registry('r');
	    	}        	$id = $message->getId();
        	return $this->getUrl('forum/myprivatemessages/delete', array('mid'=>$id, 'r'=>$redirect));
	    }

	    public function getViewUrl($message, $redirect = '')
	    {            $id = $message->getId();
        	return $this->getUrl('forum/myprivatemessages/view', array('mid'=>$id, 'r'=>$redirect));
	    }

		public function getIsRead($message)
		{        	if($message->getSent_to() == Mage::registry('current_customer')->getId() && $message->getIs_read() == 0 )
        	{            	return false;
        	}
        	return true;
		}

		public function haveReplyBox()
		{        	//if(!Mage::registry('message')->getIs_trash())
        	{                return true;
        	}
		}

		public function getRyplyFormAction()
		{        	return $this->getUrl('forum/myprivatemessages/save');
		}

		public function getParentId()
		{        	return Mage::registry('message')->getId();
		}

		public function getIsTrash()
		{        	return Mage::registry('message')->getIs_trash();
		}

		public function getIsInbox()
		{
			if(Mage::registry('message')->getSent_to() == Mage::registry('current_customer')->getId() && Mage::registry('message')->getIs_trash() == 0)
			{        		return true;
        	}
		}

		public function getSubject()
		{
			if($data = Mage::getSingleton('forum/session')->getPostPMData())
        	{
            	if(!empty($data['subject']))
            	{
                	return $data['subject'];
            	}
        	}        	return 'RE: ' . Mage::registry('message')->getSubject();
		}

		public function getMessageTextarea()
		{         	if($data = Mage::getSingleton('forum/session')->getPostPMData())
        	{
            	if(!empty($data['privatemessage']))
            	{
                	return $data['privatemessage'];
            	}
        	}
		}

		public function getRedirect()
		{        	return Mage::registry('r');
		}

		public function getParents()
		{        	return Mage::registry('parents');
		}

  		public function getRecaptchField()
		{
			if(Mage::getStoreConfig('forum/recaptchasetting/allowrecaptchapm'))
			{
				return $this->getChildHtml('recaptcha');
			}
		}
}