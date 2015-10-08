<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Label
*/
class Amasty_Label_Block_Adminhtml_Label_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('labelTabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('amlabel')->__('Label Options'));
    }

    protected function _beforeToHtml()
    {
        $name = Mage::helper('amlabel')->__('General');
        $this->addTab('general', array(
            'label'     => $name,
            'content'   => $this->getLayout()->createBlock('amlabel/adminhtml_label_edit_tab_general')
                ->setTitle($name)->toHtml(),
        ));
        
        $name = Mage::helper('amlabel')->__('Images');
        $this->addTab('images', array(
            'label'     => $name,
            'content'   => $this->getLayout()->createBlock('amlabel/adminhtml_label_edit_tab_images')
                ->setTitle($name)->toHtml(),
        ));           
        
        $name = Mage::helper('amlabel')->__('Conditions');
        $this->addTab('condition', array(
            'label'     => $name,
            'content'   => $this->getLayout()->createBlock('amlabel/adminhtml_label_edit_tab_condition')
                ->setTitle($name)->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}