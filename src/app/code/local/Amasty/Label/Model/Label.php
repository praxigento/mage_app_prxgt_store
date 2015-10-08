<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Label
*/
class Amasty_Label_Model_Label extends Mage_Core_Model_Abstract
{
    protected $_info = array();
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('amlabel/label');
    }
    
    public function init($p, $mode, $parent = null)
    {
        $this->setProduct($p);        
        if (Mage::registry('current_product')) {
        	$this->setMode('prod');
        } else {
        	$this->setMode('cat');
        }   
        
        $regularPrice = $p->getPrice();
        $specialPrice = $p->getFinalPrice();        
        
        if ($p->getTypeId() == 'bundle'){              
            list($specialPrice, $maxPrice) = $p->getPriceModel()->getPrices($p);
            $regularPrice = $specialPrice;
            
            $price = $p->getData('special_price');
            if (!is_null($price) && $price < 100){
                $regularPrice = ($specialPrice / $price) * 100;
            } 
        }
        
        if ($parent && ($parent->getTypeId() == 'grouped')) {
            $usedProds = Mage::helper('amlabel')->getUsedProducts($parent);
            foreach ($usedProds as $child) {
                if ($child != $p) {
                    $regularPrice += $child->getPrice();
                    $specialPrice += $child->getFinalPrice();
                }
            }
        }
        
        $this->_info['price']         = $regularPrice;
        $this->_info['special_price'] = $specialPrice;
    }    
    
    public function getAvailablePositions($asText = true)
    {
        $a = array();
        foreach (array('top', 'middle', 'bottom') as $first){
            foreach (array('left', 'center', 'right') as $second){
                $a[] = $asText ? 
                    Mage::helper('amlabel')->__(ucwords($first . ' ' . $second)) 
                    : 
                    $first . '-' . $second;
            }
        }  
        return $a;     
    }    

    public function isApplicable()
    {
        $p = $this->getProduct();
        
        if (!$p){
            return false;
        }
        
        // has image for the current mode
        if (!$this->getValue('img'))
            return false;
            

        /*
         * Check Customer Group
         */     
        $groups = $this->getCustomerGroup();
        if ($groups) {
        	$groups = explode(',', $groups);
        	if (count($groups) > 0) {
        		$currentGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        		$hasGroup = false;
        		foreach ($groups as $key => $groupId) {
        			if ($groupId != '' && intval($currentGroupId) === intval($groupId)) {
        				$hasGroup = true;
        			}
        		}
        		if (!$hasGroup) {
        			return false;
        		}
        	}
        }
		
        
        // individula products logic
        $inArray = in_array($p->getSku(), explode(',', $this->getIncludeSku())); 
        if (!$p->getSku())
           $inArray = false;
           
        $now = Mage::getModel('core/date')->date();
        if ($this->getDateRangeEnabled() && ($now < $this->getFromDate() || $now > $this->getToDate())) {
        	return false;
        }
        
        if ($this->getPriceRangeEnabled() && ($p->getPrice() < $this->getFromPrice() || $p->getPrice() > $this->getToPrice())) {
            return false;
        }
        
        // include skus
        if (0 ==  $this->getIncludeType() && $inArray)
            return true;
        // exclude skus    
        if (1 ==  $this->getIncludeType() && $inArray)
             return false;            
        // use for skus only    
        if (2 ==  $this->getIncludeType())
             return $inArray;

        $attrCode = $this->getAttrCode();
        if ($attrCode){
            $v = $p->getData($attrCode);
            if (preg_match('/^[0-9,]+$/', $v)){
                if (!in_array($this->getAttrValue(), explode(',', $v))){
                    return false;                 
                }
            }
            elseif ($v != $this->getAttrValue()){
                return false; 
            }
        } 
        
        $catId = $this->getCategory(); 
        if ($catId){
            $ids = $p->getCategoryIds();    
            if (!is_array($ids))
                return false;
            if (!in_array($catId, $ids))
                return false;
        }     
        
        
        
//        $qty = $this->getStockLess();
//        if ($qty){
            // skip complicated types in the first version
//            if ($p->isConfigurable()){
//                return false;
//            }
//                
//            if (!$p->getStockItem()->checkQty($qty)){
//                return false;    
//            }
//        } 
        
        $stockStatus = $this->getStockStatus();
        if ($stockStatus){
            $inStock = $p->getStockItem()->getIsInStock() ? 2 : 1;
            if ($inStock != $stockStatus)
                return false;
                
            if ($inStock == 2) {
	            $qty = $this->getProduct()->getStockItem()->getQty();
	            if ($this->getStockLess() > 0 && $qty >= $this->getStockLess()) {
	            	return false;
	            }
            }
        }
        
        if ($this->getIsNew()){
            $isNew = $this->_isNew($p) ? 2 : 1;
            if ($this->getIsNew() != $isNew)
                return false;
        }
        
        if ($this->getIsSale()){
            $isSale = $this->_isSale() ? 2 : 1;
            if ($this->getIsSale() != $isSale)
                return false;
        }
        
        // finally ...    
        return true;
    }
    
    protected function _isNew($p)
    {
        $fromDate = $p->getNewsFromDate();
        $toDate   = $p->getNewsToDate();
        
        if (!$fromDate && !$toDate){
            $days = Mage::getStoreConfig('amlabel/new/days');
            if (!$days)
                return false;
            $createdAt = strtotime($p->getCreatedAt());
            return (time() - $createdAt <=  $days*86400);            
        }
        
        if ($fromDate && time() < strtotime($fromDate))
            return false;        
            
        if ($toDate && time() > strtotime($toDate))
            return false;
            
        return true;    
    }
    
    protected function _isSale()
    {
        if ($this->_info['price'] <= 0)
            return false;
            
        if ($this->_info['price'] - $this->_info['special_price'] < 0.001 )
            return false; 
            
        $min = Mage::getStoreConfig('amlabel/general/sale_min');
        if ($min && $this->_info['price'] - $this->_info['special_price'] < $min)
            return false; 
            
        return true;
    }    
    
    public function getImageUrl()
    {
        return Mage::getBaseUrl('media') . 'amlabel/' . $this->getValue('img');
    } 
    
    public function getImagePath()
    {
        return Mage::getBaseDir('media') . '/amlabel/' . $this->getValue('img');
    }   
    
    public function getImageInfo()
    {
        $info = getimagesize($this->getImagePath());
        return array('w'=>$info[0], 'h'=>$info[1]);
    }  

    public function getCssClass()
    {
        $all = $this->getAvailablePositions(false);
        return $all[$this->getValue('pos')];
    }  

    public function getStyle()
    {
        return $this->getValue('style');
    }  

    public function getText()
    {
        $txt = $this->getValue('txt');
        
        $vars = array();
        preg_match_all('/{([a-zA-Z:\_0-9]+)}/', $txt, $vars);
        if (!$vars[1]){
            return $txt;    
        }
        $vars = $vars[1];
        
        foreach ($vars as $var){
            $value = '';
            switch ($var){
                case 'PRICE': 
                    $value = strip_tags(Mage::app()->getStore()
                        ->convertPrice($this->_info['price'], true));    
                break;
                case 'SPECIAL_PRICE': 
                    $value = strip_tags(Mage::app()->getStore()
                        ->convertPrice($this->_info['special_price'], true));       
                break;
                case 'SAVE_AMOUNT': 
                    $value = strip_tags(Mage::app()->getStore()
                        ->convertPrice($this->_info['price'] - $this->_info['special_price'], true));    
                break;
                case 'SAVE_PERCENT': 
                    $value = $this->_info['price'] - $this->_info['special_price'];
                    $value = ceil($value * 100 / $this->_info['price']);   
                break;
                
                case 'BR': 
                    $value = '<br/>';
                break;  

                case 'SKU': 
                    $value = $this->getProduct()->getSku();
                break; 
                
                case 'NEW_FOR': 
                    $createdAt = strtotime($this->getProduct()->getCreatedAt());
                    $value = max(1, floor((time() - $createdAt) / 86400));                  
                break;
                
                case 'STOCK_COUNT':
                	$value = intval($this->getProduct()->getStockItem()->getQty());
                break;
                
                default:
                    $str = 'ATTR:';
                    if (substr($var,0 , strlen($str))  == $str){
                        $code = trim(substr($var, strlen($str)));
                        $value = $this->getProduct()->getData($code);
                        if (is_numeric($value) && $this->getProduct()->getData($code . '_value')){
                            $value = $p->getData($code . '_value');
                        }                        
                    }
            }   
            $txt = str_replace('{' . $var . '}', $value, $txt);
        }
        
        return $txt;
    } 
    
    public function getValue($key)
    {
        return $this->_getData($this->getMode() . '_' . $key);    
    }  
    
    public function getJs()
    {
        $js = 'ondblclick="if(product_zoom)product_zoom.toggleFull.bind(product_zoom)();"';
        if ('cat' == $this->getMode()){
            $url = $this->getProduct()->getProductUrl();
            $js = 'onclick="window.location=\'' . $url . '\'"';            
        }
        return $js;
    }
    
}
