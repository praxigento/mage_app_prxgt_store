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
 * Article tag cloud block
 */
class AW_Kbase_Block_Main_Tag extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        if(!AW_Kbase_Helper_Data::getFrontendEnabled()) return '';

        if(!Mage::getStoreConfig('kbase/tag/enabled')) return '';

        $autoSize = Mage::getStoreConfig('kbase/tag/auto_size');
        $tagMaxCount = AW_Kbase_Model_Mysql4_Article::getTagMaxCount();

        if(!$tagMaxCount) $autoSize = false;

        $this->setAutoSize($autoSize);
        $this->setTagMaxCount($tagMaxCount);

        $this->setTags(AW_Kbase_Model_Mysql4_Article::getAllTags($autoSize));

        return parent::_toHtml();
    }

}
