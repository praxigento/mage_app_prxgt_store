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

class Simple_Forum_Block_Adminhtml_Post_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('post_form', array('legend'=>Mage::helper('forum/post')->__('Post Details'),'class'=>'fieldset-wide'));
        $topics   = $this->_getTopicsSelectOptions();
        $fieldset->addField('parent_id', 'select', array(
            'label'     => Mage::helper('forum/post')->__('Topic'),
            'name'      => 'parent_id',
            'values'    => $topics,
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('forum/post')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('forum/post')->__('Active'),
                ),
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('forum/post')->__('Inactive'),
                ),
            ),
        ));

        $fieldset->addField('is_sticky', 'select', array(
            'label'     => Mage::helper('forum/post')->__('Always at top'),
            'name'      => 'is_sticky',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('forum/post')->__('Yes'),
                ),
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('forum/post')->__('No'),
                ),
            ),
        ));

        $fieldset->addField('post', 'editor', array(
            'name'      => 'post',
            'label'     => Mage::helper('forum/post')->__('Post Content'),
            'title'     => Mage::helper('forum/post')->__('Post Content'),
            'style'     => 'width:98%; height:200px;',
        ));

        if ( Mage::getSingleton('adminhtml/session')->getPostData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
            Mage::getSingleton('adminhtml/session')->setPostData(null);
        } elseif ( Mage::registry('post_data') ) {
            $form->setValues(Mage::registry('post_data')->getData());
        }
        return parent::_prepareForm();
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('cms')->__('Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('cms')->__('Content');
    }

	private function _getTopicsSelectOptions()
	{
		if(!Mage::registry('store_id'))
    	{
			$data = Mage::helper('forum/topic')->getOptionsTopics(true, 0, array(), false, false, false);
		}
		else
		{
			$data = Mage::helper('forum/topic')->getOptionsTopics(true, 0, array(), false, false, false, true, Mage::registry('store_id'));
		}
		return $data;
	}
}

?>