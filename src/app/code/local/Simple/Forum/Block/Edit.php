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

class Simple_Forum_Block_Edit extends Mage_Core_Block_Template
{
	protected $_model;
	protected $_customer;

	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $root = $this->getLayout()->getBlock('root');
		$root->setTemplate(Mage::helper('forum/data')->getLayout());
        $this->setModel(Mage::registry('current_object'));
        $this->setParentModel(Mage::registry('current_object_parent'));
        $this->setCustomer(Mage::registry('current_customer'));
    }

    protected function getHeadHtml()
    {
		return $this->getChildHtml('head');
	}

	protected function getHeadProductHtml()
	{
		return $this->getChildHtml('headproduct');
	}

	protected function getTitle()
	{
		if($this->getModel()->getId())
		{
			if(Mage::registry('current_object')->getSystem_user_id() == Mage::registry('current_customer')->getId() && Mage::registry('current_object_post')->getSystem_user_id())
			{
				return Mage::helper('forum/topic')->__('Edit Topic "%s" and Post', $this->getModel()->getTitle())
				. $this->getProductTitle();
			}
			elseif(Mage::registry('current_object_post')->getSystem_user_id() == Mage::registry('current_customer')->getId())
			{
				return Mage::helper('forum/topic')->__('Edit Post in Topic "%s"', $this->getModel()->getTitle())
				. $this->getProductTitle();
			}
			else
			{
				return Mage::helper('forum/topic')->__('Add Post to Topic "%s"', $this->getModel()->getTitle())
				. $this->getProductTitle();
			}
		}
		else
		{
			return Mage::helper('forum/topic')->__('Add New Topic')
			. $this->getProductTitle();
		}
	}

	protected function getSaveUrl()
	{
		if(!$this->getModel()->getId())
			return $this->getUrl('*/*/save');
		else
			return $this->getUrl('*/*/save/id/' . $this->getModel()->getId());
	}

	protected function getRedirectUrl()
	{
		return Mage::registry('redirect');
	}

	protected function titleReadOnly()
	{
		$isModerator = Mage::helper('forum/topic')->isModerator();
		if($this->getModel()->getId() && $this->getModel()->getSystem_user_id() != $this->getCustomerId() && !$isModerator)
			return ' readonly=1 ';
	}

	protected function getNotAllowIcons()
	{       $isModerator = Mage::helper('forum/topic')->isModerator();
		if($this->getModel()->getId() && $this->getModel()->getSystem_user_id() != $this->getCustomerId() && !$isModerator)
			return true;
	}

	protected function getCustomerId()
	{
		return Mage::registry('current_customer')->getId();
	}

	protected function getSubmitTitle()
	{
		if($this->getModel()->getId())
			return  Mage::helper('forum/topic')->__('Save Post') ;
		else
			return  Mage::helper('forum/topic')->__('Save Topic') ;
	}

	public function getBackUrl()
    {
    	if(Mage::registry('redirect') && Mage::registry('redirect') != false)
    	{
			return $this->getUrl('' . Mage::registry('redirect'));
		}
        $sess = Mage::getSingleton('core/session')->getData();
        if (!empty($sess["visitor_data"]['http_referer'])) {
            return $sess["visitor_data"]["http_referer"];
        }
		return $this->getUrl('forum/topic');

    }

    public function getBreadCrumbs()
	{
		if(Mage::getStoreConfig('forum/forumconfiguration/allowbreadcrumbs'))
		{
			return $this->getChildHtml('util_breadcrumbs');
		}
	}

    public function getModelPost()
    {
		return Mage::registry('current_object_post');
	}

	public function getBackFormUrl()
	{
		$action            = $this->getRequest()->getActionName();
		$parentObjectId    = (int) $this->getRequest()->getParam('parent_id', false);
		$postObjectId      = (int) $this->getRequest()->getParam('post_id', false);
		$topicObjectId     = (int) $this->getRequest()->getParam('id', false);
		$ret               = $this->getRequest()->getParam('ret', false);
		$ret = 'forum/topic/' . ($action ? $action . '/' : '') . ($parentObjectId ? 'parent_id/' . $parentObjectId  . '/' : '') . ($postObjectId ? 'post_id/' . $postObjectId . '/' : '') . ($topicObjectId ? 'id/' . $topicObjectId . '/' : '') .($ret ? 'ret/' . $ret  . '/': '');
		return $ret;
	}

	public function getRecaptchField()
	{
		if(Mage::getStoreConfig('forum/recaptchasetting/allowrecaptcha') && !Mage::helper('forum/topic')->isModerator())
		{
			return $this->getChildHtml('recaptcha');
		}
	}

	public function getModelTitle()
	{
		$data = Mage::getSingleton('forum/session')->getPostForumData();
		if(!empty($data['Title']))
		{
			return $data['Title'];
		}
		return $this->htmlEscape($this->getModel()->getTitle());
	}

	public function getModelDescription()
	{
		$data = Mage::getSingleton('forum/session')->getPostForumData();
		if(!empty($data['description']))
		{
			return $data['description'];
		}
		return $this->htmlEscape($this->getModel()->getDescription());
	}

	public function getModelContent()
	{
		$data = Mage::getSingleton('forum/session')->getPostForumData();
		if(!empty($data['Post']))
		{
			return $data['Post'];
		}
		return $this->htmlEscape($this->getModelPost()->getPost());
	}

	public function getQuoteId()
	{    	return Mage::registry('quote');
	}

	public function getQuoteText()
	{
		$quoteText = ''; 		if(Mage::registry('quote'))
		{
        	$post = Mage::getModel('forum/post')->load(intval(Mage::registry('quote')));
        	if($post->getId())
        	{
            	$quoteText .= '<blockquote class="simple-forum">' . $post->getPost() . '</blockquote><br>';
        	}
		}
		return $quoteText;
	}

	public function getModelNickName()
	{
		$data = Mage::getSingleton('forum/session')->getPostForumData();
		if(!empty($data['NickName']))
		{
			return $data['NickName'];
		}
		return $this->htmlEscape($this->getModelPost()->getUser_nick());
	}

	public function getModelParentId()
	{
		$data = Mage::getSingleton('forum/session')->getPostForumData();
		if(!empty($data['parent_id']))
		{
			return $data['parent_id'];
		}
		return $this->htmlEscape($this->getModelPost()->getParent_id());
	}

	public function getIcon_id()
	{     	$data = Mage::getSingleton('forum/session')->getPostForumData();
		if(!empty($data['icon_id']))
		{
			return $data['icon_id'];
		}
		return $this->htmlEscape($this->getModel()->getIcon_id());
	}

	public function getNotifyMe($id = false)
	{
		$data = Mage::getSingleton('forum/session')->getPostForumData();
		if(!empty($data['notify_me']))
		{
			return $data['notify_me'];
		}
		if(!$data)
		{
			if($id && Mage::registry('current_customer'))
			{	        	$notify_me = Mage::helper('forum/notify')->getIsNotified($id, Mage::registry('current_customer')->getId());
	        	return $notify_me;
			}
		}
	}

	public function getUseNick()
	{
		$data = Mage::getSingleton('forum/session')->getPostForumData();
		if(!empty($data['use_nick']))
		{
			return $data['use_nick'];
		}
		if($this->getModelPost()->getUser_nick() != '' && $this->getModelPost()->getUser_nick())
		{
			return true;
		}
	}

	public function getAllowNotification()
	{
		return Mage::getStoreConfig('forum/forumnotification/allownotifycheckbox');
	}

	public function getParentId()
	{
		if(Mage::registry('current_object')->getId())return Mage::registry('current_object')->getParent_id();
		else return $this->getRequest()->getParam('parent_id');
	}

	public function getAllowedNick()
	{
		return Mage::getStoreConfig('forum/forumconfiguration/allowuseingnicknames');
	}

	public function isOnProduct()
	{
		if(Mage::registry('current_product'))
		{
			return true;
		}
	}

	public function getProductId()
	{
		if(Mage::registry('current_product'))
		{
			return Mage::registry('current_product')->getId();
		}
	}

	public function getForums()
	{
		$store  = Mage::app()->getStore()->getId();
		$forums = Mage::helper('forum/topic')->getOptionsTopics(false, 0, array('1' => 'is_category=?'), Mage::helper('forum/topic')->__('No Parent Forum'), false, false, true, $store, true);
		return $forums;
	}

	private function getProductTitle()
	{
		if(Mage::registry('current_product'))
		{
			return	' ' . Mage::helper('forum/topic')->__('Product') .  ': "' .  Mage::registry('current_product')->getName() . '"';
		}
	}
}

?>