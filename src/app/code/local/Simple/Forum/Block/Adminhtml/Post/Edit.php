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


class Simple_Forum_Block_Adminhtml_Post_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'forum';
        $this->_controller = 'adminhtml_post';
        $this->_updateButton('save', 'label', Mage::helper('forum/post')->__('Save Post'));
        $this->_updateButton('delete', 'label', Mage::helper('forum/post')->__('Delete Post'));
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0)));
    }

    public function getHeaderText()
    {
        if( Mage::registry('post_data') && Mage::registry('post_data')->getId() ) 
		{
            return Mage::helper('forum/post')->__("Edit Post ID: '%s'", $this->htmlEscape(Mage::registry('post_data')->getId())) 
			. 
			( Mage::registry('store_id') ? ' ' . Mage::helper('forum/topic')->__('Store') .  ": " . $this->htmlEscape(Mage::app()->getStore(Mage::registry('store_id'))->getName()): '');
        } 
		else 
		{
            return Mage::helper('forum/post')->__('Add Post')
            . 
			( Mage::registry('store_id') ? ' ' . Mage::helper('forum/topic')->__('Store') .  ": " . $this->htmlEscape(Mage::app()->getStore(Mage::registry('store_id'))->getName()): '');
        }
    }
}
