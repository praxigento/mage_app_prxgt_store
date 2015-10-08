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


class Simple_Forum_Block_Adminhtml_Moderators_Index_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('moderators_tabs');
        $this->setDestElementId('content');
        $this->setTitle(Mage::helper('forum/topic')->__('Manage Moderators'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('moderators_section', array(
            'label'     => Mage::helper('forum/topic')->__('Moderators'),
            'title'     => Mage::helper('forum/topic')->__('Moderators'),
            'content'   => $this->getLayout()->createBlock('forum/adminhtml_moderators_index_tab_gridmoderators')->toHtml(),
        ));
       	
       	$this->addTab('moderators_section_2', array(
            'label'     => Mage::helper('forum/topic')->__('Create from Customer'),
            'title'     => Mage::helper('forum/topic')->__('Create Forum Moderators from Customers'),
            'content'   => $this->getLayout()->createBlock('forum/adminhtml_moderators_index_tab_fromcustomermoder')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
