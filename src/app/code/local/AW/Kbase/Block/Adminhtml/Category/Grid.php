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


class AW_Kbase_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('kbaseCategoryGrid');
        $this->setDefaultSort('category_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('kbase/category')->getCollection();
        $collection->getSelect()
                ->joinLeft(array('ca' => $collection->getTable('kbase/category_article')), 'main_table.category_id=ca.category_id', array(
                    'article_count' => new Zend_Db_Expr('count(ca.article_id)'),
                        )
                )
                ->group('main_table.category_id');

        $collection->addStoreIds();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('category_name', array(
            'index' => 'category_name',
            'header' => $this->__('Title'),
        ));

        $this->addColumn('article_count', array(
            'index' => 'article_count',
            'type' => 'number',
            'header' => $this->__('Articles'),
            'width' => '100px',
            'align' => 'right',
            'filter_condition_callback' => array($this, '_filterArticleCount'),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_ids', array(
                'header' => $this->__('Store views'),
                'index' => 'store_ids',
                'type' => 'store',
                'store_all' => true,
                'store_view' => true,
                'sortable' => false,
                'filter_condition_callback' => array($this, '_filterStore'),
            ));
        }

        $this->addColumn('category_url_key', array(
            'index' => 'category_url_key',
            'header' => $this->__('URL key'),
        ));

        $this->addColumn('category_order', array(
            'index' => 'category_order',
            'type' => 'number',
            'header' => $this->__('Order'),
            'width' => '100px',
            'align' => 'right',
        ));

        $this->addColumn('category_status', array(
            'index' => 'category_status',
            'type' => 'options',
            'header' => $this->__('Status'),
            'align' => 'left',
            'width' => '100px',
            'options' => AW_Kbase_Model_Source_Status::toShortOptionArray(),
        ));

        $this->addColumn('action', array(
            'header' => $this->__('Action'),
            'width' => '80px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $this->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'category_id',
            'is_system' => true,
        ));

        // $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        // $this->addExportType('*/*/exportXml', $this->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('main_table.category_id');
        $this->getMassactionBlock()->setFormFieldName('category_ids');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => $this->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => $this->__('Are you sure?')
        ));

        $statuses = AW_Kbase_Model_Source_Status::toOptionArray();
        array_unshift($statuses, array('label' => '', 'value' => ''));

        $this->getMassactionBlock()->addItem('status', array(
            'label' => $this->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => $this->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _filterStore($collection, $column) {

        $val = $column->getFilter()->getValue();

        if (!$val) {
            return $this;
        } else {
            $cond = "FIND_IN_SET('$val', {$column->getIndex()}) OR FIND_IN_SET('0', {$column->getIndex()})";
        }

        $collection->getSelect()->having($cond);
    }

    /*
     * Article count filter callback
     * @see Mage_Adminhtml_Block_Widget_Grid::_addColumnFilterToCollection()
     */

    protected function _filterArticleCount($collection, $column) {
        if (!$value = $column->getFilter()->getValue())
            return;

        if (isset($value['from']) && isset($value['to']))
            $collection->getSelect()->having('article_count BETWEEN \'' .
                    addslashes($value['from']) . '\' AND \'' . addslashes($value['to']) . '\'');
        elseif (isset($value['from']))
            $collection->getSelect()->having('article_count >=\'' . addslashes($value['from']) . '\'');
        elseif (isset($value['to']))
            $collection->getSelect()->having('article_count <=\'' . addslashes($value['to']) . '\'');
    }

}