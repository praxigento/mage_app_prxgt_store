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


class Simple_Forum_Adminhtml_ForumController extends Mage_Adminhtml_Controller_Action
{
	const ADM_USER_ID = 10000000;

    protected function _initAction()
    {
    	$store_id = $this->getRequest()->getParam('store');
    	if($store_id)
    	{
			Mage::register('store_id', $store_id);
		}
        $this->loadLayout()
            ->_setActiveMenu('forum/forum')
            ->_addBreadcrumb(Mage::helper('forum/forum')->__('Forum Manager'), Mage::helper('forum/forum')->__('Forum Manager'));
        return $this;
    }

 	/**
     * default action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Create new post
     */
    public function newAction()
    {
    	$this->_forward('edit');
    }

    /**
    * Edit action
    */
    public function editAction()
    {
        $forumId     = $this->getRequest()->getParam('id');
        $forumModel  = Mage::getModel('forum/forum')->load($forumId);

        $store_id    = $this->getRequest()->getParam('store', false);
		if($store_id)Mage::register('store_id', $store_id);

		if ($forumModel->getId() || $forumId == 0)
		{
            Mage::register('forum_data', $forumModel);
            $this->loadLayout();
            $this->_setActiveMenu('forum/forum');
            $this->_addBreadcrumb(Mage::helper('forum/forum')->__('Forum Manager'), Mage::helper('forum/forum')->__('Forum Manager'));
            $this->_addBreadcrumb(Mage::helper('forum/forum')->__('Item Forum'), Mage::helper('forum/forum')->__('Item Forum'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('forum/adminhtml_forum_edit'))
                 ->_addLeft($this->getLayout()->createBlock('forum/adminhtml_forum_edit_tabs'));
            $this->renderLayout();
        }
		else
		{
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('forum/forum')->__('Forum does not exist'));
            $this->_redirect('*/*/');
        }
    }

