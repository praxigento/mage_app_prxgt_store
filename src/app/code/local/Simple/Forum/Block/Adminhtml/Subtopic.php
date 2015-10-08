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

class Simple_Forum_Block_Adminhtml_Subtopic extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_subtopic';
        $this->_blockGroup = 'forum';
        $this->_headerText     = Mage::helper('forum/topic')->__('Sub-Topic Manager');
        $this->_addButtonLabel = Mage::helper('forum/topic')->__('Add New Sub-Topic');
        parent::__construct();
        $this->setTemplate('forum/subtopics.phtml');
    }

	protected function _prepareLayout()
	{
		$this->setChild('add_new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('forum/topic')->__('Add Sub-Topic'),
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
    
    public function isSingleStoreMode()
    {
		return Mage::app()->isSingleStoreMode();
	}
	
	public function getStoreId()
	{
		return Mage::registry('store_id');
	}
}

