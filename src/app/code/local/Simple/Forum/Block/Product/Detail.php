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

class Simple_Forum_Block_Product_Detail extends Mage_Core_Block_Template
{	
	
	const PAGE_VAR_NAME             = 'p';
	const LIMIT_VAR_NAME            = 'limit';
	const SB                        = 'sb';
	
	private $_collection;
	private $_product_id;
	
	private $order_type = 'desc';
	private $order_by   = 'created_time';
	
	protected function _prepareLayout()
    {
    	if(!$this->getIsDisplayed())
    	{
			return;
		}
		$this->setTemplate('forum/product/detail.phtml');
        parent::_prepareLayout();
    }
    
    public function getCollection()
    {
		if($this->_collection)
		{
			return $this->_collection;
		}
		$this->initCollection();
		return $this->_collection;
	}
    
    public function getButtonName()
    {
		return Mage::helper('forum/topic')->__('Add New Topic');
	}
	
	public function getAddNewUrl()
	{
		return $this->getUrl('forum/topic/new/product_id/' . $this->getProductId(), array('_query'=>array(self::SB => urlencode(Mage::helper('core/url')->getCurrentUrl()))));	
	}
	
	public function getTotalData()
	{
		$c = Mage::getModel('forum/post')->getCollection();	
		$c->getSelect()->where('product_id=?', $this->getProductId());
	}
	
	public function getViewUrl($id, $obj = false)
	{
		if($obj && $obj->getUrl_text() != '' && $obj->getUrl_text())
		{
			return $this->_getUrlrewrited( array(self::PAGE_VAR_NAME => 1, self::SB =>  urlencode(Mage::helper('core/url')->getCurrentUrl())), $obj->getUrl_text());	
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
    
    private function initCollection()
    {
    	$store = Mage::app()->getStore();
    	
		$c = Mage::getModel('forum/topic')->getCollection();
		$c->setOrder($this->order_by, $this->order_type);
		$c->setPageSize($this->getDisplayTopicsQuantity());
		$c->addStoreFilterToCollection($store->getId());
		$c->getSelect()->where('main_table.product_id=?', $this->getProductId())->where('status=?', 1);
		
		$this->_collection = $c;
		$this->setAdditionalData();
	}
	
	private function setAdditionalData()
	{
		if($this->_collection)
		{
			foreach($this->_collection as $key=>$val)
			{
				$this->_collection->getItemById($key)->setTotal_posts($this->getTotalPosts($val->getId()));		
			}
		}
	}
	
	private function getTotalPosts($id)
	{
		$c = Mage::getModel('forum/post')->getCollection();
		$c->getSelect()->where('parent_id=?', $id);
		return $c->getSize();
	}
	
	private function getTotalTopics()
	{
		$store = Mage::app()->getStore();
		$c = Mage::getModel('forum/topic');
		$c->addStoreFilterToCollection($store->getId());
		$c->getSelect()->where('main_table.product_id=?', $this->getProductId());
	}
	
	private function getProductId()
	{
		if($this->_product_id)
		{
			return $this->_product_id;
		}
		$this->_product_id = Mage::registry('product')->getId();
		return $this->_product_id;
	}
    
    private function getIsDisplayed()
    {
		return Mage::getStoreConfig('forum/advanced_settings/display_on_product_details');
	}
	
	private function getDisplayTopicsQuantity()
	{
		return Mage::getStoreConfig('forum/advanced_settings/display_topics_quantity');
	}
	
}