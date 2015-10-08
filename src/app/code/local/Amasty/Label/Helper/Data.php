<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
*/
class Amasty_Label_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_labels = null;
    protected $_sizes = array();
    
    public function getAllGroups()
    {
        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array('value' => 0, 'label'=>Mage::helper('salesrule')->__('NOT LOGGED IN')));
        } 
        
        return $customerGroups;
    } 
    
    public function getLabels($product, $mode='category')
    {
        $html = '';
        
        $applied = false;
        foreach ($this->_getCollection() as $label) {
            if ($label->getIsSingle() && $applied) {
                continue;
            }
            $label->init($product, $mode);
            if ($label->isApplicable()) {
                $applied = true;
                $html .= $this->_generateHtml($label);   
            } elseif ($label->getUseForParent() && ($product->isConfigurable() || $product->isGrouped())) {
                $usedProds = $this->getUsedProducts($product);
                foreach ($usedProds as $child) {
                    $label->init($child, $mode, $product);
                    if ($label->isApplicable()) {
                        $applied = true;
                        $html .= $this->_generateHtml($label);   
                    }
                }
            }
        }
        return $html;
    }
    
    public function getUsedProducts($product)
    {
        if ($product->isConfigurable()) {
            return $product->getTypeInstance(true)->getUsedProducts(null, $product);
        } else { // product is grouped
            return $product->getTypeInstance(true)->getAssociatedProducts($product);
        }
    }
        
    protected function _generateHtml($label)
    {    
        $imgUrl = $label->getImageUrl();
        
        if (empty($this->_sizes[$imgUrl])){
            $this->_sizes[$imgUrl] = $label->getImageInfo();    
        }
        
        $tableClass = $label->getCssClass();
        
        $tableStyle = '';
        $tableStyle .= 'height:' . $this->_sizes[$imgUrl]['h'] . 'px; ';
        $tableStyle .= 'width:'  . $this->_sizes[$imgUrl]['w'] . 'px; ';

        $customStyle = $label->getStyle();
        if ($customStyle){
            $tableStyle .= $customStyle;
        }
        else { //adjust image position for middle cases
            $tableStyle .= $this->_getPositionAdjustment($tableClass, $this->_sizes[$imgUrl]);    
        }
        
        if ($label->getMode() == 'cat') {
        	$textStyle = $label->getCatTextStyle();
        } else {
        	$textStyle = $label->getProdTextStyle();
        }
        
        if ($textStyle) {
        	$textStyle = 'style="' . $textStyle . '"';
        }
        
        $html =  '<table ' . $label->getJs() .' class="amlabel-table ' . $tableClass . '" style ="' . $tableStyle . '">';
        $html .= '<tr>';
        $html .= '<td style="background: url(' . $imgUrl .') no-repeat 0 0">';
        $html .= '<span class="amlabel-txt" ' . $textStyle . '>' . $label->getText() . '</span>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        
        return $html;        
    }
    
    protected function _getPositionAdjustment($tableClass, $sizes)
    {
        $style = '';
        
        if ('top-center' == $tableClass){
             $style .= 'margin-left:' . (-$sizes['w'] / 2) . 'px;'; 
        }
        elseif (false !== strpos($tableClass, 'center')){
            $style .= 'margin-right:' . (-$sizes['w'] / 2) . 'px;';            
        }
        if (false !== strpos($tableClass, 'middle')){
            $style .= 'margin-top:'. (-$sizes['h'] / 2) .'px;';            
        }        

        return $style;
    }  
    
    protected function _getCollection()
    {
        if (is_null($this->_labels)){
            $id = Mage::app()->getStore()->getId();
            $this->_labels = Mage::getModel('amlabel/label')->getCollection()
                ->addFieldToFilter('stores', array('like' => "%,$id,%"))
                ->setOrder('pos', 'asc')
                ->load();
        }
        return $this->_labels;
    }      
}