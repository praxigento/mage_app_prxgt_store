<?php
class Vinagento_Vpager_Block_Catalog_Layer_State extends Mage_Catalog_Block_Layer_State{
	public function getClearUrl(){
		return str_replace('?', '#%21', parent::getClearUrl());
	}
}