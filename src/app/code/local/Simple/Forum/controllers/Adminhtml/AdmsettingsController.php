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


class Simple_Forum_Adminhtml_AdmsettingsController extends Mage_Adminhtml_Controller_Action
{

	const ADM_USER_ID = 10000000;

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('forum/forum')
            ->_addBreadcrumb(Mage::helper('forum/forum')->__('Admin Front Settings'), Mage::helper('forum/forum')->__('Admin Front Settings'));
        return $this;
    }

 	/**
     * default action
     */
    public function indexAction()
    {
    	$this->_initAction();    	$model = Mage::getModel('forum/usersettings')->load(self::ADM_USER_ID, 'system_user_id');
        Mage::register('admin_data', $model);

  		$this->_setActiveMenu('forum/forum');
    	$this->_addBreadcrumb(Mage::helper('forum/forum')->__('Admin Front Settings'), Mage::helper('forum/forum')->__('Admin Front Settings'));
  		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
    	$this->_addContent($this->getLayout()->createBlock('forum/adminhtml_admsettings_edit'))
                 ->_addLeft($this->getLayout()->createBlock('forum/adminhtml_admsettings_edit_tabs'));

        $this->renderLayout();
    }


	public function saveAction()
	{    	$post = $this->getRequest()->getPost();
    	if(!$errors = $this->validateNickname($post['nickname']))
    	{        	try
        	{            	$m      = Mage::getModel('forum/usersettings')->load( self::ADM_USER_ID, 'system_user_id');
				$m->setNickname($post['nickname']);
				$m->setSignature($post['signature']);
				$m->setSystem_user_id(self::ADM_USER_ID);
				$m->setWebsite_id(0);

				if ( isset($_FILES['avatar']) && $_FILES['avatar']['name'] != '' ) {
					$newfileName  = $this->getNewFileName($_FILES['avatar']['name']);
	            	if ( ( $error = $this->uploadFile($_FILES['avatar'], $newfileName) ) !== true ) {
	            		$this->_getSession()->addError($error);
	            		$this->_redirect('*/*/index');
	            		return;
	            	}
	            	else
	            	{
	            		if($m->getAvatar_name() && $m->getAvatar_name() != '')
	            		{
                    		$this->deleteAvatar($m->getAvatar_name());
                    	}
                    	$m->setAvatar_name($newfileName);
	            	}
	            }
				$m->save();
       			$this->_getSession()->addSuccess($this->__('Forum profile was successfully saved!'));
       			Mage::getSingleton('adminhtml/session')->setPostData(null);
       		}
        	catch(Exception $e)
        	{            	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            	Mage::getSingleton('adminhtml/session')->setPostData($this->getRequest()->getPost());
        	}
    	}
    	else
    	{        	Mage::getSingleton('adminhtml/session')->addError($errors);
            Mage::getSingleton('adminhtml/session')->setPostData($this->getRequest()->getPost());
    	}
    	$this->_redirect('*/*/index');
	}

	public function delavatarAction()
	{     	$m      = Mage::getModel('forum/usersettings')->load( self::ADM_USER_ID, 'system_user_id');

        if($m->getAvatar_name() && $m->getAvatar_name() != '')
  		{
         	$this->deleteAvatar($m->getAvatar_name());
         	$m->setAvatar_name('');

         	$m->save();
         	$this->_getSession()->addSuccess($this->__('Profile image are deleted!'));
        }

        $this->_redirect('*/*/index');
	}

	private function deleteAvatar($filename)
	{
		$del = Mage::getBaseDir() . '/' .  Mage::getStoreConfig('forum/advanced_settings/avatar_path') . '/' . $filename;
		if(file_exists($del))
		{
        	unlink( $del );
		}
	}

	private function validateNickname($nickname = '')
	{
		$errors = '';    	if($nickname == '')
        {
        	  $errors .= ($errors != '' ? '<br>' : '') . Mage::helper('forum/topic')->__('Nickname could not be empty');
        }
        if(strlen($nickname) < 5)
        {
        	  $errors .= ($errors != '' ? '<br>' : '') . Mage::helper('forum/topic')->__('Nickname should be more then 4 characters length');
        }

        $check_collection = Mage::getModel('forum/usersettings')->getCollection();
        $check_collection->getSelect()->where('nickname=?', $nickname)->where('system_user_id!=?', self::ADM_USER_ID);
        $check_collection->setPageSize(1);
        if($check_collection->getSize())
        {
        	$errors .= ($errors != '' ? '<br>' : '') . Mage::helper('forum/topic')->__('Nickname already exists!');
        }
        return ( $errors != '' ? $errors : false );
	}

	private function uploadFile($file, $newfileName)
	{
        $max_size_file    = 3670016;
		$uploadavatar     = Mage::getModel('forum/uploadavatar');
		$uploadavatar->upload_dir = Mage::getBaseDir() . '/' .  Mage::getStoreConfig('forum/advanced_settings/avatar_path') . '/';
		$uploadavatar->extensions = array(".gif", ".jpg",".jpeg",".png");
		$uploadavatar->max_length_filename = 100;
		$uploadavatar->rename_file   = true;
		$uploadavatar->filename      = self::ADM_USER_ID;
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
	{
    	$name = strtolower($filename);
		$ext  = substr( strrchr($name, '.'), 1);

		$new_name = time() . '_' . self::ADM_USER_ID . '.' . $ext;
		return $new_name;
	}

}
