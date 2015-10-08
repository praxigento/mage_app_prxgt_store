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

class Simple_Forum_TopicController extends Mage_Core_Controller_Front_Action
{

	public $customer;
	public $blockTopic;

	private $init    = false;
	private $setMeta = false;

	const PAGE_VAR_NAME             = 'p';
	const SEARCH_VAR                = 'q_f';
	const SORT_VAR_NAME             = 'sort';

	private $current_product;


	public function preDispatch()
    {
    	parent::preDispatch();
    	$action = $this->getRequest()->getActionName();
    	if($action == 'new' || $action == 'edit')
    	{
	        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
	            $this->setFlag('', 'no-dispatch', true);
	        }
	    }
    }


	protected function _getSession()
    {
        return Mage::getSingleton('forum/session');
    }

	public function _init()
	{
		$this->blockTopic = Mage::getBlockSingleton('forum/topic');
		Mage::helper('forum/topic')->setObject($this->blockTopic);
		$sess           =  Mage::getSingleton('customer/session');
		$this->customer = $sess->getCustomer();
		if(!Mage::registry('current_customer'))Mage::register('current_customer', $this->customer);
	}

	public function indexAction()
	{
		$this->_init();
		Mage::getSingleton('forum/session')->setLastViewedObjectId(0);
        $this->_loadLayout();
	}

    public function viewAction()
    {
		$this->_init();
    	$search   = strip_tags(trim($this->getRequest()->getParam(self::SEARCH_VAR, false)));
    	if($search && $search != '')
    	{
			Mage::getSingleton('forum/session')->setSearchValue($search);
		}
    	$objectId = (int) $this->getRequest()->getParam('id', false);
    	if(!$objectId)
    	{
			return false;
		}
		$this->_initObject($objectId);
		Mage::getSingleton('forum/session')->setPostForumData(null);
	}

	public function viewreplyAction()
	{
		$id       = $this->getRequest()->getParam('id');
		$redirect = Mage::helper('forum/viewreply')->getViewReplyUrlObj($id);
		$this->_redirectUrl($redirect->getData('url') . $redirect->getData('identifier'));
	}

	public function newAction()
    {
	    $this->_forward('edit');
	}

	public function unsubscribeAction()
	{
		$hash      = $this->getRequest()->getParam('h');
		$topic_id  = $this->getRequest()->getParam('topic_id');
		$res       = Mage::helper('forum/notify')->deleteByHash($topic_id, $hash);
		if($res)
		{
			if($res->getId())
			{
				$o = Mage::getModel('forum/topic')->load($res->getTopic_id());
				$this->_getSession()->addSuccess(Mage::helper('forum/topic')->__('You unsubscribed from Topic %s', $o->getTitle()));
			}
		}
		$this->_redirect('*/');
	}

	public function saveAction()
	{
		$this->_init();
		$data      = $this->customer->getData();
		if(empty($data['entity_id']))
		{
			$this->_forward('index');
			return;
		}
		$system_user_id = $data['entity_id'];
		if ( $this->getRequest()->getPost() )
		{
			try
			{
				$moderatePost   = Mage::helper('forum/topic')->getModeratePosts();
				$moderateTopic  = Mage::helper('forum/topic')->getModerateTopics();

				$postData      = $this->getRequest()->getPost();

				$topicId       = !empty( $postData['topic_id'] )  ? $postData['topic_id']   : NULL;
				$postId        = !empty( $postData['post_id'] )   ? $postData['post_id']    : NULL;
				$redirect      = !empty( $postData['redirect'] )  ? urldecode($postData['redirect'])  : NULL;
				$userName       = $data['firstname'] .  ' ' . $data['lastname'];
				$topic = Mage::getModel('forum/topic')->load($topicId);
				$isModerator = Mage::helper('forum/topic')->isModerator();
				if(!$isModerator && $this->getUseRecaptcha())
				{
					$err = $this->validateRecaptcha();
					if($err)
					{
						$this->_getSession()->addError($err);
						$back = !empty( $postData['back'] )  ? urldecode($postData['back'])  : 'index';
						Mage::getSingleton('forum/session')->setPostForumData($postData);
						$this->_redirect($back, array('ret'=>$redirect));
						return;
					}
				}

				if(!empty($postData['quote']))
				{
                   	$postData['Post'] = $postData['quote'] . $postData['Post'];
				}

				if(!$topicId || ($topic->getSystem_user_id() == $system_user_id && !empty($postData['Title'])) || ($isModerator && !empty($postData['Title'])))
				{
					$topicModel     = Mage::getModel('forum/topic');
					$topicModel->setId($topicId)
	                    ->setStatus(($moderateTopic && !$topicId && !$isModerator ? 0 : 1))//active
	                    ->setTitle($postData['Title'])
						;

					$topicModel->setIcon_id($postData['icon_id']);

						if(!empty($postData['description']))
						{
							//$description    = strip_tags(ereg_replace("[\r\t\n\v]","",$postData['description']));
							$description    = strip_tags(preg_replace("/[\r\t\n\v]/","",$postData['description']));
							$topicModel->setDescription($description);
						}

					if(!empty($postData['product_id']))
					{
						$topicModel->setProduct_id($postData['product_id']);
					}
					if(!$isModerator || !$topicId)
                    {
						$topicModel->setUser_name($data['firstname'] .  ' ' . $data['lastname'])
						->setSystem_user_id($system_user_id);
					}
					if(Mage::getStoreConfig('forum/forumconfiguration/allowuseingnicknames') && !empty($postData['use_nick']))
					{
						if($postData['use_nick'] == 'on')$topicModel->setUser_nick($postData['NickName']);
					}
					else
					{
						$topicModel->setUser_nick('');
					}
					if(!$topicId)
					{
						$url_text = Mage::helper('forum/topic')->buildUrlKeyFromTitle($postData['Title'], $this->getRequest()->getParam('id'));
						$topicModel->setUrl_text('forum/' . $url_text);
						$topicModel->setUrl_text_short( $url_text );
						$topicModel->setCreated_time(now());
					}
					else
					{
						$url_text = $topic->getUrl_text();
						$topicModel->setUpdated_time(now());
					}
					if(!empty($postData['parent_id']) && !$topicId )
					{
						$topicModel->setParent_id($postData['parent_id']);
						$topicModel->setStore_id(Mage::helper('forum/topic')->___getStoreId($postData['parent_id']));
						if(Mage::helper('forum/topic')->getIsHaveSubtopics($postData['parent_id']))
						{
							$topicModel->setIs_subtopic(1);
						}
					}
					$topicModel->save();
					$topicModel->setEntityUserId($data['entity_id']);
					Mage::helper('forum/topic')->setEntity($topicModel->getId(), $topicModel, true);
				 	if(!$topicId && !empty($postData['parent_id']))
				 	{
				 		$store_id    = Mage::helper('forum/topic')->___getStoreId($postData['parent_id']);
						$id_path     = $this->buildIdPath($topicModel->getId());
	                	$requestPath = $this->buildRequestPath($topicModel->getId());
	                	Mage::helper('forum/topic')->updateUrlRewrite($id_path, $store_id, 'forum/' . $url_text, $requestPath);

	                	$this->_getSession()->addSuccess($moderateTopic && !$topicId && !$isModerator ? $this->__('The Topic was saved and will be added after moderation!') : $this->__('The Topic was successfully created!'));
	                	$url_text = 'forum/' . $url_text;
					}
					else
					{
						$this->_getSession()->addSuccess($moderateTopic && !$topicId && !$isModerator ? $this->__('The Topic was saved and will be added after moderation!') : $this->__('The Topic was successfully updated!'));
					}
					$postData['id'] = $topicModel->getId();
					if(!$isModerator && Mage::getStoreConfig('forum/forumnotification/allownotifymoder') == 1 && !$topicId)
					{
						Mage::helper('forum/notify')->notifyModerNewTopic($postData, $topicModel->getUrl_text(), $userName);
					}
				}
				if(empty($url_text) && $topic)
				{
					$url_text = $this->getUrlRewrite( $topic );
				}
				$topicId        = $topicId ? $topicId  : $topicModel->getId();
				$postData['id'] = $topicId;
				if($postId)
				{
					$post = Mage::getModel('forum/post')->load($postId);
				}
				if((!$postId || $post->getSystem_user_id() == $system_user_id ) || $isModerator)
				{
					$postModel = Mage::getModel('forum/post');

					$postModel->setPost_id($postId)
	                    ->setStatus(($moderatePost && !$postId && !$isModerator ? 0 : 1))//active
	                    ->setParent_id($topicId)

	                    ->setPost($postData['Post'])
	                    ->setPost_orig(strip_tags($postData['Post']));

						if(!empty($postData['product_id']))
						{
							$postModel->setProduct_id($postData['product_id']);
						}

						if(!$isModerator || !$postId)
	                    {
							$postModel->setUser_name($data['firstname'] .  ' ' . $data['lastname'])
							->setSystem_user_id($system_user_id);
						}
						if(Mage::getStoreConfig('forum/forumconfiguration/allowuseingnicknames') && !empty($postData['use_nick']))
						{
							if($postData['use_nick'] == 'on')$postModel->setUser_nick($postData['NickName']);
						}
						else
						{
							$postModel->setUser_nick('');
						}
	                if(!$postId)
	                {
						$postModel->setCreated_time(now());
						Mage::helper('forum/topic')->setUserForum($postModel->getSystem_user_id(), $postModel, Mage::helper('forum/topic')->___getStoreId($postData['parent_id']));
						Mage::helper('forum/topic')->updateTotalPostsUser($system_user_id, Mage::helper('forum/topic')->___getStoreId($postData['parent_id']));
					}
					else
					{
						$postModel->setUpdated_time(now());
					}

					$postModel->save();
					if(!$isModerator && Mage::getStoreConfig('forum/forumnotification/allownotifymoder') == 1)
					{
						$postData['post_id']        = $postModel->getId();
						Mage::helper('forum/notify')->notifyModerNewPost($postData, $url_text , $userName);
					}
					$this->_getSession()->addSuccess((!$moderatePost || $postId || $isModerator? $this->__('The Post was successfully saved!') : $this->__('The Post was saved and will be added after moderation!')) );
					Mage::getSingleton('forum/session')->setPostForumData(null);
					if( Mage::getStoreConfig('forum/forumnotification/allownotifycheckbox') )
					{
						if(!empty($postData['notify_me']))
						{
							if($postData['notify_me'] == 'on')
							{								Mage::helper('forum/notify')->addToNotifyList($topicId);
								$this->_getSession()->addSuccess($this->__('You are subscribed to this Topic!'));
							}
							else
							{                            	$show_uns_message = Mage::helper('forum/notify')->removeFromNotifyList($topicId);
								if($show_uns_message)
								{
	                            	$this->_getSession()->addSuccess($this->__('You are unsubscribed from this Topic!'));
								}
							}
						}
						else
						{							$show_uns_message = Mage::helper('forum/notify')->removeFromNotifyList($topicId);
							if($show_uns_message)
							{                            	$this->_getSession()->addSuccess($this->__('You are unsubscribed from this Topic!'));
							}
						}

						if(!$moderatePost || $isModerator)
						{
							$postData['system_user_id'] = $system_user_id;
							$postData['post_id']        = $postModel->getId();
							Mage::helper('forum/notify')->notifyCustomersPost($postData);
						}

					}
				}
				if($moderateTopic && !empty($topicModel) && !$isModerator && !$topicId)
				{
					$url_text = Mage::getModel('forum/topic')->load($topicModel->getParent_id())->getUrl_text();
				}

				if(!empty($redirect)) $this->_redirect($redirect);
				else $this->_redirect('*/*/viewreply/id/' . $postModel->getId());//$this->_redirect($url_text, array( '_current'=>false, '_escape'=>false, '_use_rewrite'=>false, '_query'=>array(self::PAGE_VAR_NAME => 1, self::SORT_VAR_NAME => 1)));

			}
			catch(Exception $e)
			{
				$this->_getSession()->addError($this->__('Error saving data!') . $e->getMessage());
				$this->_redirect('forum/', array( '_current'=>false, '_escape'=>false, '_use_rewrite'=>false));
			}
		}
		else
		{
			$this->_forward('index');
			return;
		}
	}

    public function editAction()
    {
    	$store = Mage::app()->getStore();
    	$this->_init();
		$data     = $this->customer->getData();
		if(empty($data['entity_id']))
		{
			$this->_forward('index');
			return;
		}
		$product_id = (int) $this->getRequest()->getParam('product_id', false);
		if(!empty($product_id))
		{
			$product = $this->registerProduct($product_id);
			if(!$this->current_product)
			{
				$this->_redirect('*/');
				return;
			}
		}
		$objectId          = (int) $this->getRequest()->getParam('id', false);
		$quoteId           = (int) $this->getRequest()->getParam('quote', false);
		$parentObjectId    = (int) $this->getRequest()->getParam('parent_id', false);
		$postId            = (int) $this->getRequest()->getParam('post_id', false);
		$redirect          = $this->getRequest()->getParam('ret', false);
		$sb                = $this->getRequest()->getParam('sb', false);
        $objectModel       = Mage::getModel('forum/topic')->load($objectId);
        $parentObjectModel = Mage::getModel('forum/topic')->load($parentObjectId);

        if($sb && !empty($product_id))
        {
			$this->_setProductBackLink($sb);
		}

        if($objectModel->getProduct_id() && empty($product_id))
		{
			$this->registerProduct($objectModel->getProduct_id());
		}

		if((!$parentObjectModel->getId() && $parentObjectId))
        {
			$this->_redirect('*/');
			return;
		}

		if(($parentObjectModel->getStore_id() != $store->getId() && $parentObjectModel->getStore_id()) || ($parentObjectModel->getStatus() == 0 && $parentObjectModel->getId())|| ($objectModel->getStatus() == 0 && $objectModel->getId()) || ($objectModel->getStore_id() != $store->getId() && $objectModel->getStore_id()))
		{
			$this->_redirect('*/');
			return;
		}

        $postModel         = Mage::getModel('forum/post')->load($postId);
        $isModerator       = Mage::helper('forum/topic')->isModerator();

        if(((!$objectModel->getId() && $objectId) || ($parentObjectId && !$parentObjectModel->getId()) || ($postId && $postModel->getSystem_user_id() != $this->customer->getId())) && !$isModerator)
        {
        	$this->_redirect('*/');
			return;
        }
        else
        {
			Mage::register('current_object',        $objectModel );
			Mage::register('redirect',              $redirect );
			Mage::register('quote',                 $quoteId );
			Mage::register('current_object_post',   $postModel );
			Mage::register('current_object_parent', $parentObjectModel );
        	$this->_loadLayout();
		}
	}

	public function deleteTopicAction()
	{
		$this->_init();
		if(Mage::getStoreConfig('forum/forumconfiguration/allowdeleteowntpics') == 0 )
		{
			$this->_forward('index');
			return;
		}
		if( $this->getRequest()->getParam('topic_id') > 0 )
		{
			$model = Mage::getModel('forum/topic')->load($this->getRequest()->getParam('topic_id'));

        	$isModerator       = Mage::helper('forum/topic')->isModerator();
			if($model->getSystem_user_id() != $this->customer->getId() && !$isModerator)
			{
				$this->_forward('index');
				return;
			}
			$ret = $this->getRequest()->getParam('ret');
			$ret = urldecode($ret);
            try
			{
				$this->deleteEntity($this->getRequest()->getParam('topic_id'));
				Mage::helper('forum/notify')->deleteByTopicId($this->getRequest()->getParam('topic_id'));
				$this->deleteUrlRewrite($this->getRequest()->getParam('topic_id'));
                $this->deleteAllPosts($this->getRequest()->getParam('topic_id'));
                $topicModel = Mage::getModel('forum/topic');
                $topicModel->setId($this->getRequest()->getParam('topic_id'))
                    ->delete();
                    $this->_getSession()->addSuccess($this->__('The Topic and its Posts were successfully deleted!'));
                $this->_redirect( $ret);
            }
			catch (Exception $e)
			{
                $this->_getSession()->addError($this->__('There was an error while deleting the Topic!'));
                $this->_redirect( $ret);
            }
        }
	}

	public function deletePostAction()
	{
		$this->_init();
		if( $this->getRequest()->getParam('post_id') > 0 )
		{
			$model = Mage::getModel('forum/post')->load($this->getRequest()->getParam('post_id'));
			$isModerator = Mage::helper('forum/topic')->isModerator();
			if($model->getSystem_user_id() != $this->customer->getId() && !$isModerator)
			{
				$this->_forward('index');
				return;
			}
			$ret = $this->getRequest()->getParam('ret');
			$ret = urldecode($ret);
            try
			{
                $postModel = Mage::getModel('forum/post');
                $postModel->setId($this->getRequest()->getParam('post_id'))
                    ->delete();
                    $this->_getSession()->addSuccess($this->__('The Post was successfully deleted!'));
                $this->_redirect( $ret);
            }
			catch (Exception $e)
			{
                $this->_getSession()->addError($this->__('There was an error while deleting the post!'));
                $this->_redirect( $ret);
            }
        }
	}

	public function disablePostAction()
	{
		$ret         = $this->getRequest()->getParam('ret');
		$isModerator = Mage::helper('forum/topic')->isModerator();
		if(!$isModerator)
		{
			$this->_forward('index');
			return;
		}
		$postId = $this->getRequest()->getParam('post_id');
		if($postId)
		{
			$this->_disablePost($postId);
		}
		$this->_getSession()->addSuccess($this->__('The Post was successfully disabled!'));
		$ret = urldecode($ret);
		$this->_redirect( $ret);
	}

	public function enablePostAction()
	{
		$ret         = $this->getRequest()->getParam('ret');
		$isModerator = Mage::helper('forum/topic')->isModerator();
		if(!$isModerator)
		{
			$this->_forward('index');
			return;
		}
		$postId = $this->getRequest()->getParam('post_id');
		if($postId)
		{
			$this->_enablePost($postId);
		}
		$this->_getSession()->addSuccess($this->__('The Post was successfully enabled!'));
		$ret = urldecode($ret);
		if( Mage::getStoreConfig('forum/forumnotification/allownotifycheckbox') )
		{
			$obj       = Mage::getModel('forum/post')->load($postId);
			if($obj->getId())$obj_topic = Mage::getModel('forum/topic')->load($obj->getParent_id());
			if($obj->getId())Mage::helper('forum/notify')->notifyCustomersPost( array('id'=>$obj->getParent_id(), 'parent_id' => $obj_topic->getParent_id(), 'system_user_id' => $obj->getSystem_user_id(), 'post_id' => $postId) );
		}
		$this->_redirect( $ret);
	}

	public function disableTopicAction()
	{
		$ret         = $this->getRequest()->getParam('ret');
		$isModerator = Mage::helper('forum/topic')->isModerator();
		if(!$isModerator)
		{
			$this->_forward('index');
			return;
		}
		$topicId = $this->getRequest()->getParam('topic_id');
		if($topicId)
		{
			$this->_getSession()->addSuccess($this->__('The Topic and its Post was successfully disabled!'));
			$this->_disableTopic($topicId);
		}
		$ret = urldecode($ret);
		$this->_redirect( $ret);
	}

	public function enableTopicAction()
	{
		$ret         = $this->getRequest()->getParam('ret');
		$isModerator = Mage::helper('forum/topic')->isModerator();
		if(!$isModerator)
		{
			$this->_forward('index');
			return;
		}
		$topicId = $this->getRequest()->getParam('topic_id');
		if($topicId)
		{
			$this->_enableTopic($topicId);
			$this->_getSession()->addSuccess($this->__('The Topic and its Post was successfully enabled!'));
		}
		$ret = urldecode($ret);
		$this->_redirect( $ret);
	}

	public function massDisableAction()
	{
		$post        = $this->getRequest()->getPost();
		$ret         = $this->getRequest()->getParam('ret');
		$ret 		 = urldecode($ret);
		$isModerator = Mage::helper('forum/topic')->isModerator();
		if(!$isModerator || empty($post['wio-action-element']))
		{
			$this->_forward('index');
			return;
		}
		$all  = count($post['wio-action-element']);
		$type = $post['type'];
		foreach($post['wio-action-element'] as $id)
		{
			if($type == 'topic')
			{
				$this->_disableTopic($id);
			}
			elseif($type == 'post')
			{
				$this->_disablePost($id);
			}
		}
		if($type == 'topic')    $this->_getSession()->addSuccess($this->__('%s Topics and theirs Post were successfully disabled!', $all));
		elseif($type == 'post') $this->_getSession()->addSuccess($this->__('%s Posts were successfully disabled!', $all));
		$this->_redirect( $ret);

	}

	public function massEnableAction()
	{
		$post 		 = $this->getRequest()->getPost();
		$ret         = $this->getRequest()->getParam('ret');
		$ret 		 = urldecode($ret);
		$isModerator = Mage::helper('forum/topic')->isModerator();
		if(!$isModerator || empty($post['wio-action-element']))
		{
			$this->_forward('index');
			return;
		}
		$all = count($post['wio-action-element']);
		$type = $post['type'];
		foreach($post['wio-action-element'] as $id)
		{
			if($type == 'topic')
			{
				$this->_enableTopic($id);
			}
			elseif($type == 'post')
			{
				$notify = $this->_enablePost($id);
				if( Mage::getStoreConfig('forum/forumnotification/allownotifycheckbox') && $notify)
				{
					$obj       = Mage::getModel('forum/post')->load($id);
					if($obj->getId())$obj_topic = Mage::getModel('forum/topic')->load($obj->getParent_id());
					if($obj->getId())Mage::helper('forum/notify')->notifyCustomersPost( array('id'=>$obj->getParent_id(), 'parent_id' => $obj_topic->getParent_id(), 'system_user_id' => $obj->getSystem_user_id(), 'post_id' => $id) );
				}
			}
		}
		if($type == 'topic')    $this->_getSession()->addSuccess($this->__('%s Topics and theirs Post were successfully enabled!', $all));
		elseif($type == 'post') $this->_getSession()->addSuccess($this->__('%s Posts were successfully enabled!', $all));
		$this->_redirect( $ret);
	}

	public function massDeleteAction()
	{
		$post        = $this->getRequest()->getPost();
		$ret         = $this->getRequest()->getParam('ret');
		$isModerator = Mage::helper('forum/topic')->isModerator();
		$ret 		 = urldecode($ret);
		if(!$isModerator || empty($post['wio-action-element']))
		{
			$this->_forward('index');
			return;
		}
		$all = count($post['wio-action-element']);
		$type = $post['type'];
		foreach($post['wio-action-element'] as $id)
		{
			if($type == 'topic')
			{
				try
				{
					$this->deleteEntity($id);
					Mage::helper('forum/notify')->deleteByTopicId($id);
					$this->deleteUrlRewrite($id);
	                $this->deleteAllPosts($id);
	                $topicModel = Mage::getModel('forum/topic');
	                $topicModel->setId($id)
	                    ->delete();
	            }
				catch (Exception $e)
				{
	                $this->_getSession()->addError($this->__('There was an error while deleting the Topic!'));
	            	$this->_redirect( $ret);
				}
			}
			elseif($type == 'post')
			{
				try
				{
	                $postModel = Mage::getModel('forum/post');
	                $postModel->setId($id)
	                    ->delete();
	            }
				catch (Exception $e)
				{
	                $this->_getSession()->addError($this->__('There was an error while deleting the post!'));
	            	$this->_redirect( $ret);
				}
			}
		}

		if($type == 'topic')    $this->_getSession()->addSuccess($this->__('%s Topics and theirs Post were successfully deleted!', $all));
		elseif($type == 'post') $this->_getSession()->addSuccess($this->__('%s Posts were successfully deleted!', $all));
		$this->_redirect( $ret);
	}

	private function _setProductBackLink($back)
	{
		$store = Mage::app()->getStore();
		$links = Mage::getModel('forum/session')->getBackProductLinks();
		if(!$links)
		{
			$links = array();
		}
        $links[$store->getId() . '_' . Mage::registry('current_product')->getId()] = urldecode($back);
        Mage::getModel('forum/session')->setBackProductLinks($links);
	}

	private function _disableTopic($topic_id)
	{
		$posts = Mage::getModel('forum/post')->getCollection();
		$posts->getSelect()->where('parent_id=?', $topic_id);
		foreach($posts as $post)
		{
			if($post->getId())
			{
				$this->_disablePost($post->getId(), $post);
			}
		}
		$topic = Mage::getModel('forum/topic')->load($topic_id);
		if($topic->getId())
		{
			$topic->setStatus(0);
			$topic->save();
		}
	}

	private function _enableTopic($topic_id)
	{
		$posts = Mage::getModel('forum/post')->getCollection();
		$posts->getSelect()->where('parent_id=?', $topic_id);
		foreach($posts as $post)
		{
			if($post->getId())
			{
				$this->_enablePost($post->getId(), $post);
			}
		}
		$topic = Mage::getModel('forum/topic')->load($topic_id);
		if($topic->getId())
		{
			$topic->setStatus(1);
			$topic->save();
		}
	}

	private function _disablePost($_postId, $post = false)
	{
		if(!$post)$post = Mage::getModel('forum/post')->load($_postId);
		if($post->getId() && $post->getStatus() == 1)
		{
			$post->setStatus(0);
			$post->save();
			return true;
		}
		else
		{
			return false;
		}
	}

	private function _enablePost($_postId, $post = false)
	{
		if(!$post)$post = Mage::getModel('forum/post')->load($_postId);
		if($post->getId() && $post->getStatus() == 0)
		{
			$post->setStatus(1);
			$post->save();
			return true;
		}
		else
		{
			return false;
		}
	}

	private function deleteEntity($id)
	{
		return Mage::helper('forum/topic')->deleteEntity($id);
	}

	private function deleteUrlRewrite($id)
	{
		$id_path = $this->buildIdPath($id);
		Mage::helper('forum/topic')->deleteUrlRewrite($id_path);
	}

	private function buildIdPath($id)
	{
		return Mage::helper('forum/topic')->buildIdPath($id);
	}

	private function deleteAllPosts($id)
	{
		$collection  = Mage::getModel('forum/post')->getCollection();
		$collection->getSelect()->where('status=1');
		$collection->getSelect()->where('parent_id=?', $id);
		foreach($collection as $post)
		{
			try
			{
				$modelDelete  = Mage::getModel('forum/post');
				$modelDelete->setId($post->getId())
                    ->delete();
			}
			catch(Exception $e)
			{

			}
		}
	}

	private function _initObject($objectId)
	{
		$object = Mage::getModel('forum/topic')
            ->load($objectId);
        if (!Mage::helper('forum/topic')->canShow($object)) {
        	$this->_forward('index');
            return false;
        }
        Mage::register('current_object', $object);
		Mage::getSingleton('forum/session')->setLastViewedObjectId($object->getId());
		$objects_viewed = Mage::getSingleton('forum/session')->getLastViewedObjectIds() ? Mage::getSingleton('forum/session')->getLastViewedObjectIds() : array();
		if(!in_array($objectId, $objects_viewed))
		{
			array_push($objects_viewed, $objectId);
			Mage::helper( 'forum/topic' )->updateEnitityView($objectId);
			Mage::getSingleton('forum/session')->setLastViewedObjectIds( $objects_viewed );
		}
		$this->setMeta = true;

		if($object->getProduct_id())
		{
			$this->registerProduct($object->getProduct_id());
			$sb = $this->getRequest()->getParam('sb', false);
	        if($sb && $this->current_product)
	        {
				$this->_setProductBackLink($sb);
			}
		}
		return $this->_loadLayout();
	}

	private function _setMetaData()
	{
		$meta_description = false;
		$meta_keywords    = false;
		if(Mage::registry('current_object')->getIs_category() == 1)
		{
			$meta_description = Mage::registry('current_object')->getMeta_description();
			$meta_keywords    = Mage::registry('current_object')->getMeta_keywords();
		}
		elseif(Mage::registry('current_object')->getParent_id())
		{
			$parent_object = Mage::getModel('forum/topic')->load(Mage::registry('current_object')->getParent_id());
			if($parent_object->getId() && $parent_object->getStatus() == 1)
			{
				$meta_description = $parent_object->getMeta_description();
				$meta_keywords    = $parent_object->getMeta_keywords();
				Mage::register('parent_current_object', $parent_object);
			}
			else
			{
				$this->_redirect('*/');
			}
		}
		$head = $this->getLayout()->getBlock('head');
		if($head && $meta_description && $meta_description != '' && $meta_keywords && $meta_keywords != '')
		{
			$head->setKeywords($meta_keywords);
			$head->setDescription($meta_description);
		}
	}

	private function _loadLayout()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('forum/session');
		Mage::helper('forum/topic')->_isActive = true;
		if($this->setMeta)
		{
			$this->_setMetaData();
		}
		$this->renderLayout();
	}

	private function getUrlRewrite($topicModel)
	{
		return Mage::getModel('forum/topic')->load($topicModel->getId())->getUrl_text();
	}

	private function buildRequestPath($id)
	{
		return Mage::helper('forum/topic')->buildRequestPath($id);
	}

	private function getUseRecaptcha()
	{
		return Mage::getStoreConfig('forum/recaptchasetting/allowrecaptcha');
	}

	private function validateRecaptcha()
	{
		global $_SERVER;
		if(!$this->getUseRecaptcha())
		{
			return false;
		}
		$this->includeRecaptchLib();
		$privatekey = $this->getKey();
		$recaptcha_challenge_field = $this->getRequest()->getPost('recaptcha_challenge_field');
		$recaptcha_response_field  = $this->getRequest()->getPost('recaptcha_response_field');
		$resp = recaptcha_check_answer (
								$privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $recaptcha_challenge_field,
                                $recaptcha_response_field
		);

		if (!$resp->is_valid)
		{
			return Mage::helper('forum/topic')->__('Not Correct Recaptcha Text!');
		}
		return false;
	}

	private function includeRecaptchLib()
	{
		require_once 'recaptchalib.php';
	}

	private function getKey()
	{
		return Mage::getStoreConfig('forum/recaptchasetting/recaptchakeyprivate'); //recaptchakeyprivate
	}

	private function registerProduct( $product_id )
	{
		$product = Mage::getModel('catalog/product')
        ->setStoreId(Mage::app()->getStore()->getId())
        ->load($product_id);
        if($product->getId())
		{
	        if (!Mage::helper('catalog/product')->canShow($product))
			{
	            $product = false;
	        }
	        if (!in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds()))
			{
	            $product = false;
	        }

	        if($product)
	        {
				Mage::register('current_product', $product);
				$this->current_product = $product;
			}
		}
		return $product;
	}
}