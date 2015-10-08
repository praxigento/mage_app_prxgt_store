<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Label
*/ 
class Amasty_Label_Block_Adminhtml_Label_Edit_Tab_Condition extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Label_Helper_Data */
        $hlp = Mage::helper('amlabel');
        
        $fldProd = $form->addFieldset('products', array('legend'=> $hlp->__('Individual Products')));
        $fldProd->addField('include_type', 'select', array(
            'label'     => $hlp->__('Apply label to'),
            'name'      => 'include_type',
            'values'    => array(
                0 => $hlp->__('All matching products and SKUs listed below'), 
                1 => $hlp->__('All matching products except SKUs listed below'), 
                2 => $hlp->__('SKUs listed below only'), 
             ),              
        ));         
        $fldProd->addField('include_sku', 'text', array(
            'label'     => $hlp->__('SKUs'),
            'name'      => 'include_sku',
            'note'      => $hlp->__('Comma separated SKUs, no spaces. Please make sure the SKU attribute properties `Visible on Product View Page on Front-end`, `Used in Product Listing` are set to `Yes`'),
        ));        
        
        $fldState = $form->addFieldset('state', array('legend'=> $hlp->__('State')));
        $fldState->addField('is_new', 'select', array(
            'label'     => $hlp->__('Is New'),
            'name'      => 'is_new',
            'values'    => array(
                0 => $hlp->__('Does not matter'), 
                1 => $hlp->__('No'), 
                2 => $hlp->__('Yes'), 
             ),
        ));
        $fldState->addField('is_sale', 'select', array(
            'label'     => $hlp->__('Is on Sale'),
            'name'      => 'is_sale',
            'values'    => array(
                0 => $hlp->__('Does not matter'), 
                1 => $hlp->__('No'), 
                2 => $hlp->__('Yes'), 
             ),
        ));   

        $fldAttr = $form->addFieldset('attr', array('legend'=> $hlp->__('Category & Attributes')));
        $fldAttr->addField('category', 'select', array(
            'label'     => $hlp->__('Category is'),
            'name'      => 'category',
            'values'    => $this->getTree(),
        ));        
        $fldAttr->addField('attr_code', 'select', array(
            'label'     => $hlp->__('Has attribute'),
            'name'      => 'attr_code',
            'values'    => $this->getAttributes(),
            'onchange'  => 'showOptions(this)',
            'note'      => $hlp->__('If you do not see the label, please make sure the attribute properties `Visible on Product View Page on Front-end`, `Used in Product Listing` are set to `Yes`'),
        ));
        
        $attributeCode = Mage::registry('amlabel_label')->getData('attr_code');
        if ('' != $attributeCode) {
            $attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($attributeCode);
            if ('select' === $attribute->getFrontendInput() || 'multiselect' === $attribute->getFrontendInput()) {
                $options = $attribute->getFrontend()->getSelectOptions();
                $fldAttr->addField('attr_value', 'select', array(
                  'label'     => $hlp->__('Attribute value is'),
                  'name'      => 'attr_value',
                  'values'    => $options,
                ));
            } else {
                $fldAttr->addField('attr_value', 'text', array(
                  'label'     => $hlp->__('Attribute value is'),
                  'name'      => 'attr_value',
                ));
            }
        } else {
            $fldAttr->addField('attr_value', 'text', array(
                'label'     => $hlp->__('Attribute value is'),
                'name'      => 'attr_value',
            ));
        }
               
        $fldStock = $form->addFieldset('stock', array('legend'=> $hlp->__('Stock')));
        $fldStock->addField('stock_status', 'select', array(
            'label'     => $hlp->__('Status'),
            'name'      => 'stock_status',
            'values'    => array(
                0 => $hlp->__('Does not matter'), 
                1 => $hlp->__('Out of Stock'), 
                2 => $hlp->__('In Stock'), 
             ),
        ));
        
        $fldStock->addField('stock_less', 'text', array(
        	'label'     => $hlp->__('Stock Amount Less Than'),
            'name'      => 'stock_less',
        	'note'      => $hlp->__('Works only when Stock Status field above is set to `In Stock`'),
		));
             
		$fldDateRange = $form->addFieldset('timeline', array('legend'=> $hlp->__('Date Range')));
        $fldDateRange->addField('date_range_enabled', 'select', array(
            'label'     => $hlp->__('Use Date Range'),
            'title'     => $hlp->__('Use Date Range'),
            'name'      => 'date_range_enabled',
            'options'   => array(           
                '0' => $hlp->__('No'),
             	'1' => $hlp->__('Yes'),
            ),
        ));
        
         $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        
        $fldDateRange->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => $hlp->__('From Date'),
            'title'  => $hlp->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' =>  Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
        ));
        
        $fldDateRange->addField('from_time', 'text', array(
            'name'   => 'from_time',
            'label'  => $hlp->__('From Time'),
            'title'  => $hlp->__('From Time'),
        	'note'      => $hlp->__('In format 15:32'),
        ));
        
        $fldDateRange->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => $hlp->__('To Date'),
            'title'  => $hlp->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' =>  Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
		$fldDateRange->addField('to_time', 'text', array(
            'name'   => 'to_time',
            'label'  => $hlp->__('To Time'),
            'title'  => $hlp->__('To Time'),
        	'note'      => $hlp->__('In format 19:32'),
        )); 

        
        $fldPriceRange = $form->addFieldset('price', array('legend'=> $hlp->__('Price Range')));
        $fldPriceRange->addField('price_range_enabled', 'select', array(
            'label'     => $hlp->__('Use Price Range'),
            'title'     => $hlp->__('Use Price Range'),
            'name'      => 'price_range_enabled',
            'options'   => array(           
                '0' => $hlp->__('No'),
                '1' => $hlp->__('Yes'),
            ),
        ));
        
        
        $fldPriceRange->addField('from_price', 'text', array(
            'name'   => 'from_price',
            'label'  => $hlp->__('From Price'),
            'title'  => $hlp->__('From Price'),
        ));
        $fldPriceRange->addField('to_price', 'text', array(
            'name'   => 'to_price',
            'label'  => $hlp->__('To Price'),
            'title'  => $hlp->__('To Price'),
        ));
       
        
        
        $data = Mage::registry('amlabel_label')->getData();
        $data['is_active'] = '1';
        
        $dateFrom = explode(" ", $data['from_date']);
        $dateTo = explode(" ", $data['to_date']);
        
        if ($dateFrom[1] != '00:00:00') {
	        $data['from_time'] = $dateFrom[1];
        }
        
        if ($dateTo[1] != '00:00:00') {
	        $data['to_time'] = $dateTo[1];
        } 
        //set form values
        $form->setValues($data); 
        
        return parent::_prepareForm();
    } 
    
    protected function getAttributes()
    {
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->setEntityTypeFilter(Mage::getResourceModel('catalog/product')->getTypeId())
        ;
            
        $options = array(''=>'');
		foreach ($collection as $attribute){
		    $label = $attribute->getFrontendLabel();
			if ($label){ // skip system attributes
			    $options[$attribute->getAttributeCode()] = $label;
			}
		}
		asort($options);
        
		return $options;
    }

    /**
     * Genarates tree of all categories
     *
     * @return array sorted list category_id=>title
     */
    protected function getTree()
    {
        $rootId = Mage::app()->getStore(0)->getRootCategoryId();         
        $tree = array();
        
        $collection = Mage::getModel('catalog/category')
            ->getCollection()->addNameToResult();
        
        $pos = array();
        foreach ($collection as $cat){
            $path = explode('/', $cat->getPath());
            if ((!$rootId || in_array($rootId, $path)) && $cat->getLevel()){
                $tree[$cat->getId()] = array(
                    'label' => str_repeat('--', $cat->getLevel()) . $cat->getName(), 
                    'value' => $cat->getId(),
                    'path'  => $path,
                );
            }
            $pos[$cat->getId()] = $cat->getPosition();
        }
        
        foreach ($tree as $catId => $cat){
            $order = array();
            foreach ($cat['path'] as $id){
            	if (isset($pos[$id])){
                	$order[] = $pos[$id];
            	}
            }
            $tree[$catId]['order'] = $order;
        }
        
        usort($tree, array($this, 'compare'));
        array_unshift($tree, array('value'=>'', 'label'=>''));
        
        return $tree;
    }
    
    /**
     * Compares category data. Must be public as used as a callback value
     *
     * @param array $a
     * @param array $b
     * @return int 0, 1 , or -1
     */
    public function compare($a, $b)
    {
        foreach ($a['path'] as $i => $id){
            if (!isset($b['path'][$i])){ 
                // B path is shorther then A, and values before were equal
                return 1;
            }
            if ($id != $b['path'][$i]){
                // compare category positions at the same level
                $p = isset($a['order'][$i]) ? $a['order'][$i] : 0;
                $p2 = isset($b['order'][$i]) ? $b['order'][$i] : 0;
                return ($p < $p2) ? -1 : 1;
            }
        }
        // B path is longer or equal then A, and values before were equal
        return ($a['value'] == $b['value']) ? 0 : -1;
    }           
       
    
}