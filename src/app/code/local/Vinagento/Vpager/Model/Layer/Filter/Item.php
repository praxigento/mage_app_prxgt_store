<?php
class Vinagento_Vpager_Model_Layer_Filter_Item 
extends Mage_Catalog_Model_Layer_Filter_Item{
	    public function getUrl2(){
    	return str_replace('?','?',parent::getUrl());
    }
    public function getRemoveUrl2(){
    	return str_replace('?','?1',parent::getRemoveUrl());
    }
}