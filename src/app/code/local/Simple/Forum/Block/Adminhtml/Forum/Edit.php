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


class Simple_Forum_Block_Adminhtml_Forum_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'topic_id';
        $this->_blockGroup = 'forum';
        $this->_controller = 'adminhtml_forum';
        $this->_updateButton('save', 'label', Mage::helper('forum/topic')->__('Save Forum'));
        $this->_updateButton('delete', 'label', Mage::helper('forum/topic')->__('Delete Forum'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0)));
    }

    public function getHeaderText()
    {
        if( Mage::registry('forum_data') && Mage::registry('forum_data')->getId() ) 
		{
            return Mage::helper('forum/topic')->__("Edit Forum '%s'", $this->htmlEscape(Mage::registry('forum_data')->getTitle()));
        } 
		else 
		{
            return Mage::helper('forum/topic')->__('Add Forum');
        }
    }
}
