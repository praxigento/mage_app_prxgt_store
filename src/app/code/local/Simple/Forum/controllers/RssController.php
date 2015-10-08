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

class Simple_Forum_RssController extends Mage_Core_Controller_Front_Action
{
	public function indexAction() 
	{
		$store_id = Mage::app()->getStore()->getid();
		Mage::register('store_id', $store_id);
		if (Mage::getStoreConfig('forum/forumconfiguration/allowrssfeeds')) 
		{
			$this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
			$id = (int) $this->getRequest()->getParam('id', 0);
			Mage::register('rssParent', $id);
			$this->loadLayout(false);
			$this->renderLayout();
		}
		else 
		{
			$this->_redirect('*/');
		}
	}
}

?>