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
 
class Simple_Forum_Helper_Post extends Mage_Core_Helper_Abstract
{
	
	private $search_block_begin = '<span class="forum-search-block-selected">';
	private $search_block_end   = '</span>';
	
	public function preparePostBySearchValue($_post)
	{
		$search_val  = Mage::getSingleton('forum/session')->getSearchValue();
		$pattern     = '#(?!<.*)(?<!\w)(\w*)(' . $search_val . ')(\w*)(?!\w|[^<>]*(?:</s(?:cript|tyle))?>)#is';
		return preg_replace($pattern, '$1' . $this->search_block_begin . '$2' . $this->search_block_end . '$3', $_post);
	}
}