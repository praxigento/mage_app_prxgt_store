<?php

class TM_AskIt_Model_Mysql4_AskIt_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_questionCountAnswersFilterFlag = false;
    protected $_addProductNameFlag = false;

     protected $_map = array('fields' => array(
        'product_name'   => 'cpev.value'
    ));

    public function _construct()
    {
        parent::_construct();
        $this->_init('askit/askit');
    }

    public function addStatusFilter($statuses)
    {
        if (!is_array($statuses)) {
            $statuses = array($statuses);
        }
        $this->getSelect()->where('main_table.status IN (?)', $statuses);
        return $this;
    }

    public function addProductFilter($productId)
    {
        $this->getSelect()->where('main_table.product_id=?', $productId);
        return $this;
    }

    public function addParentIdFilter($parentId)
    {
        $this->getSelect()->where('main_table.parent_id=?', $parentId);
        return $this;
    }

    public function addQuestionFilter()
    {
        $this->getSelect()->where('main_table.parent_id IS NULL');
        return $this;
    }

    public function addPrivateFilter()
    {
        $this->getSelect()->where('main_table.private = 0');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $this->getSelect()->orWhere(
                'main_table.private = 1 AND main_table.customer_id = ?',
                $customerId
            );
        }

        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        
        if ($this->_questionCountAnswersFilterFlag) {
            $this->_addQuestionCountAnswersFilter();
        }

        if ($this->_addProductNameFlag) {
            $this->_addProductName();
        }
        return $this;
    }
    
    public function addQuestionCountAnswersFilter($flag = true)
    {
        $this->_questionCountAnswersFilterFlag = $flag;
    }

    protected  function _addQuestionCountAnswersFilter()
    {
        $select = $this->getConnection()->select()
            ->from($this->getResource()->getMainTable(), 
                array('parent_id', 'count_answers' => 'COUNT(id)')
            )
            ->where('parent_id IS NOT NULL')
            ->group('parent_id')
            ;
        $data = array();
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $data[$row['parent_id']] = $row['count_answers'];
        }
        
        foreach ($this as $row) {
            $count = 0;
            if (isset($data[$row->getId()])) {
                $count = $data[$row->getId()];
            }
            $row->setData('count_answers', $count);
        }
        return $this;
    }

    public function addProductName($flag = true)
    {
        $this->_addProductNameFlag = $flag;
    }


    protected  function _addProductName()
    {
        $store  = Mage::app()->getStore(true);
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ;

        $collection->joinAttribute(
            'custom_name',
            'catalog_product/name',
            'entity_id',
            null,
            'inner',
            $store->getId()
        );
        $select = $collection->getSelect();
        $data = array();
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $data[$row['entity_id']] = $row['custom_name'];
        }
        foreach ($this as $row) {
            $id = $name = $row->getProductId();
            if (isset($data[$id])) {
                $name = $data[$id];
            }
//            $row->setData('product_id', $name);
            $row->setData('product_name', $name);
        }
        return $this;
    }

    public function addStoreFilter($storeId, $all = true)
    {
        $stores = array($storeId);
        if ($all) {
            $stores[] = 0;
        }
        $this->getSelect()->where('main_table.store_id IN (?)', $stores);
        return $this;
    }
}