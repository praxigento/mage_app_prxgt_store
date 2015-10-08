<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Label
*/
class Amasty_Label_Block_Adminhtml_Label_Edit_Tab_Images extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Label_Helper_Data */
        $hlp   = Mage::helper('amlabel');
        $model = Mage::registry('amlabel_label');
        
        // {ATTR:code} - attribute value, {STOCK_QTY} - quantity in stock
        $note = 'Variables: {ATTR:code} - attribute value, {SAVE_PERCENT} - save percents, {SAVE_AMOUNT} - save amount, {PRICE} - price, {SPECIAL_PRICE} special price, {BR} - new line, {NEW_FOR} - how may days ago the product was added, {SKU} - product SKU, {STOCK_COUNT} - amount of products in stock'; 
        
        $fldProduct = $form->addFieldset('product_page', array('legend'=> $hlp->__('Product Page')));
        $fldProduct->addField('prod_txt', 'text', array(
            'label'     => $hlp->__('Text'),
            'name'      => 'prod_txt',
            'note'      => $hlp->__($note),
        ));         
        $fldProduct->addField('prod_img', 'file', array(
            'label'     => $hlp->__('Image'),
            'name'      => 'prod_img',
            'after_element_html' => $this->getImageHtml('prod_img', $model->getProdImg()),
        )); 
        $fldProduct->addField('prod_pos', 'select', array(
            'label'     => $hlp->__('Position'),
            'name'      => 'prod_pos',
            'values'    => $model->getAvailablePositions(),
        ));  
        $fldProduct->addField('prod_style', 'text', array(
            'label'     => $hlp->__('Styles'),
            'name'      => 'prod_style',
        )); 
        
        $fldProduct->addField('prod_text_style', 'text', array(
            'label'     => $hlp->__('Text Style'),
            'name'      => 'prod_text_style',
        	'note'      => $hlp->__('Example: color:red;font-size:12px;'),
        )); 
        
        $fldCat = $form->addFieldset('category_page', array('legend'=> $hlp->__('Category Page')));
        $fldCat->addField('cat_txt', 'text', array(
            'label'     => $hlp->__('Text'),
            'name'      => 'cat_txt',
            'note'      => $hlp->__($note),
        ));         
        $fldCat->addField('cat_img', 'file', array(
            'label'     => $hlp->__('Image'),
            'name'      => 'cat_img',
            'after_element_html' => $this->getImageHtml('cat_img', $model->getCatImg()),
        ));
        $fldCat->addField('cat_pos', 'select', array(
            'label'     => $hlp->__('Position'),
            'name'      => 'cat_pos',
            'values'    => $model->getAvailablePositions(),
        ));  
        $fldCat->addField('cat_style', 'text', array(
            'label'     => $hlp->__('Styles'),
            'name'      => 'cat_style',
        ));  
        
		$fldCat->addField('cat_text_style', 'text', array(
            'label'     => $hlp->__('Text Style'),
            'name'      => 'cat_text_style',
			'note'      => $hlp->__('Example: color:red;font-size:12px;'),
        )); 
        
        //set form values
        $form->setValues($model->getData()); 
        
        return parent::_prepareForm();
    }
    
    protected function getImageHtml($field, $img)
    {
        $html = '';
        if ($img) {
            $html .= '<p style="margin-top: 5px">';
            $html .= '<img src="' . Mage::getBaseUrl('media') . 'amlabel/' . $img . '" />';
            $html .= '<br/><input type="checkbox" value="1" name="remove_' . $field . '"/> ' . Mage::helper('amlabel')->__('Remove');
            $html .= '<input type="hidden" value="' . $img . '" name="old_' . $field . '"/>';
            $html .= '</p>';
        } 
        return $html;     
    }  
  
}