<?php

class TM_AskIt_Block_Adminhtml_AskIt_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'askit';
        $this->_controller = 'adminhtml_askIt';
        
        $this->_updateButton('save', 'label', Mage::helper('askit')->__('Save Question'));
        $this->_updateButton('delete', 'label', Mage::helper('askit')->__('Delete Question'));
        $this->_updateButton('reset', 'label', Mage::helper('askit')->__('Go to Product'));
        $link = Mage::helper("adminhtml")->getUrl(
            "adminhtml/catalog_product/edit/",
            array('id' => Mage::registry('askit_data')->getProductId())
        );
        $this->_updateButton('reset', 'onclick', "setLocation('{$link}')");
        $this->_updateButton('reset', 'class', 'back');

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('new_answer_text') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'new_answer_text');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'new_answer_text');
                }
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('askit_data') && Mage::registry('askit_data')->getId() ) {
            return Mage::helper('askit')->__(
                "Edit Question '%s'",
                $this->stripTags(Mage::registry('askit_data')->getText())
            );
        } else {
            return Mage::helper('askit')->__('Add New Question');
        }
    }
}