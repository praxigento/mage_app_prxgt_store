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
 
class Simple_Forum_Helper_Rss extends Mage_Core_Helper_Abstract
{
			
	public function __getUrl( $post )
	{
		return $this->_getUrl('forum/topic/viewreply', array('id'=>$post->getId(), '_current'=>false, '_escape'=>false, '_use_rewrite'=>false));
	}
	
	public function __getRSSUrl()
	{
		$object = Mage::registry('current_object');
		$query  = array(); 
		if($object)
		{
			$query = array('id'=>$object->getId()); 
		}	
		
		$path = '*/rss';
		return $this->_getUrl( $path, array( '_current'=>false, '_escape'=>false, '_use_rewrite'=>false, '_query'=>$query));
	}
}