<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

class Simple_Forum_Block_Topic extends Mage_Core_Block_Template
{
	public  $limits                 = array(5, 10, 15);

	const PAGE_VAR_NAME             = 'p';
	const LIMIT_VAR_NAME            = 'limit';
	public function getViewUrl($id, $obj = false)
	{
		if($obj && $obj->getUrl_text() != '' && $obj->getUrl_text())
		{
			return $this->_getUrlrewrited( array(self::PAGE_VAR_NAME => 1), $obj->getUrl_text());
		}
		return $this->_getUrl( array(self::PAGE_VAR_NAME => 1), '/view/id/' . $id);
	}

	private function _getUrlrewrited($params, $urlAddon = '')
	{
		$urlParams = array();
        $urlParams['_current']  = false;
        $urlParams['_escape']   = false;
        $urlParams['_use_rewrite']   = false;
        $urlParams['_query']    = $params;
        return $this->getUrl( $urlAddon, $urlParams);
	}

	private function _getUrl($params, $urlAddon = '')
	{
		$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        //return $this->getUrl('*//*//' . $urlAddon, $urlParams);
	}

}

?>