<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Kbase
 * @version    1.3.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


/*
 * Article list generated by search by category, tag, or phrase
 */
class AW_Kbase_Block_List_Container extends Mage_Core_Block_Template
{
    /*
     * @var Mage_Core_Model_Mysql4_Collection_Abstract Collection of items to display
     */
    protected $_collection = false;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('aw_kbase/list.phtml');
    }

    /*
     * Prepares page
     */
    protected function _preparePage()
    {
        $this
            ->addBreadcrumb(
                    $this->__('Home'),
                    Mage::getBaseUrl(),
                    $this->__('Go to Home Page')
                )
            ->addBreadcrumb(
                    $this->__(Mage::getStoreConfig('kbase/general/title')),
                    AW_Kbase_Helper_Url::getUrl(AW_Kbase_Helper_Url::URL_TYPE_MAIN),
                    $this->__('Go to Knowledge Base Home Page')
                );

        return $this;
    }

    /*
     * Prepares layout, should be called after block creation
     */
    public function preparePage()
    {
        $this->_preparePage();

        $category = $this->getCategory();

        $headBlock = $this->getLayout()->getBlock('head');
        $breadcrumbs = $this->getChild('kbase_breadcrumbs');

        if ($headBlock) {

            if(!$category){
                if ($breadcrumbs){
                    $headBlock->setTitle($breadcrumbs->getPageTitle());    
                }
                return $this;
            }
            
            $title = $category->getMetaTitle();
            if ($title) {
                $headBlock->setTitle($title);
            } elseif ($breadcrumbs) {
                $headBlock->setTitle($breadcrumbs->getPageTitle());
            }

            $keywords = $category->getMetaKeywords();
            if ($keywords) {
                $headBlock->setKeywords($keywords);
            }

            $description = $category->getMetaDescription();
            if ($description) {
                $headBlock->setDescription($description);
            }
        }
        
        return $this;
    }

    /*
     * Prepares collection
     */
    protected function _prepareCollection()
    {
        return $this;
    }

    protected function _toHtml()
    {
        if(!AW_Kbase_Helper_Data::getFrontendEnabled()) return '';

        $this->_prepareCollection();

        if($sorter = $this->getChild('kbase_sorter'))
        {
            list($sortOrder, $sortDir) = $sorter->getCurrentSorting();
            if($sortOrder)
                $this->_collection = $this->_collection->applySorting($sortOrder, $sortDir);
        }

        if($pager = $this->getChild('kbase_pager'))
            $this->_collection = $pager
                                    ->setCollection($this->_collection)
                                    ->getCollection();

        return parent::_toHtml();
    }

    /*
     * Adds page layout breadcrumbs
     * @see AW_Kbase_Block_General_Breadcrumbs::addItem()
     */
    public function addBreadcrumb($name, $url=false, $title='')
    {
        if($this->getChild('kbase_breadcrumbs'))
            $this->getChild('kbase_breadcrumbs')
                ->addItem($name, $url, $title);

        return $this;
    }

}
