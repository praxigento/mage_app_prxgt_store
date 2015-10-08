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

class Simple_Forum_Helper_Notify extends Mage_Core_Helper_Abstract
{

	const PAGE_VAR_NAME             = 'p';
	const SORT_VAR_NAME             = 'sort';

	public function notifyModerNewPost($data, $url, $user)
	{
		$obj = new Varien_Object();
		if(!empty($data['Post']))
		{
			$data['post_orig'] = $data['Post'];
		}
		elseif(!empty($data['post_id']))
		{
			$data['post_orig'] = $this->loadPost($data['post_id']);
		}
		$data['url']        = Mage::helper('core/url')->_getUrl('forum/topic/viewreply/id/' . $data['post_id'], array( '_current'=>false, '_escape'=>false, '_use_rewrite'=>false ));
		$data['forum_name'] = $this->getObjectName($data['parent_id']);
		$data['topic_name'] = $this->getObjectName($data['id']);
		$data['now']        = date('Y-m-d H:i:s');
		$data['posted_by']  = $user;
		$name = $this->__('Forum Moderator Notification');
		$obj->setData($data);
		$title = $this->__('New Post on Forum') . ' ' . Mage::helper('core/url')->getHomeUrl();
		$this->sendEmail($obj, Mage::getStoreConfig('forum/forumnotification/email_template_moderator'), Mage::getStoreConfig('forum/forumnotification/senderemail'), Mage::getStoreConfig('forum/forumnotification/notifyemail'), $name, $title);
	}

	public function notifyModerNewTopic($data, $url, $user)
	{
		$obj = new Varien_Object();
		if(!empty($data['Post']))
		{
			$data['post_orig'] = $data['Post'];
		}
		elseif(!empty($data['post_id']))
		{
			$data['post_orig'] = $this->loadPost($data['post_id']);
		}
		$data['url']        = Mage::helper('core/url')->_getUrl($url, array( '_current'=>false, '_escape'=>false, '_use_rewrite'=>false, '_query'=>array(self::PAGE_VAR_NAME => 1, self::SORT_VAR_NAME => 1)));
		$data['forum_name'] = $this->getObjectName($data['parent_id']);
		$data['topic_name'] = $this->getObjectName($data['id']);
		$data['now']        = date('Y-m-d H:i:s');
		$data['posted_by']  = $user;
		$obj->setData($data);
		$title = $this->__('New Topic on Forum') . ' ' . Mage::helper('core/url')->getHomeUrl();
		$name = $this->__('Forum Moderator Notification');
		$this->sendEmail($obj, Mage::getStoreConfig('forum/forumnotification/email_template_moderator'), Mage::getStoreConfig('forum/forumnotification/senderemail'), Mage::getStoreConfig('forum/forumnotification/notifyemail'), $name, $title);
	}

	public function notifyCustomersPost( $data )
	{
		$topic_id = $data['id'];
		$c        = $this->getByTopic($topic_id);

		if($c)
		{
			try
			{
				if(!empty($data['Post']))
				{
					$data['post_orig'] = $data['Post'];
				}
				elseif(!empty($data['post_id']))
				{
					$data['post_orig'] = $this->loadPost($data['post_id']);
				}
				$user = $this->loadUser($data['post_id']);
				$data['url']        = Mage::helper('core/url')->_getUrl('forum/topic/viewreply/id/' . $data['post_id'], array( '_current'=>false, '_escape'=>false, '_use_rewrite'=>false));
				$data['forum_name'] = $this->getObjectName($data['parent_id']);
				$data['topic_name'] = $this->getObjectName($data['id']);
				$data['now']        = date('Y-m-d H:i:s');
				$data['posted_by']  = $user;
				$title = $this->__('Subscription Notification') . ' ' . Mage::helper('core/url')->getHomeUrl();
				$name = $this->__('Forum User Notification');
				foreach($c as $val)
				{
					if($val->getSystem_user_id() == $data['system_user_id'])
					{
						continue;
					}
					$email_to                 = $val->getSystem_user_email();
					$data['unsubscribe_link'] = $this->getUnsubscribeLink($val);
					$obj = new Varien_Object();
					$obj->setData($data);
					$this->sendEmail($obj, Mage::getStoreConfig('forum/forumnotification/email_template_customer'),Mage::getStoreConfig('forum/forumnotification/senderemail') , $email_to, $name, $title);
				}
			}
			catch(Exception $e)
			{
			}
		}
	}

