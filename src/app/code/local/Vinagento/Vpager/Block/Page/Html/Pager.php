<?php
class Vinagento_Vpager_Block_Page_Html_Pager extends Mage_Page_Block_Html_Pager {
	public function getPageUrl($page) {
		return str_replace('?', '#%21', parent::getPageUrl($page));
	}
		public function getPage2Url($page) {
		return str_replace('?', '?', parent::getPage2Url($page));
	}
	
}