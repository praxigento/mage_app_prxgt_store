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
 
class Simple_Forum_Helper_Sitemap extends Mage_Core_Helper_Abstract
{
	
	const PAGE_VAR_NAME             = 'p';
	const SORT_VAR_NAME             = 'sort';
	const LIMIT_VAR_NAME            = 'limit';
	const STORE_VAR_NAME            = '___store';
		
	public function __getUrl($url_begin, $store_code, $o, $limit, $page)
	{
		return $url_begin . $o->getUrl_text() . '?'  . self::STORE_VAR_NAME . '=' . $store_code . '&' . self::PAGE_VAR_NAME . '=' . $page . '&' . self::LIMIT_VAR_NAME . '=' . $limit . '&' . self::SORT_VAR_NAME . '=1'; 
	}
}