	public function addToNotifyList($topic_id)
	{
		$customer = Mage::registry('current_customer');
		$email    = $customer->getEmail();
		$id       = $customer->getId();
		$hash     = md5('notify_' . $email);
		if(!$this->checkIsNotified($topic_id, $id))
		{
			$m = Mage::getModel('forum/notify');
			$m->setId(NULL)
			->setHash($hash)
			->setSystem_user_id($id)
			->setSystem_user_email($email)
			->setTopic_id($topic_id);

			$m->save();
		}
	}

	public function removeFromNotifyList($topic_id)
	{        $customer = Mage::registry('current_customer');
		$email    = $customer->getEmail();
		$id       = $customer->getId();
		$hash     = md5('notify_' . $email);
		if($this->checkIsNotified($topic_id, $id))
		{
			$c = Mage::getModel('forum/notify')->getCollection();
            $c->getSelect()->where('system_user_id=?', $id)->where('topic_id=?', $topic_id);
			if($c->getSize())
			{
				foreach($c as $obj)
				{                	$del = Mage::getModel('forum/notify')->load($obj->getId());
                	$del->delete();
				}
				return true;
			}
		}
	}

	public function getIsNotified($topic_id, $customer_id)
	{        return $this->checkIsNotified($topic_id, $customer_id);
	}

	public function deleteByHash($topic_id, $hash)
	{
		$c = Mage::getModel('forum/notify')->getCollection();
		$c->getSelect()->where('hash=?', $hash)->where('topic_id=?', $topic_id);
		if($c->getSize())
		{
			foreach($c as $val)
			{
				$c = Mage::getModel('forum/notify')->load($val->getId());
				$c->delete();
			}
			return $val;
		}
		return false;
	}

	public function deleteByTopicId($topic_id)
	{
		$c = Mage::getModel('forum/notify')->getCollection();
		$c->getSelect()->where('topic_id=?', $topic_id);
		if($c->getSize())
		{
			foreach($c as $val)
			{
				$c = Mage::getModel('forum/notify')->load($val->getId());
				$c->delete();
			}
		}
	}

	private function loadUser($id)
	{
		$m = Mage::getModel('forum/post')->load($id);
		if($m->getId())
		{
			return ($m->getUser_nick() && $m->getUser_nick() != '' ? $m->getUser_nick() : $m->getUser_name());
		}
	}

	private function loadPost($id)
	{
		$m = Mage::getModel('forum/post')->load($id);
		if($m->getId())
		{
			return $m->getPost_orig();
		}
	}

	private function getUnsubscribeLink($obj)
	{
		return Mage::helper('core/url')->_getUrl('forum/topic/unsubscribe/topic_id/' . $obj->getTopic_id() . '/h/' . $obj->getHash());
	}

	private function getByTopic($topic_id)
	{
		$c = Mage::getModel('forum/notify')->getCollection();
		$c->getSelect()->where('topic_id=?', $topic_id);
		if($c->getSize())
		{
			return $c;
		}
	}

	private function checkIsNotified($topic_id, $customer_id)
	{
		$c = Mage::getModel('forum/notify')->getCollection();
		$c->getSelect()->where('system_user_id=?', $customer_id)->where('topic_id=?', $topic_id);
		if($c->getSize())
		{
			return true;
		}
	}

	private function getObjectName($id)
	{
		$data = Mage::getModel('forum/topic')->load($id);
		if($data->getId())
		{
			return $data->getTitle();
		}
	}

	private function sendEmail($postObject, $template_id, $sender, $email_to, $name = '', $title = '')
	{
		$mailTemplate = Mage::getModel('core/email_template');
		if($title != '')
		{
			$mailTemplate->setTemplateSubject($title);
		}
		$translate    = Mage::getSingleton('core/translate');
		$postObject   = $this->preparePostObject($postObject);
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

	private function preparePostObject($obj = array())
	{
		if(!empty($obj['post_orig']))
		{
			$obj['post_orig'] = str_replace('src="/', 'src="' . Mage::app()->getStore()->getBaseUrl() . "", $obj['post_orig']);
		}
		return $obj;
	}
}