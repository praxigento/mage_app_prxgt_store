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
 * Categories and their articles block
 */
class AW_Kbase_Block_Main_Category extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        if(!AW_Kbase_Helper_Data::getFrontendEnabled()) return '';

        if(!Mage::getStoreConfig('kbase/category/enabled')) return '';

        $this->setCategories(Mage::getResourceModel('kbase/article')
            ->getCategoryWithArticleList(
                Mage::getStoreConfig('kbase/category/count')
            )
        );

        return parent::_toHtml();
    }

}
