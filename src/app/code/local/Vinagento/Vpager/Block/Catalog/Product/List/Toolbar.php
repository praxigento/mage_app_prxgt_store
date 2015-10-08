<?php
class Vinagento_Vpager_Block_Catalog_Product_List_Toolbar
  extends Mage_Catalog_Block_Product_List_Toolbar{
    public function getLimitUrl($limit)
    {
    	return str_replace('?', '#%21', parent::getLimitUrl($limit));
        
    }
	    public function getLimit2Url($limit)
    {
    	return str_replace('?', '?', parent::getLimit2Url($limit));
        
    }
    public function getModeUrl($mode)
    {
        return str_replace('?', '#%21', parent::getModeUrl($mode));
    }
	    public function getMod2eUrl($mode)
    {
        return str_replace('?', '?', parent::getMode2Url($mode));
    }
    public function getOrderUrl($order, $direction)
    {
        return str_replace('?', '#%21', parent::getOrderUrl($order, $direction));
    }
	    public function getOrder2Url($order, $direction)
    {
        return str_replace('?', '?', parent::getOrder2Url($order, $direction));
    }
  }
