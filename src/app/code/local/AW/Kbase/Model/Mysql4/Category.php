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


class AW_Kbase_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('kbase/category', 'category_id');
    }

    /*
     * Returns category_id => category_name associated array
     * @param int|empty $storeId The ID of the current store
     * @return array category_id => category_name
     */
    public static function getCategories() {
        $_res = array();
        $_catCollection = Mage::getModel('kbase/category')->getCollection();
        $_catCollection->getSelect()->order('category_name');
        foreach ($_catCollection as $category)
            $_res[$category->getData('category_id')] = $category->getData('category_name');
        return $_res;
    }

    public static function toOptionArray()
    {
        $res = array();

        foreach(self::getCategories() as $key => $value)
            $res[] = array( 'value' => $key,
                            'label' => $value);

        return $res;
    }

    /*
     * Returns IDs of the stores the category with the ID given belongs to
     * @param int $categoryId The ID of the category
     * @result array ID of the stores
     */
    public function getStoreIds($categoryId)
    {
        if(!$categoryId) return array();

        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from($this->getTable('kbase/category_store'), 'store_id')
            ->where('category_id=?', $categoryId);

        return $db->fetchCol($select);
    }

    /*
     * Saves category-to-store relation
     * @param int $categoryId The ID of the category
     * @param array $storeIds Store IDs to which the category belongs to
     */
    public function saveStoreIds($categoryId, $storeIds)
    {
        if(!is_array($storeIds))
            $storeIds = explode(',', $storeIds);

        $existing = $this->getStoreIds($categoryId);
        $common = array_intersect($existing, $storeIds);
        $deleted = array_diff($existing, $common);
        $new = array_diff($storeIds, $common);

        $db = $this->_getWriteAdapter();

        if(!empty($deleted))
            $db->delete($this->getTable('kbase/category_store'),
                'category_id='.$categoryId.' AND store_id IN ('.implode(',', $deleted).')');

        if(!empty($new))
        {
            $data = array();
            foreach($new as $storeId)
                $data[] = array($categoryId, $storeId);

            AW_Kbase_Helper_Data::insertArray($this->getTable('kbase/category_store'),
                array('category_id', 'store_id'),
                $data
            );
        }
        return $this;
    }

    /*
     * Returns the IDs of the stores that have categories with the same URL key as passed
     * @param int $categoryId The ID of current category, needed to exclude from result set
     * @param string $url URL key of the category
     * @return array The IDs of the stores
     */
    public function getSameUrlCategoryStoreIds($categoryId, $url)
    {
        if(!$url) return array();

        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from(array('c' => $this->getMainTable()),
                ''
                )
            ->joinInner(array('cs' => $this->getTable('kbase/category_store')),
                    'c.category_id=cs.category_id',
                    array('store_ids' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT cs.store_id)'))
                )
            ->where('c.category_url_key=?', $url)
            ->group('c.category_id');
        
        if($categoryId)
            $select
                ->where('c.category_id<>?', $categoryId);

        if($res = $db->fetchOne($select))
            return array_unique(explode(',', $res));
        else return array();
    }

    /*
     * Returns the ID of the category with the same URL key as passed
     * @param string $urlKey URL key
     * @result int The ID of the category
     */
    public function getIdByUrlKey($urlKey)
    {
        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from(array('c' => $this->getMainTable()),
                'category_id'
                )
            ->joinLeft(array('cs' => $this->getTable('kbase/category_store')),
                    'c.category_id=cs.category_id',
                    ''
                )
            ->where('c.category_url_key=?', $urlKey)
            ->where('cs.store_id=?', Mage::app()->getStore()->getId())
            ->limit(1);

        return $db->fetchOne($select);
    }

}