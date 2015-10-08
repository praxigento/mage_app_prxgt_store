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

class Simple_Forum_Block_Adminhtml_Admsettings extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    const AVATAR_ID_BLOCK = '_dummy';

	public function __construct()
    {
        $this->_controller = 'adminhtml_forum';
        $this->_blockGroup = 'forum';
        $this->_headerText     = Mage::helper('forum/topic')->__('Admin Front Settings');
        parent::__construct();
    }

	protected function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	public function getAvatarUrl()
	{
    	$model  = Mage::registry('admin_data');
    	$avatar = Mage::getStoreConfig('forum/advanced_settings/avatar_path');
		if($model->getAvatar_name() && file_exists(Mage::getBaseDir() . '/' .  Mage::getStoreConfig('forum/advanced_settings/avatar_path') . '/' . $model->getAvatar_name()))
        {
        	$img = $model->getAvatar_name();
         	if(Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') && Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') != '')
         	{
          		return Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') . $model->getAvatar_name();
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

	public function getDelAvatarUrl()
	{
        return $this->getUrl('*/*/delavatar');
	}

	public function getAvatarWidth()
	{
     	return Mage::getStoreConfig('forum/advanced_settings/avatar_width');
	}

	public function getAvatarBlock()
	{
    	return self::AVATAR_ID_BLOCK;
	}

    public function getHaveAvatar()
    {
    	$model  = Mage::registry('admin_data');
    	if($model->getAvatar_name() && file_exists(Mage::getBaseDir() . '/' .  Mage::getStoreConfig('forum/advanced_settings/avatar_path') . '/' . $model->getAvatar_name()))
        {
        	return true;
        }
    }

}

