<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

class Simple_Forum_Block_Adminhtml_Topic_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('forumTopicGrid');
        $this->setDefaultSort('topic_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('forum/topic')->getCollection();
        $collection->getTopicsOnly();
        $table_topics = $collection->getTable('forum/topic');
        $collection->getSelect()->where(  'main_table.is_category=0');
        $collection->getSelect()->joinLeft(array('table_topics'=>$table_topics), 'main_table.parent_id = table_topics.topic_id', 'table_topics.title as parent_title');
        $store_id   = Mage::registry('store_id'); 
        $collection->addStoreFilterToCollection($store_id, true);
        $this->setCollection($collection);
        $r = parent::_prepareCollection();
        return $r;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('topic_id', array(
         'header'    => Mage::helper('forum/topic')->__('ID'),
         'align'     => 'right',
         'width'     => '50px',
         'index'     => 'topic_id',
        ));

        $this->addColumn('title', array(
         'header'    => Mage::helper('forum/topic')->__('Topic Name'),
         'align'     => 'left',
         'index'     => 'title',
         'filter_index'    =>'main_table.title'
        ));
        
        $this->addColumn('user_name', array(
         'header'    => Mage::helper('forum/post')->__('User'),
         'align'     => 'left',
         'index'     => 'user_name',
         'width'     => '100px',
         'filter_index'     => 'main_table.user_name',
        ));
        
        $this->addColumn('parent_title', array(
            'header'    => Mage::helper('forum/topic')->__('Parent Forum'),
            'align'     => 'left',
            'width'     => '140px',
            'index'     => 'parent_title',
         	'filter_index'    =>'table_topics.title'
        ));
        
        $this->addColumn('url_text', array(
            'header'    => Mage::helper('forum/topic')->__('URL Key'),
            'align'     => 'left',
            'width'     => '140px',
            'index'     => 'url_text',
         	'filter_index'    =>'main_table.url_text'
        ));
        
        $this->addColumn('created_time', array(
            'header'    => Mage::helper('forum/post')->__('Creation Time'),
            'align'     => 'left',
            'width'     => '140px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'created_time',
         	'filter_index'    =>'main_table.created_time'
        ));
        
        $this->addColumn('update_time', array(
            'header'    => Mage::helper('forum/post')->__('Updated Time'),
            'align'     => 'left',
            'width'     => '140px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'update_time',
         	'filter_index'    =>'main_table.update_time'
        ));
        

		$this->addColumn('status', array(
            'header'    => Mage::helper('forum/topic')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('forum/topic')->__('Active'),
                0 => Mage::helper('forum/topic')->__('Inactive'),
            ),
            'filter_index'    =>'main_table.status'
        ));
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('forum/topic')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('forum/topic')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('topic');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('forum/topic')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete', array('store'=>Mage::registry('store_id'))),
             'confirm' => Mage::helper('forum/topic')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('forum/topic')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true, 'store'=>Mage::registry('store_id'))),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('forum/topic')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store'=>Mage::registry('store_id')));
    }
}