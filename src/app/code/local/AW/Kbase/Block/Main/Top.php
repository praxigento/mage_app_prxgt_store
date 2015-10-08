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
 * Top articles block
 */
class AW_Kbase_Block_Main_Top extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        if(!AW_Kbase_Helper_Data::getFrontendEnabled()) return '';

        if(!Mage::getStoreConfig('kbase/top/enabled')) return '';
        if(!Mage::getStoreConfig('kbase/general/rating_enabled')) return '';

        $collection = Mage::getResourceModel('kbase/article_collection')
            ->addStatusFilter()
            ->setStoreFilter()
            ->applySorting(AW_Kbase_Model_Source_Sorting::BY_RATING, AW_Kbase_Model_Source_Sorting::SORT_DESC)
            ->limit(Mage::getStoreConfig('kbase/top/count'));

        $collection->getSelect()->distinct();

        $collection->load();

        $this->setArticles($collection);

        return parent::_toHtml();
    }

}
