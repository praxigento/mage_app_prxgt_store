<?php

class TM_AskIt_Block_Adminhtml_AskIt_EditAnswer_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
    
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_answer_form',
            'action' => $this->getUrl('*/*/save', array('id' => $id)),
            'method' => 'post'
        ));

        if (Mage::registry('askit_data') ) {
            $data = Mage::registry('askit_data')->getData();
        }

        $fieldset = $form->addFieldset(
            'askit_form',
            array('legend'=>Mage::helper('askit')->__('Answer information'))
        );
        
        $fieldset->addField('id', 'hidden', array(
          'class'     => 'required-entry',
          'name'      => 'id'
        ));
        
        $fieldset->addField('parent_id', 'hidden', array(
          'label'     => Mage::helper('askit')->__('Parent Id') ,
          'class'     => 'required-entry',
          'required'  => true,
          'disabled'  => null !== $id ? true : false,
          'name'      => 'parent_id',
        ));
        
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

        $fieldset->addField('text', 'editor', array(
          'label'     => Mage::helper('askit')->__('Text'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'text',
          'style'     => 'height:36em',
          'config'    => $wysiwygConfig
        ));
        
        $fieldset->addField('hint', 'text', array(
          'label'     => Mage::helper('askit')->__('Hint'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'hint',
        ));
        
        $fieldset->addField('customer_name', null !== $id ? 'label' : 'text', array(
          'label'     => Mage::helper('askit')->__('Customer'),
          'class'     => 'required-entry',
          'name'      => 'customer_name'
        ));
        $fieldset->addField('product_id', 'hidden', array(
          'class'     => 'required-entry',
          'name'      => 'product_id'
        ));
        $fieldset->addField('email',  null !== $id ? 'label' : 'text', array(
          'label'     => Mage::helper('askit')->__('Email'),
          'class'     => 'required-entry',
          'name'      => 'email'
        ));
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('askit')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'values'    => array(
                array(
                  'value'     => 3,
                  'label'     => Mage::helper('askit')->__('Disapprowed'),
                ),
                array(
                  'value'     => 2,
                  'label'     => Mage::helper('askit')->__('Approwed'),
                ),
                array(
                  'value'     => 1,
                  'label'     => Mage::helper('askit')->__('Pending'),
                ),
            ),
        ));
        $fieldset->addField('created_time', 'date', array(
            'label'     => Mage::helper('askit')->__('Create date'),
            'required'  => true,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
            //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
            'name'      => 'created_time',
        ));
        $fieldset->addField('update_time', 'date', array(
            'label'     => Mage::helper('askit')->__('Update date'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
            //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
            'name'      => 'update_time',
        ));
        $form->setValues($data);
        $form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }    
}