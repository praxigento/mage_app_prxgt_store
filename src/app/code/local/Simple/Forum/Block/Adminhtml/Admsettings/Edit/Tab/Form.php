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

class Simple_Forum_Block_Adminhtml_Admsettings_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('post_form', array('legend'=>Mage::helper('forum/topic')->__('Admin Front Settings')));
        $fieldset->addField('_dummy', 'text', array(
            'label'     => '',
            'name'      => '_dummy',
            'id'        => '_dummy'
        ));

        $fieldset->addField('nickname', 'text', array(
            'label'     => Mage::helper('forum/topic')->__('Nickname'),
            'name'      => 'nickname',
        ));

        $fieldset->addField('avatar', 'file', array(
            'label'     => Mage::helper('forum/topic')->__('Avatar'),
            'name'      => 'avatar',
        ));

        $fieldset->addField('signature', 'textarea', array(
            'label'     => Mage::helper('forum/topic')->__('Signature'),
            'name'      => 'signature',
        ));

        if ( Mage::getSingleton('adminhtml/session')->getPostData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
            Mage::getSingleton('adminhtml/session')->setPostData(null);
        } elseif ( Mage::registry('admin_data') ) {
            $form->setValues(Mage::registry('admin_data')->getData());
        }
        return parent::_prepareForm();
    }
}

?>