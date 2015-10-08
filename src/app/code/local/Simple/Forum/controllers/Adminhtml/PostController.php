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


class Simple_Forum_Adminhtml_PostController extends Mage_Adminhtml_Controller_Action
{
	const ADM_USER_ID = 10000000;

    protected function _initAction()
    {
    	$store_id = $this->getRequest()->getParam('store');
    	Mage::register('store_id', $store_id);
        $this->loadLayout()
            ->_setActiveMenu('forum/post')
            ->_addBreadcrumb(Mage::helper('forum/topic')->__('Post Manager'), Mage::helper('forum/topic')->__('Post Manager'));
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
        $postId     = $this->getRequest()->getParam('id');
        $postModel  = Mage::getModel('forum/post')->load($postId);

		$store_id   = $this->getRequest()->getParam('store', false);
    	if($store_id)
		{
			Mage::register('store_id', $store_id);
		}

        if ($postModel->getId() || $postId == 0)
		{
            Mage::register('post_data', $postModel);
            $this->loadLayout();
            $this->_setActiveMenu('forum/post');
            $this->_addBreadcrumb(Mage::helper('forum/topic')->__('Post Manager'), Mage::helper('adminhtml')->__('Post Manager'));
            $this->_addBreadcrumb(Mage::helper('forum/topic')->__('Item Post'), Mage::helper('adminhtml')->__('Item Post'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('forum/adminhtml_post_edit'))
                 ->_addLeft($this->getLayout()->createBlock('forum/adminhtml_post_edit_tabs'));
            $this->renderLayout();
        }
		else
		{
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('forum/topic')->__('Post does not exist'));
            $this->_redirect('*/*/');
        }
    }

    /**
    * Save action
    */
	public function saveAction()
    {
    	$set_last_post = false;
        if ( $postData = $this->getRequest()->getPost() ) {
        	if($errors = $this->validatePost($postData))
        	{
        		Mage::getSingleton('adminhtml/session')->addError($errors);
                Mage::getSingleton('adminhtml/session')->setPostData($this->getRequest()->getPost());				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$this->getRequest()->getParam('store')));
				return;
        	}
            try {
                $postData = $this->getRequest()->getPost();
                $postModel = Mage::getModel('forum/post');

					$postModel->setId($this->getRequest()->getParam('id'))
                    ->setStatus($postData['status'])
                    ->setParent_id($postData['parent_id'])
                    ->setPost($postData['post'])
                    ->setIs_sticky($postData['is_sticky']);
                    $postModel->setPost_orig(strval(strip_tags($postData['post'])));
                if(!$this->getRequest()->getParam('id')) //new item
                {
					$postModel->setCreated_time(now());
					$postModel->setUser_name(__('admin'));
					$postModel->setSystem_user_id(self::ADM_USER_ID);

					Mage::helper('forum/topic')->setUserForum(self::ADM_USER_ID, $postModel, Mage::helper('forum/topic')->___getStoreId($postData['parent_id']));
					Mage::helper('forum/topic')->updateTotalPostsUser(self::ADM_USER_ID, Mage::helper('forum/topic')->___getStoreId($postData['parent_id']));
				}
				else
				{
					$postModel->setUpdate_time(now());
				}

                $postModel->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Post was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPostData(false);
				$this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store')));
                return;
            }
			catch (Exception $e)
			{
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPostData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$this->getRequest()->getParam('store')));
                return;
            }
        }
        $this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store')));
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
                $postModel = Mage::getModel('forum/post');
                $postModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/topic')->__('Post was successfully deleted'));
                $this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store')));
            }
			catch (Exception $e)
			{
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$this->getRequest()->getParam('store')));
            }
        }
        $this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store')));
	}


	public function massDeleteAction()
	{
		$params = $this->getRequest()->getParams();
		$ids = $params['post'];
		if(is_array($ids))
		{
			foreach($ids as $id)
			{
				try
				{
	                $postModel = Mage::getModel('forum/post');
	                $postModel->setId($id)
	                    ->delete();
				}
				catch (Exception $e)
				{
	                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
	                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$this->getRequest()->getParam('store')));
	            }
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/post')->__('All Posts were successfully deleted!'));
		$this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store')));
	}

	public function massStatusAction()
	{
		$params = $this->getRequest()->getParams();
		$status = (!empty($params['status']) ? $params['status'] : 0);
		$ids = $params['post'];
		if(is_array($ids))
		{
			foreach($ids as $id)
			{
				$post = Mage::getModel('forum/post')->load($id);
				$post->setStatus($status==2?0:$status);
				$post->setUpdate_time(now());
				$post->save();
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/post')->__('All Posts statuses were successfully changed.'));
		$this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store')));
	}

	private function validatePost($post)
	{    	$parent_id = $post['parent_id'];
    	if($errors = $this->validateParent($parent_id))
    	{        	return $errors;
    	}
	}

	private function validateParent($parent_id)
	{    	$errors = '';
     	$m = Mage::getModel('forum/topic')->load($parent_id);

	    if($m->getHave_subtopics() == 1)
	    {        	$errors .= Mage::helper('forum/topic')->__('Selected parent Topic could not have Posts. Only subtopics!') . '<br>';
	    }
	    if($m->getIs_category() == 1)
	    {			$errors .= Mage::helper('forum/topic')->__('Selected parent Topic is Forum and could not have Posts. Only Topics!') . '<br>';
	    }

	    return ($errors === '' ? false : $errors);
	}

}
