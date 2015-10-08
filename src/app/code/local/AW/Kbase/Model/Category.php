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
 * Category model
 */
class AW_Kbase_Model_Category extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('kbase/category');
    }

    /*
     * Checks whether there is a category with the same URL key among the stores the category belongs to
     * @return bool
     */
    public function isUrlKeyUsed()
    {
        $storeIds = $this->getCategoryStoreIds();
        if(!is_array($storeIds)) $storeIds = explode(',', $storeIds);

        $sameUrlCategoryStoreIds = $this->getResource()->getSameUrlCategoryStoreIds($this->getId(), $this->getCategoryUrlKey());

        $res = array_intersect($storeIds, $sameUrlCategoryStoreIds);
        return !empty($res);
    }

    protected function _afterLoad()
    {
        if(is_null($storeIds = $this->getCategoryStoreIds()))
            $this->setCategoryStoreIds($this->getResource()->getStoreIds($this->getId()));
        elseif(!is_array($storeIds))
            $this->setCategoryStoreIds(array_unique(explode(',', $storeIds)));

        return parent::_afterLoad();
    }

    public function afterLoad()
    {
        $this->_afterLoad();
    }

    protected function _afterSave()
    {
        $this->getResource()->saveStoreIds($this->getId(), $this->getCategoryStoreIds());
        return parent::_afterSave();
    }

    /*
     * Loads itself using the URL key parameter
     * @param string $urlKey URL key used to identify the category
     */
    public function loadByUrlKey($urlKey)
    {
        $id = $this->getResource()->getIdByUrlKey($urlKey);
        return $this->load($id);
    }

}