    /**
    * Save action
    */
	public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData   = $this->getRequest()->getPost();
                $forumModel = Mage::getModel('forum/forum');
                //validate url key
                if($postData['url_text_short'] != '')
                {
                	$notValidUrlKey = Mage::helper('forum/topic')->validateUrlKey('forum/'. $postData['url_text_short'], $this->getRequest()->getParam('id'), $postData['store_id']);
                	if($notValidUrlKey)
	                {
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('forum/forum')->__('Forum URL Key for specified forum already exist in store.'));
		                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$this->getRequest()->getParam('store', 0)));
		                Mage::getSingleton('adminhtml/session')->setPostData($this->getRequest()->getPost());
                		return;
					}
				}
				else
				{
					$postData['url_text_short'] = Mage::helper('forum/topic')->buildUrlKeyFromTitle($postData['title'], $this->getRequest()->getParam('id'));
				}
					//$description = strip_tags( ereg_replace("[\r\t\n\v]","",$postData['description']) );
					$description    = strip_tags(preg_replace("/[\r\t\n\v]/","",$postData['description']));
					$forumModel->setId($this->getRequest()->getParam('id'))
                    ->setIs_category(1)
                    ->setStatus($postData['status'])
                    ->setUser_name('admin')
                    ->setUrl_text_short($postData['url_text_short'])
                    ->setUrl_text('forum/' . $postData['url_text_short'])
                    ->setTitle($postData['title'])
                    ->setPriority( $postData['priority'] )
                    ->setIcon_id( $postData['icon_id'] )
                    ->setDescription( $description )
					->setStore_id($postData['store_id'])
					;


                if(!$this->getRequest()->getParam('id')) //new item
                {
					$forumModel->setCreated_time(now());
				}
				else
				{
					$this->updateTopicsRewrites($forumModel->getId(), $postData['store_id']);
					$forumModel->setUpdate_time(now());
				}
                $forumModel->setMeta_description($postData['meta_description']);
                $forumModel->setMeta_keywords($postData['meta_keywords']);
                $forumModel->save();

                $forumModel->setEntityUserId(self::ADM_USER_ID);
                $id_path     = $this->buildIdForumPath($forumModel->getId());
                $requestPath = $this->buildRequestPath($forumModel->getId());
                Mage::helper('forum/topic')->updateUrlRewrite($id_path, $postData['store_id'], 'forum/' . $postData['url_text_short'], $requestPath);
                Mage::helper('forum/topic')->setEntity($forumModel->getId(), $forumModel, true);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/forum')->__('Forum was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPostData(false);
                if($this->getRequest()->getParam('back') == 'edit') $this->_redirect('*/*/edit', array('id' => $forumModel->getId(), 'store' => $this->getRequest()->getParam('store', 0)));
				else $this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store', 0)));
                return;
            }
			catch (Exception $e)
			{
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPostData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$this->getRequest()->getParam('store', 0)));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
    * Delete action
    */
    public function deleteAction()
    {
		if( $this->getRequest()->getParam('id') > 0 )
		{
            try
			{
				$this->deleteEntity($this->getRequest()->getParam('id'));
				$this->deleteUrlRewrite($this->getRequest()->getParam('id'), 1);
                $this->deleteAllTopics($this->getRequest()->getParam('id'));
                $forumModel = Mage::getModel('forum/forum');
                $forumModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/forum')->__('Forum was successfully deleted.<br>All Forum Topics were successfully deleted!<br>All Topics Posts were successfully deleted!'));
                $this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store', 0)));
            }
			catch (Exception $e)
			{
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$this->getRequest()->getParam('store', 0)));
            }
        }
        $this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store', 0)));
	}

	public function massDeleteAction()
	{
		$params = $this->getRequest()->getParams();
		$ids = $params['forum'];
		if(is_array($ids))
		{
			foreach($ids as $id)
			{
				try
				{
					$this->deleteEntity($id);
					$this->deleteUrlRewrite($id, 1);
	                $this->deleteAllTopics($id);
	                $forumModel = Mage::getModel('forum/forum');
	                $forumModel->setId($id)
	                    ->delete();

	            }
				catch (Exception $e)
				{
	                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
	                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$this->getRequest()->getParam('store', 0)));
	            }
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/forum')->__('All Forums were successfully deleted.<br>All Forums Topics were successfully deleted!<br>All Topics Posts were successfully deleted!'));
		$this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store', 0)));
	}

	public function massStatusAction()
	{
		$params = $this->getRequest()->getParams();
		$status = (!empty($params['status']) ? $params['status'] : 0);
		$ids = $params['forum'];
		if(is_array($ids))
		{
			foreach($ids as $id)
			{
				$forum = Mage::getModel('forum/forum')->load($id);
				$forum->setStatus($status==2?0:$status);
				$forum->setUpdate_time(now());
				$forum->save();
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/forum')->__('All Forums statuses were successfully changed.'));
		$this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store', 0)));
	}

	private function buildRequestPath($id)
	{
		return Mage::helper('forum/topic')->buildRequestForumPath($id);
	}

	private function buildIdForumPath($id)
	{
		return Mage::helper('forum/topic')->buildIdForumPath($id);
	}

	private function buildIdPath($id)
	{
		return Mage::helper('forum/topic')->buildIdPath($id);
	}

	private function deleteUrlRewrite($id, $is_forum = false)
	{
        if(!$is_forum)$id_path = $this->buildIdForumPath($id);
        else $id_path = $this->buildIdPath($id);
		Mage::helper('forum/topic')->deleteUrlRewrite($id_path);
	}

	private function deleteAllTopics($id)
	{
		$collection  = Mage::getModel('forum/topic')->getCollection();
		$collection->getSelect()->where('parent_id=?', $id);
		foreach($collection as $topic)
		{
			try
			{
				$this->deleteEntity($topic->getId());
				$this->deleteUrlRewrite($topic->getId());
				$this->deleteAllPosts($topic->getId());
				Mage::helper('forum/notify')->deleteByTopicId($topic->getId());
				$modelDelete  = Mage::getModel('forum/topic');
				$modelDelete->setId($topic->getId())
                    ->delete();
			}
			catch(Exception $e)
			{

			}
		}
	}


    private function deleteAllPosts($id)
	{
		$collection  = Mage::getModel('forum/post')->getCollection();
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

	private function deleteEntity($id)
	{
		return Mage::helper('forum/topic')->deleteEntity($id);
	}

	private function updateTopicsRewrites($_id, $store_id)
	{
		$c = Mage::getModel('forum/topic')->getCollection();
		$c->getSelect()->where('parent_id=?', $_id);
		if($c->getSize())
		{
			foreach($c as $val)
			{
				Mage::helper('forum/topic')->___updateUrlStoreById($val->getId(), $store_id);
				Mage::helper('forum/topic')->updateTopicStoreId($val->getId(), $store_id);
			}
		}
	}
}
