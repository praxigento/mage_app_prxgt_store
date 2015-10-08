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


class AW_Kbase_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    const TABLE_ALIAS_STORE = 'cs';
    
    protected $_storesTableJoined = false;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('kbase/category');
    }
    
     protected function _afterLoad() {
       
        foreach($this->_items as $model) {
           
            $model->setData('store_ids', explode(',', $model->getData('store_ids')));
            
        }
        
        return parent::_afterLoad();
         
    }

    /*
     * Covers original bug in Varien_Data_Collection_Db
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();

        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);

        $countSelect->from('', 'COUNT(DISTINCT main_table.category_id)');

        return $countSelect;
    }

    /*
     * Covers original bug in Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->reset(Zend_Db_Select::HAVING);
        $idsSelect->from(null, 'main_table.'.$this->getResource()->getIdFieldName());
        return $this->getConnection()->fetchCol($idsSelect);
    }

    public function _joinStoresTable() {
        if(!$this->_storesTableJoined) {
            $this->getSelect()->joinLeft(array(self::TABLE_ALIAS_STORE => $this->getTable('kbase/category_store')),
                'main_table.category_id = '.self::TABLE_ALIAS_STORE.'.category_id',
                '');
            $this->getSelect()->group('main_table.category_id');
        }
        return $this;
    }
    
    public function addStoreIds() {
     
         $this->_joinStoresTable()->getSelect()->columns(array('store_ids' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT '. self::TABLE_ALIAS_STORE . '.store_id)')));
         
         return $this;
        
    }
     
    public function addStoreFilter($stores = array()) {
         
        $this->_joinStoresTable();
        $_stores = array(Mage::app()->getStore()->getId());
        if(is_string($stores)) $_stores = explode(',', $stores);
        if(is_array($stores)) $_stores = $stores;
        if(!in_array('0', $_stores))
            array_push($_stores, '0');
        if($_stores == array(0)) return $this;
        $_sqlString = '(';
        $i = 0;
        foreach($_stores as $_store) {
            $_sqlString .= sprintf(self::TABLE_ALIAS_STORE.'.store_id = %s', $this->getConnection()->quote($_store));
            if(++$i < count($_stores))
                $_sqlString .= ' OR ';
        }
        $_sqlString .= ')';
        $this->getSelect()->where($_sqlString);

        return $this;
    }
}
