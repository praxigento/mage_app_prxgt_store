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

class Simple_Forum_Block_Adminhtml_Forum extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_forum';
        $this->_blockGroup = 'forum';
        $this->_headerText     = Mage::helper('forum/topic')->__('Forum Manager');
        $this->_addButtonLabel = Mage::helper('forum/topic')->__('Add New Forum');
        parent::__construct();
		$this->setTemplate('forum/forums.phtml');
    }

	protected function _prepareLayout()
	{
		$this->setChild('add_new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('forum/topic')->__('Add Forum'),
                    'onclick'   => "setLocation('".$this->getUrl('*/*/new', array('store'=>$this->getStoreId()))."')",
                    'class'   => 'add'
                    ))
                );
		return parent::_prepareLayout();
	}

	public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }
    
    public function getStoreId()
	{
		return Mage::registry('store_id');
	}
    
    public function isSingleStoreMode()
    {
		return Mage::app()->isSingleStoreMode();
	}
}

