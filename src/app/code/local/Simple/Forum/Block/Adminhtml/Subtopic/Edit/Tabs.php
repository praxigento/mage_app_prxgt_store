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


class Simple_Forum_Block_Adminhtml_Subtopic_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('forum_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('forum/topic')->__('Sub-Topic Details'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('forum/topic')->__('Sub-Topic Details'),
            'title'     => Mage::helper('forum/topic')->__('Sub-Topic Details'),
            'content'   => $this->getLayout()->createBlock('forum/adminhtml_subtopic_edit_tab_form')->toHtml(),
        ));

        $this->addTab('form_section_icons', array(
            'label'     => Mage::helper('forum/topic')->__('Sub-Topic Icons'),
            'title'     => Mage::helper('forum/topic')->__('Sub-Topic Icons'),
            'content'   => $this->getLayout()->createBlock('forum/adminhtml_subtopic_edit_tab_icons')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
