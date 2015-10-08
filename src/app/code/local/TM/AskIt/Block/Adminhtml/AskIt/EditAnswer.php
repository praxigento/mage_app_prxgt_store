<?php
class TM_AskIt_Block_Adminhtml_AskIt_EditAnswer extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'askit';
        $this->_controller = 'adminhtml_askIt';
        
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('askit')->__('Save Item'));
        $this->_updateButton('save', 'onclick', 'sendAnswerForm();return false;');
        $this->_updateButton('delete', 'label', Mage::helper('askit')->__('Delete Item'));
        $this->_updateButton('reset', 'label', Mage::helper('askit')->__('Go to Question'));
        $this->_updateButton('reset', 'onclick', 'gotoQuestion()');
        $this->_updateButton('reset', 'class', 'back');

        $link = $this->getUrl('*/*/edit', array('id' => Mage::registry('askit_data')->getParentId()));

        $this->_formScripts[] = "
            function sendAnswerForm(){\$('edit_answer_form').submit();}
            function gotoQuestion(){setLocation('{$link}');}
            function toggleEditor() {
                if (tinyMCE.getInstanceById('text') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'text');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'text');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('askit_data') && Mage::registry('askit_data')->getId() ) {
            return Mage::helper('askit')->__(
                "Edit Answer '%s'",
                $this->stripTags(Mage::registry('askit_data')->getText())
            );
        }
    }
}