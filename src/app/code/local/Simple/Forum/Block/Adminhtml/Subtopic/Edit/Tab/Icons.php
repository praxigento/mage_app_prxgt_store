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

class Simple_Forum_Block_Adminhtml_Subtopic_Edit_Tab_Icons extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('post_form', array('legend'=>Mage::helper('forum/topic')->__('Subtopic Icons'), 'class'=>'fieldset-wide'));

        $icons  = $this->_getIcons();

        $fieldset->addField('icon_id', 'radios', array(
            'label'     => Mage::helper('forum/topic')->__('Icons'),
            'name'      => 'icon_id',
            'values'    => $icons,
            'separator' => '<br>',
        ));


     	if ( Mage::getSingleton('adminhtml/session')->getPostData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
            Mage::getSingleton('adminhtml/session')->setPostData(null);
        } elseif ( Mage::registry('topic_data') ) {
            $form->setValues(Mage::registry('topic_data')->getData());
        }

        return parent::_prepareForm();
    }

    private function _getIcons()
	{
    	return Mage::helper('forum/data')->getForumIconsRadios();
	}
}

?>