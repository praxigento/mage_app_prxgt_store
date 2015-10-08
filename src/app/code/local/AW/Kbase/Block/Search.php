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
 * Search by phrase result block
 */
class AW_Kbase_Block_Search extends AW_Kbase_Block_List_Container
{
    protected function _construct()
    {
        parent::_construct();
    }

    protected function _prepareCollection()
    {
        $this->_collection = Mage::getResourceModel('kbase/article_collection')
            ->addSearchFilter($this->getQuery())
            ->addTags()
            ->addStatusFilter()
            ->setStoreFilter()                
            ->addStoreIds();
        
        $this->_collection->getSelect()->having("FIND_IN_SET(?,store_ids)", Mage::app()->getStore()->getId());
        $this->_collection->getSelect()->having('main_table.article_status = 1');
         
        return parent::_prepareCollection();
    }

    protected function _preparePage()
    {
        parent::_preparePage();

        $this
            ->setBlockType('search')
            ->setTitle($this->__('Search results for "%s"', $this->getQuery()))
            ->setRatingEnabled(Mage::getStoreConfig('kbase/general/rating_enabled'));

        $this->addBreadcrumb(
                $this->__('Search results'),
                false
            );
    }

}
