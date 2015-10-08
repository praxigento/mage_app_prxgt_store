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

class Simple_Forum_Block_Adminhtml_Forum_Edit_Tab_Formmeta extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('post_form', array('legend'=>Mage::helper('forum/topic')->__('Forum Meta Data'), 'class'=>'fieldset-wide'));
        
        $fieldset->addField('meta_keywords', 'editor', array(
            'name'      => 'meta_keywords',
            'label'     => Mage::helper('forum/post')->__('Meta Keywords'),
            'title'     => Mage::helper('forum/post')->__('Meta Keywords'),
            'style'     => 'width:98%; height:90px;',
        ));
        
        $fieldset->addField('meta_description', 'editor', array(
            'name'      => 'meta_description',
            'label'     => Mage::helper('forum/post')->__('Meta Description'),
            'title'     => Mage::helper('forum/post')->__('Meta Description'),
            'style'     => 'width:98%; height:90px;',
        ));
        
        if ( Mage::getSingleton('adminhtml/session')->getPostData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
            Mage::getSingleton('adminhtml/session')->setPostData(null);
        } elseif ( Mage::registry('forum_data') ) {
            $form->setValues(Mage::registry('forum_data')->getData());
        }
        return parent::_prepareForm();
    }
}

?>