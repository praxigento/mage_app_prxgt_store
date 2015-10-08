<?php
class TM_AskIt_Block_Adminhtml_AskIt extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_askIt';
    $this->_blockGroup = 'askit';
    $this->_headerText = Mage::helper('askit')->__('Question');
    $this->_addButtonLabel = Mage::helper('askit')->__('Add Item');
    parent::__construct();
  }
  
 public function getCreateUrl()
  {
       return $this->getUrl('*/*/edit');
  }
  
}