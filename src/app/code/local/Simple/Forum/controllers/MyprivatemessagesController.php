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

require_once 'Mage/Customer/controllers/AccountController.php';

class Simple_Forum_MyprivatemessagesController extends Mage_Customer_AccountController
{

	private $customer  = false;
	private $session   = false;
	const CUSTOMER_ID  = 'cid';
	const ADM_USER_ID  = 10000000;
	const MESSAGE_ID   = 'mid';
	const REDIRECT_VAR = 'r';

	private $__actions = array
	(
    	'index', 'sent', 'trash'
	);

	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

	public function indexAction()
	{
		Mage::register('myprivatemessages_inbox', true);
		$this->_init();
		//$this->_clearTrash();
		$this->_loadLayout();
	}

	public function sentAction()
	{    	Mage::register('myprivatemessages_sent', true);
		$this->_init();
		$this->_loadLayout();
	}

	public function trashAction()
	{    	Mage::register('myprivatemessages_trash', true);
		$this->_init();
		$this->_loadLayout();
	}

	public function cleartrashAction()
	{
	}

	public function viewAction()
	{    	Mage::register('myprivatemessages_view', true);
		$this->_init();
        $mid = $this->getRequest()->getParam( self::MESSAGE_ID );
    	$r   = $this->getRequest()->getParam( self::REDIRECT_VAR );
  		$message = Mage::getModel('forum/privatemessages')->load($mid);
        if(!$message->getId() || ($message->getSent_from() != $this->customer->getId() && $message->getSent_to() != $this->customer->getId()))
        {            $this->_redirect('forum');
            return;
        }
        if($message->getIs_read() == 0 && $message->getSent_to() == $this->customer->getId())
        {        	$this->_read($message->getId());
        }
        $parents = $this->getAllParents($message);

        Mage::register('message', $message);
        Mage::register('r', $r);
        Mage::register('r_arr', $this->__actions);
        Mage::register('parents', $parents);

		$this->_loadLayout();
	}

	public function addAction()
	{		$this->_init();
		$customer_id = $this->getRequest()->getParam(self::CUSTOMER_ID);
		if(!$customer_id || $customer_id === self::ADM_USER_ID || $this->customer->getId() == $customer_id)
		{        	$this->_redirect('forum');
        	return;
		}
        $customer       = Mage::getModel('customer/customer')->load($customer_id);
        $customer_forum = Mage::getModel('forum/usersettings')->load($customer_id, 'system_user_id');
        if(!$customer->getId())
        {            $this->_redirect('forum');
            return;
        }
		Mage::register('send_to_customer_id', $customer_id);
		Mage::register('customer_system', $customer);
		Mage::register('customer_forum',  $customer_forum);    	Mage::register('myprivatemessages_add', true);

		$this->_loadLayout();
	}

	public function saveAction()
	{
  		$this->_init();    	if ( $this->getRequest()->getPost() )
    	{
    		$isModerator = Mage::helper('forum/topic')->isModerator();
    		$postData    = $this->getRequest()->getPost();
    		$valid       = $this->validateData($postData);
    		if($valid)
    		{	         	try
	         	{
	         		if(!$isModerator && $this->getUseRecaptcha())
					{
						$err = $this->validateRecaptcha();
						if($err)
						{
							$this->_getSession()->addError($err);
							$back = !empty( $postData['back'] )  ? urldecode($postData['back'])  : 'index';
							Mage::getSingleton('forum/session')->setPostPMData($postData);
							if(!empty($postData['parent_id']))
							{
								if(!empty($postData['r']))
								{                                	$r = $postData['r'];
								}
								else
								{                                	$r = $this->__actions[0];
								}                            	$this->_redirect('forum/myprivatemessages/view', array(self::MESSAGE_ID=>$postData['parent_id'], self::REDIRECT_VAR=>$r));
							}
							else
							{
								$this->_redirect('forum/myprivatemessages/add', array(self::CUSTOMER_ID => $postData['cid']));
							}
							return;
						}
					}
	         		$parent_id = (!empty($postData['parent_id']) ? $postData['parent_id'] : 0);
	    			$model = Mage::getModel('forum/privatemessages')->load(NULL);
	    			$message = strip_tags($postData['privatemessage'], '<b><ul><em>');
	    			$subject = strip_tags($postData['subject'], '<b><ul><em>');
	    			$model->setMessage($message);
	    			$model->setSubject($subject);
	    			$model->setSent_from($this->customer->getId());
	    			$model->setSent_to($postData['cid']);
	    			$model->setParent_id($parent_id);
	    			$model->setIs_primary(1);
	    			$model->setDate_sent(now());
	    			$this->_getSession()->addSuccess($this->__('Private message sent!'));
	    			$model->save();
	    			if($parent_id != 0)
	    			{                    	$parentModel = Mage::getModel('forum/privatemessages')->load($parent_id);
                    	$parentModel->setIs_primary(0);
                    	$parentModel->save();
	    			}
	    			if(Mage::getStoreConfig('forum/forumnotification/notify_private_messages'))
	    			{
         				$nick     = Mage::helper('forum/customer')->getCustomerNick($this->customer->getId());
         				$email_to = Mage::helper('forum/customer')->getEmail($postData['cid']);                    	Mage::helper('forum/customer')->notifyCustomerPrivateMessage( $nick, $email_to, $subject );
	    			}
        			$this->_redirect('forum/myprivatemessages/sent');
	            }
				catch (Exception $e)
				{
	                $this->_getSession()->addError($this->__('Error saving private message!') . $e->getMessage());
	                $this->_redirect('*/*/add', array(self::CUSTOMER_ID => $postData['cid']));
	                return;
	            }
	         }
	         else
	         {                    $this->_getSession()->addError($this->__('Error saving private message! Data not valid!'));
	                $this->_redirect('*/*/add', array(self::CUSTOMER_ID => $postData['cid']));
	                return;
	         }
    	}
    }

