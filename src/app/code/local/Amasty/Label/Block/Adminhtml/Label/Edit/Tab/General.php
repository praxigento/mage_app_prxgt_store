<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Label
*/
class Amasty_Label_Block_Adminhtml_Label_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Label_Helper_Data */
        $hlp   = Mage::helper('amlabel');
        $model = Mage::registry('amlabel_label');
        
        $fldInfo = $form->addFieldset('general', array('legend'=> $hlp->__('General')));
        
        $fldInfo->addField('name', 'text', array(
            'label'     => $hlp->__('Name'),
            'name'      => 'name',
        ));         
        
        $fldInfo->addField('pos', 'text', array(
            'label'     => $hlp->__('Priority'),
            'name'      => 'pos',
            'note'      => $hlp->__('Use 0 to show label first, and 99 to show it last'),
        ));         
      
        $fldInfo->addField('is_single', 'select', array(
            'label'     => $hlp->__('Hide if label with higher priority is already applied'),
            'name'      => 'is_single',
            'values'    => array(
                0 => $hlp->__('No'), 
                1 => $hlp->__('Yes'), 
             ),
        ));
          
        $fldInfo->addField('use_for_parent', 'select', array(
            'label'     => $hlp->__('Use for Parent'),
            'title'     => $hlp->__('Use for Parent'),
            'name'      => 'use_for_parent',
            'note'      => $hlp->__('Display child`s label for parent (configurable and grouped products only)'),
            'options'   => array(           
                '0' => $hlp->__('No'),
                '1' => $hlp->__('Yes'),
            ),
        ));
        
        $fldInfo->addField('stores', 'multiselect', array(
            'label'     => $hlp->__('Show In'),
            'name'      => 'stores[]',
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(), 
        ));      

        $fldInfo->addField('customer_group', 'multiselect', array(
            'name'      => 'customer_group[]',
            'label'     => $hlp->__('Customer Groups'),
            'values'    => $hlp->getAllGroups(),
            'note'      => $hlp->__('Leave empty to show label for all groups'),
        )); 

        //set form values
        $form->setValues($model->getData()); 
        
        return parent::_prepareForm();
    }
}