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

class Simple_Forum_MyaccountController extends Mage_Customer_AccountController
{

	private $customer = false;
	private $session  = false;

	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

	public function indexAction()
	{
		Mage::register('myforumaccount', true);
		$this->_init();
		$this->_loadLayout();
	}

	public function saveAction()
	{
		$this->_init();
		$post   = $fields = $this->getRequest()->getParams();
        if(!$errors = $this->validateFormData($post))
        {        	try
        	{
				$m      = Mage::getModel('forum/usersettings')->load( $this->customer->getId(), 'system_user_id');
				$m->setNickname($post['nickname']);
				$m->setSignature($this->getSignature($post));
				$m->setSystem_user_id($this->customer->getId());
				$m->setWebsite_id($this->_getSession()->getCustomer()->getWebsiteId());

				if ( isset($_FILES['avatar']) && $_FILES['avatar']['name'] != '' ) {					$newfileName  = $this->getNewFileName($_FILES['avatar']['name']);
	            	if ( ( $error = $this->uploadFile($_FILES['avatar'], $newfileName) ) !== true ) {
	            		$this->_getSession()->addError($error);
	            		$this->_redirect('forum/myaccount');
	            		return;
	            	}
	            	else
	            	{
	            		if($m->getAvatar_name() && $m->getAvatar_name() != '')
	            		{                    		$this->deleteAvatar($m->getAvatar_name());
                    	}
                    	$m->setAvatar_name($newfileName);
	            	}
	            }
				$m->save();
       			$this->_getSession()->addSuccess($this->__('Forum profile was successfully saved!'));
       			if($this->session)$this->session->setPostData(false);
			}
			catch (Exception $e)
			{
                $this->_getSession()->addError($this->__('Error saving data!') . $e->getMessage());
                if($this->session)$this->session->setPostData($post);
            }
		}
		else
		{        	$this->_getSession()->addError($errors);
        	if($this->session)$this->session->setPostData($post);
		}
    	$this->_redirect('forum/myaccount');
	}

	public function delavatarAction()
	{        $this->_init();
        $m      = Mage::getModel('forum/usersettings')->load( $this->customer->getId(), 'system_user_id');

        if($m->getAvatar_name() && $m->getAvatar_name() != '')
  		{
         	$this->deleteAvatar($m->getAvatar_name());
         	$m->setAvatar_name('');

         	$m->save();
         	$this->_getSession()->addSuccess($this->__('Profile image are deleted!'));
        }
        $this->_redirect('forum/myaccount');
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
		if ($block = $this->getLayout()->getBlock('forum_myaccount'))
		{
   	 		$block->setRefererUrl($this->_getRefererUrl());
        }
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Forum Account'));

		$this->getLayout()->getMessagesBlock()->addMessages($msg);
  		$this->_initLayoutMessages('core/session');
		$this->renderLayout();
	}

	private function deleteAvatar($filename)
	{
		$del = Mage::getBaseDir() . '/' .  Mage::getStoreConfig('forum/advanced_settings/avatar_path') . '/' . $filename;		if(file_exists($del))
		{        	unlink( $del );
		}
	}

	private function uploadFile($file, $newfileName)
	{
        $max_size_file    = 3670016;
		$uploadavatar     = Mage::getModel('forum/uploadavatar');
		$uploadavatar->upload_dir = Mage::getBaseDir() . '/' .  Mage::getStoreConfig('forum/advanced_settings/avatar_path') . '/';
		$uploadavatar->extensions = array(".gif", ".jpg",".jpeg",".png");
		$uploadavatar->max_length_filename = 100;
		$uploadavatar->rename_file   = true;
		$uploadavatar->filename      = $this->customer->getId();
		$uploadavatar->the_temp_file = $file['tmp_name'];
		$uploadavatar->the_file      = $file['name'];
		$uploadavatar->http_error    = $file['error'];
		$uploadavatar->replace       = true;
		$uploadavatar->do_filename_check =  "no";
		$new_name = $newfileName;
		if ($uploadavatar->upload($new_name))
		{
			return true;
		}
		else
		{
			$error    = array();
			$error[0] = $this->__('File: <b>%s</b> successfully uploaded!', $file['name']);
			$error[1] = $this->__('The uploaded file exceeds the max. upload filesize directive in the server configuration.');
			$error[2] = $this->__('The uploaded file exceeds the MAX_FILE_SIZE directive.');
			$error[3] = $this->__('The uploaded file was only partially uploaded');
			$error[4] = $this->__('No file was uploaded');
			$error[6] = $this->__('Missing a temporary folder.');
			$error[7] = $this->__('Failed to write file to disk.');
			$error[8] = $this->__('A PHP extension stopped the file upload.');

			// end  http errors
			$error[10] = $this->__('Please select a file for upload.');
			$error[11] = $this->__('Only files with the following extensions are allowed: <b>%s</b>', implode(', ', $uploadavatar->extensions));
			$error[12] = $this->__('Sorry. The filename contains invalid characters. Use only alphanumerical chars and separate parts of the name (if needed) with an underscore. <br>A valid filename ends with one dot followed by the extension.');
			$error[13] = $this->__('The filename exceeds the maximum length of %s characters.', $uploadavatar->max_length_filename);
			$error[14] = $this->__('Sorry. The upload directory does not exist!');
			$error[15] = $this->__('Uploading <b>%s...Error!</b> Sorry. A file with this name already exitst.', $file['name']);
			$error[16] = $this->__('The uploaded file is renamed to <b>%s</b>.', $new_name);
			$error[17] = $this->__('The file %s does not exist.', $new_name);
            $t = intval($uploadavatar->show_error_string());
            if(!empty($error[$t]))
            {
				return $error[$t];
			}
			else
			{
				return $this->__('Unknown error!');
			}
		}

	}

	private function getNewFileName($filename)
	{    	$name = strtolower($filename);
		$ext  = substr( strrchr($name, '.'), 1);

		$new_name = time() . $this->customer->getId() . '.' . $ext;
		return $new_name;
	}

	private function getSignature($post)
	{    	return strip_tags($post['signature'], '<b><ul><em>');
	}

	private function checkForumNickName($nickname = '', $errors = '')
	{          if($nickname == '')
          {          	  $errors .= ($errors != '' ? '<br>' : '') . Mage::helper('forum/topic')->__('Nickname could not be empty');
          }
          if(strlen($nickname) < 5)
          {          	  $errors .= ($errors != '' ? '<br>' : '') . Mage::helper('forum/topic')->__('Nickname should be more then 4 characters length');
          }

          $check_collection = Mage::getModel('forum/usersettings')->getCollection();
          $check_collection->getSelect()->where('nickname=?', $nickname)->where('system_user_id!=?', $this->customer->getId())->where('website_id=?', $this->_getSession()->getCustomer()->getWebsiteId());
          $check_collection->setPageSize(1);
          if($check_collection->getSize())
          {          	$errors .= ($errors != '' ? '<br>' : '') . Mage::helper('forum/topic')->__('Nickname already exists!');
          }
          return ( $errors != '' ? $errors : false );
	}

	private function validateFormData($post)
	{    	$errors = '';
        if($errors = $this->checkForumNickName($post['nickname']))
        {            return $errors;
        }
		return false;
	}

}

?>