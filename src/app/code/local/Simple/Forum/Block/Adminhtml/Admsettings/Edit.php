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


class Simple_Forum_Block_Adminhtml_Admsettings_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'forum';
        $this->_controller = 'adminhtml_admsettings';
        $this->_updateButton('save', 'label', Mage::helper('forum/topic')->__('Save Settings'));
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getHeaderText()
    {
        return Mage::helper('forum/topic')->__("Edit Admin Front Settings");
    }
}