    public function movetrashAction()
    {
    	$this->_init();    	$mid = $this->getRequest()->getParam( self::MESSAGE_ID );
    	$r   = $this->getRequest()->getParam( self::REDIRECT_VAR );
		if(!$mid)
		{
        	$this->_redirect('forum');
        	return;
		}
		$this->_trash($mid);
        $this->_getSession()->addSuccess($this->__('%s private message(s) moved to trash!', 1));
        $this->_doRedirect($r);
    }

    public function undotrashAction()
    {    	$this->_init();
    	$mid = $this->getRequest()->getParam( self::MESSAGE_ID );
    	$r   = $this->getRequest()->getParam( self::REDIRECT_VAR );
		if(!$mid)
		{
        	$this->_redirect('forum');
        	return;
		}
		$this->_undotrash($mid);
		$this->_getSession()->addSuccess($this->__('%s private message(s) moved to inbox!', 1));
		$this->_doRedirect($r);
    }

    public function deleteAction()
    {    	$this->_init();
    	$mid = $this->getRequest()->getParam( self::MESSAGE_ID );
    	$r   = $this->getRequest()->getParam( self::REDIRECT_VAR );
		if(!$mid)
		{
        	$this->_redirect('forum');
        	return;
		}
		$this->_delete($mid);
		$this->_getSession()->addSuccess($this->__('%s private message(s) are deleted!', 1));
		$this->_doRedirect($r);
    }

    public function massreadAction()
    {
    	$this->_init();
    	$r   = $this->getRequest()->getParam( self::REDIRECT_VAR );    	$post = $this->getRequest()->getPost();
    	try
    	{
	    	$ids  = $post['wio-action-element'];
	    	if(is_array($ids))
	    	{	    		$count = 0;            	foreach($ids as $id)
            	{                	$this->_read($id);
                	$count++;
            	}
	    	}
	    	$this->_getSession()->addSuccess($this->__('%s private message(s) marked as read!', $count));
        	$this->_doRedirect($r);
	  	}
	  	catch (Exception $e)
		{
               $this->_getSession()->addError($this->__('Error updating message!') . $e->getMessage());
               $this->_redirect('*/*/index');
               return;
        }
    }

    public function masstrashAction()
    {		$this->_init();
    	$r    = $this->getRequest()->getParam( self::REDIRECT_VAR );
    	$post = $this->getRequest()->getPost();
    	try
    	{
	    	$ids  = $post['wio-action-element'];
	    	if(is_array($ids))
	    	{
	    		$count = 0;
            	foreach($ids as $id)
            	{
                	$this->_trash($id);
                	$count++;
            	}
	    	}
	    	$this->_getSession()->addSuccess($this->__('%s private message(s) moved to trash!', $count));
        	$this->_doRedirect($r);
	  	}
	  	catch (Exception $e)
		{
               $this->_getSession()->addError($this->__('Error updating message!') . $e->getMessage());
               $this->_redirect('*/*/index');
               return;
        }
    }

    public function massdeleteAction()
    {    	$this->_init();
    	$r    = $this->getRequest()->getParam( self::REDIRECT_VAR );
    	$post = $this->getRequest()->getPost();
    	try
    	{
	    	$ids  = $post['wio-action-element'];
	    	if(is_array($ids))
	    	{
	    		$count = 0;
            	foreach($ids as $id)
            	{
                	$this->_delete($id);
                	$count++;
            	}
	    	}
	    	$this->_getSession()->addSuccess($this->__('%s private message(s) are deleted!', $count));
        	$this->_doRedirect($r);
	  	}
	  	catch (Exception $e)
		{
               $this->_getSession()->addError($this->__('Error updating message!') . $e->getMessage());
               $this->_redirect('*/*/index');
               return;
        }
    }

    public function massundoAction()
    {    	$this->_init();
    	$r    = $this->getRequest()->getParam( self::REDIRECT_VAR );
    	$post = $this->getRequest()->getPost();
    	try
    	{
	    	$ids  = $post['wio-action-element'];
	    	if(is_array($ids))
	    	{
	    		$count = 0;
            	foreach($ids as $id)
            	{
                	$this->_undotrash($id);
                	$count++;
            	}
	    	}
	    	$this->_getSession()->addSuccess($this->__('%s private message(s) moved to Inbox!', $count));
        	$this->_doRedirect($r);
	  	}
	  	catch (Exception $e)
		{
               $this->_getSession()->addError($this->__('Error updating message!') . $e->getMessage());
               $this->_redirect('*/*/index');
               return;
        }
    }

