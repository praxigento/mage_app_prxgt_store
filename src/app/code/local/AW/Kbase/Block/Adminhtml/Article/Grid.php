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


class AW_Kbase_Block_Adminhtml_Article_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('kbaseArticleGrid');
        $this->setDefaultSort('article_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _toHtml()
    {
        return $this->getLayout()
            ->createBlock('adminhtml/store_switcher')
                ->setUseConfirm(false)
                ->toHtml()
            .parent::_toHtml();
    }

    protected function _prepareCollection()
    {
        $storeId = $this->getRequest()->getParam('store');
        if(is_null($storeId)) { $storeId = 0; }

        $collection = Mage::getModel('kbase/article')->getCollection()
            ->addAuthorName()
            ->addCategoryIds()
            ->addStoreIds();
        
            /* Deprecated */        
            //->setStoreFilter($storeId);
        
       if($storeId) {          
            $collection->getSelect()->having("FIND_IN_SET('{$storeId}',  store_ids) OR FIND_IN_SET('0',  store_ids)");
       }
        
        $this->setCollection($collection);
       
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('article_title', array(
            'index'     => 'article_title',
            'header'    => $this->__('Title'),
        ));

        $this->addColumn('article_author', array(
            'index'     => 'u_name',
            'header'    => $this->__('Author'),
            'filter_condition_callback' => array($this, '_filterAuthorCondition'),
        ));

        $categoryOptions = AW_Kbase_Model_Mysql4_Category::getCategories();
        $allOptions = array(0 => $this->__('No category'));

        foreach($categoryOptions as $key => $value)
            $allOptions[$key] = $value;

        $this->addColumn('categories', array(
            'index'     => 'category_ids',
            'type'      => 'options',
            'header'    => $this->__('Categories'),
            'renderer'  => 'AW_Kbase_Block_Adminhtml_Grid_Column_Multiselect',
            'options'   => $allOptions,
            'value_separator'   => ',',
            'line_separator'    => '<br>',
            'filter_condition_callback' => array($this, '_filterCategoryCondition'),
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_ids', array(
                'header' => $this->__('Store views'),
                'index' => 'store_ids',
                'type' => 'store',
                'filter' => false,
                'store_all' => true,
                'store_view' => true,
                'sortable' => false,
               // 'filter_condition_callback' => array($this, '_filterStore'),
            ));
        }

        $this->addColumn('article_rating', array(
            'index'     => 'article_rating',
            'type'      => 'number',
            'header'    => $this->__('Rating'),
            'width'     => '50px',
            'align'     => 'right',
            'renderer'  => 'AW_Kbase_Block_Adminhtml_Grid_Column_Rating',
        ));

        $this->addColumn('article_rating_votes', array(
            'index'     => 'article_rating_votes',
            'type'      => 'number',
            'header'    => $this->__('Votes'),
            'width'     => '50px',
            'align'     => 'right',
        ));

        $this->addColumn('article_status', array(
            'index'     => 'article_status',
            'type'      => 'options',
            'header'    => $this->__('Status'),
            'width'     => '80px',
            'options'   => AW_Kbase_Model_Source_Status::toShortOptionArray(),
        ));

        $this->addColumn('article_date_created', array(
            'index'     => 'article_date_created',
            'header'    => $this->__('Created at'),
            'width'     => '120px',
            'type'      => 'datetime',
        ));

        $this->addColumn('article_date_updated', array(
            'index'     => 'article_date_updated',
            'header'    => $this->__('Updated at'),
            'width'     => '120px',
            'type'      => 'datetime',
        ));

         $this->addColumn('action', array(
            'header'    =>  $this->__('Action'),
            'width'     => '50px',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => $this->__('Edit'),
                    'url'       => array('base' => '*/*/edit'),
                    'field'     => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'article_id',
            'is_system' => true,
          ));

        // $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        // $this->addExportType('*/*/exportXml', $this->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('main_table.article_id');
        $this->getMassactionBlock()->setFormFieldName('article_ids');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => $this->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => $this->__('Are you sure?')
        ));

        $statuses = AW_Kbase_Model_Source_Status::toOptionArray();
        array_unshift($statuses, array('label' => '', 'value' => ''));

        $this->getMassactionBlock()->addItem('status', array(
            'label'=> $this->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                    'visibility'    => array(
                        'name'          => 'status',
                        'type'          => 'select',
                        'class'         => 'required-entry',
                        'label'         => $this->__('Status'),
                        'values'        => $statuses
                    )
            )
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /*
     * Applies a filter to 'Categories' column
     * @see Mage_Adminhtml_Block_Widget_Grid::_addColumnFilterToCollection
     */
    protected function _filterCategoryCondition($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if('0' === $value)
            $collection->getSelect()
                ->where(AW_Kbase_Model_Mysql4_Article_Collection::TABLE_ALIAS_CATEGORY_ARTICLE.'.category_id IS NULL');
        elseif($value)
            $collection
                ->addCategoryFilter($value);
    }
   
    
    /*
     * Applies a filter to 'Author' column
     * @see Mage_Adminhtml_Block_Widget_Grid::_addColumnFilterToCollection()
     */
    protected function _filterAuthorCondition($collection, $column)
    {
        if(!$value = $column->getFilter()->getValue()) return;
        $collection->addAuthorFilter($value);
    }

}
