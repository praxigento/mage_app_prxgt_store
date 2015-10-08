<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Label
*/
class Amasty_Label_Model_Observer
{
    public function applyLabels($observer) 
    {
        if (!Mage::app()->getRequest()->getParam('amlabels'))
            return $this;         
        
        $product = $observer->getEvent()->getProduct(); 
  
        $collection = Mage::getModel('amlabel/label')->getCollection()
            ->addFieldToFilter('include_type', array('neq'=>1));
         
        foreach ($collection as $label){
            $skus = trim($label->getIncludeSku(),', ');
            if ($skus)
                $skus = explode(',', $skus);
            else
                $skus = array();
            
            $name = 'amlabel_' . $label->getId();
            if (Mage::app()->getRequest()->getParam($name)){ //add
                if (!in_array($product->getSku(), $skus)){
                    $skus[] = $product->getSku();
                    $label->setIncludeSku(implode(',', $skus));
                    $label->save();                    
                }
            }
            else { //remove
                $k = array_search($product->getSku(), $skus);
                if ($k !== false){
                    $skus[$k] = null;
                    unset($skus[$k]);
                    $label->setIncludeSku(trim(implode(',', $skus), ' ,'));
                    $label->save();                    
                }
            }
        }        
        
        return $this;           
    }
}