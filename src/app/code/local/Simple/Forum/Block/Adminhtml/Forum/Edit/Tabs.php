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


class Simple_Forum_Block_Adminhtml_Forum_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('forum_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('forum/topic')->__('Forum Details'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('forum/topic')->__('Forum Details'),
            'title'     => Mage::helper('forum/topic')->__('Forum Details'),
            'content'   => $this->getLayout()->createBlock('forum/adminhtml_forum_edit_tab_form')->toHtml(),
        ));

        $this->addTab('form_section_meta', array(
            'label'     => Mage::helper('forum/topic')->__('Forum Meta Data'),
            'title'     => Mage::helper('forum/topic')->__('Forum Meta DataDetails'),
            'content'   => $this->getLayout()->createBlock('forum/adminhtml_forum_edit_tab_formmeta')->toHtml(),
        ));

        $this->addTab('form_section_icons', array(
            'label'     => Mage::helper('forum/topic')->__('Forum Icons'),
            'title'     => Mage::helper('forum/topic')->__('Forum Icons'),
            'content'   => $this->getLayout()->createBlock('forum/adminhtml_forum_edit_tab_icons')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
