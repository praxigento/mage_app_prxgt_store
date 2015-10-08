<?php
class TM_AskIt_Block_Adminhtml_AskIt_Edit_Tab_AddAnswers 
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        $link = $this->getUrl('*/*/save', array('id' => $id));
        $form = new Varien_Data_Form(array(
            'id' => 'new_answer_form',
            'action' => $link,
            'method' => 'post'
        ));
        
        $fieldset = $form->addFieldset(
            'askit_add_new_answer_form',
            array('legend' => Mage::helper('askit')->__('Add New Answer'))
        );
        
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
            'tab_id'                      => $this->getTabId(),
            'add_variables'               => false,
            'add_widgets'                 => false,
            'add_directives'              => true,
            'files_browser_window_url'    => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
            
            'files_browser_window_width'  => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
            'files_browser_window_height' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height'),
            'encode_directives'           => true,
            'directives_url'              => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive')
        ));

        $fieldset->addField('new_answer_text', 'editor', array(
            'label' => Mage::helper('askit')->__('Text'),
            'name'  => 'new_answer_text',
            'config' => $wysiwygConfig
        ));
        
        $onclick = "if ($('new_answer_text').value == '') " .
            " $('new_answer_text').addClassName('required-entry validation-failed');" .
            "editForm.submit();return false;";
            
        $fieldset->addField('add', 'button', array(
            'value' => Mage::helper('askit')->__('Add Answer'),
            'class' => 'form-button',
            'onclick' => $onclick
        ));
        
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}