    private function getAllParents($message, $objects = false)
    {    	$parent_id = $message->getParent_id();
       	$new_message = Mage::getModel('forum/privatemessages')->load($parent_id);
       	if(!$objects)
   		{
        	$objects = array();
   		}
       	if($new_message->getId())
       	{
       		$objects[] = $new_message;
           	if($new_message->getParent_id())
           	{
               	return $this->getAllParents($new_message, $objects);
           	}
           	else
           	{
				return array_reverse($objects);
           	}
       	}
       	return array_reverse($objects);
    }

    private function _doRedirect($r)
    {    	if(in_array($r, $this->__actions))
        {
        	if($r == 'index')
        	{
        		$this->_redirect('*/*/index');
        	}
        	elseif($r == 'sent')
        	{
                $this->_redirect('*/*/sent');
        	}
        	elseif($r == 'trash')
        	{
                $this->_redirect('*/*/trash');
        	}
        }
        else
        {
        	$this->_redirect('*/*/index');
        }
    }

	private function _read($mid)
	{        $model = Mage::getModel('forum/privatemessages')->load($mid);
     	if(!$model->getId() || ($model->getSent_to() != $this->customer->getId()))
     	{
        	$this->_redirect('forum');
        	return;
     	}
      	$model->setIs_read(true);
		$model->save();
	}

    private function _delete($mid)
    {    	$model = Mage::getModel('forum/privatemessages')->load($mid);
     	if(!$model->getId() || ($model->getSent_from() != $this->customer->getId() && $model->getSent_to() != $this->customer->getId()))
     	{        	$this->_redirect('forum');
        	return;
     	}
        if($model->getSent_from() == $this->customer->getId())
        {           $model->setIs_deletesent(true);
        }
        if($model->getSent_to() == $this->customer->getId())
        {           $model->setIs_deleteinbox(true);
        }

        $model->save();
    }

    private function _clearTrash()
    {    	$collection = Mage::getModel('forum/privatemessages')->getCollection();
        $collection->getSelect()->where('is_trash=1')->where('sent_to=?', Mage::registry('current_customer')->getId())->where('is_deleteinbox=?', 0)->where('is_primary=?', 1);
        if($collection->getSize())
        {        	foreach($collection as $object)
        	{
        		try
        		{					$o = Mage::getModel('forum/privatemessages')->load($object->getId());
					$o->setIs_deleteinbox();
					$o->save();
				}
				catch(Exception $e)
				{    				//ERRORS DEBUG
				}
        	}
        }
    }

    private function _undotrash($mid)
    {        $model = Mage::getModel('forum/privatemessages')->load($mid);
  		if(!$model->getId() || $model->getSent_to() != $this->customer->getId())
  		{
            $this->_redirect('forum');
            return;
  		}
  		$model->setIs_trash(0);
  		$model->save();
    }

    private function _trash($mid)
    {
  		$model = Mage::getModel('forum/privatemessages')->load($mid);
  		if(!$model->getId() || $model->getSent_to() != $this->customer->getId())
  		{
            $this->_redirect('forum');
            return;
  		}
  		$model->setIs_trash(1);
  		$model->save();
    }

    private function validateData($post)
    {    	if(!empty($post['cid']))
    	{
			// TODO add validation for magento registrated customers only        	//$object = Mage::getModel('forum/user')->load($post['cid'], 'system_user_id');
        	//if($object->getId())
        	{            	return true;
        	}
    	}
    	return false;
    }

	private function _init()
	{
		$sess           =  Mage::getSingleton('customer/session');
		$this->customer = $sess->getCustomer();
		if(!Mage::registry('current_customer'))Mage::register('current_customer', $this->customer);

		$this->session  = Mage::getSingleton('forum/session');
		return true;
	}

	private function _loadLayout()
	{
		$msg = $this->_getSession()->getMessages(true);
		$this->loadLayout();
		if ($block = $this->getLayout()->getBlock('forum_myprivatemessages'))
		{
   	 		$block->setRefererUrl($this->_getRefererUrl());
        }
        if(Mage::registry('myprivatemessages_trash') || Mage::registry('myprivatemessages_sent') || Mage::registry('myprivatemessages_add'))
        {
	        if ($my_account_block = $this->getLayout()->getBlock('customer_account_navigation'))
	        {
	        	if(Mage::registry('myprivatemessages_trash'))
	        	{	         		$my_account_block->setActive('forum/myprivatemessages/');
	        	}
	        	if(Mage::registry('myprivatemessages_sent'))
	        	{	                $my_account_block->setActive('forum/myprivatemessages/');
	        	}
	        }
	    }
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Private Messages'));
        $this->getLayout()->getMessagesBlock()->addMessages($msg);
		$this->_initLayoutMessages('forum/session');
		$this->renderLayout();
	}

	private function getUseRecaptcha()
	{
		return Mage::getStoreConfig('forum/recaptchasetting/allowrecaptchapm');
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

}

?>