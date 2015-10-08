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

class Simple_Forum_Block_Adminhtml_Topic_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $topics = $this->_getTopicsSelectOptions();
        $fieldset = $form->addFieldset('post_form', array('legend'=>Mage::helper('forum/topic')->__('Topic Details')));
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('forum/topic')->__('Topic'),
            'class'     => 'required-entry',
           	'required'  => true,
            'name'      => 'title',
        ));

        $fieldset->addField('url_text_short', 'text', array(
            'name'      => 'url_text_short',
            'label'     => Mage::helper('forum/post')->__('URL Key'),
            'class'     => 'validate-identifier',
            'note'      => Mage::helper('forum/topic')->__('Relative to Website Base URL/forum')
        ));

        $fieldset->addField('parent_id', 'select', array(
            'label'     => Mage::helper('forum/topic')->__('Parent Forum'),
            'name'      => 'parent_id',
            'values'    => $topics,
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('forum/topic')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('forum/topic')->__('Active'),
                ),
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('forum/topic')->__('Inactive'),
                ),
            ),
        ));

        $fieldset->addField('have_subtopics', 'select', array(
            'label'     => Mage::helper('forum/topic')->__('Have Subtopics'),
            'name'      => 'have_subtopics',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('forum/topic')->__('Yes'),
                ),
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('forum/topic')->__('No'),
                ),
            ),
        ));

        $fieldset->addField('description', 'editor', array(
            'name'      => 'description',
            'label'     => Mage::helper('forum/topic')->__('Description')
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

    private function _getTopicsSelectOptions()
    {
    	if(!Mage::registry('store_id'))
    	{
			$data = Mage::helper('forum/topic')->getOptionsTopics(true, Mage::registry('topic_data')->getId(), array('1' => 'is_category=?'), Mage::helper('forum/topic')->__('No Parent Forum'));
		}
		else
		{
			$data = Mage::helper('forum/topic')->getOptionsTopics(true, Mage::registry('topic_data')->getId(), array('1' => 'is_category=?'), Mage::helper('forum/topic')->__('No Parent Forum'), false, false, true, Mage::registry('store_id'));
		}
		return $data;
	}
}

?>