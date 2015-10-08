<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Label
*/    
class Amasty_Label_Block_Adminhtml_Label extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_label';
        $this->_blockGroup = 'amlabel';
        $this->_headerText = Mage::helper('amlabel')->__('Product Labels');
        $this->_addButtonLabel = Mage::helper('amlabel')->__('Add Label');
        parent::__construct();
    }
}