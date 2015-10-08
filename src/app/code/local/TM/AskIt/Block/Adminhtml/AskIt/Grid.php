<?php

class TM_AskIt_Block_Adminhtml_AskIt_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('askItGrid_question');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');

        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('askit/askIt')->getCollection();
        $collection->addQuestionFilter();
        $collection->addProductName();
        $collection->addQuestionCountAnswersFilter();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

// fix search by product name
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'name') {
                 $this->getCollection()->getSelect()
                     ->join(array('cpev' => 'catalog_product_entity_varchar'),
                        'cpev.entity_id = main_table.product_id',
                        array('product_name' => 'value'))
                    ->join(array('ea' => 'eav_attribute'),
                '       cpev.attribute_id = ea.attribute_id')
                    ->join(array('eet' => 'eav_entity_type'),
                        'eet.entity_type_id = ea.entity_type_id')
                    ->where('ea.attribute_code = ?', 'name')
                    ->where('eet.entity_type_code = ?', 'catalog_product')
                ;
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
          'header'    => Mage::helper('askit')->__('ID'),
          'align'     => 'right',
          'width'     => '50px',
          'index'     => 'id',
          'type'      => 'number'
        ));
        $this->addColumn('text', array(
          'header'    => Mage::helper('askit')->__('Question'),
          'align'     => 'left',
          'index'     => 'text',
        ));
        $this->addColumn('count_answers', array(
            'header'    =>Mage::helper('askit')->__('Answers'),
            'width'     => '45px',
            'index'     => 'count_answers',
            'sortable'      => false
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('askit')->__('Product Name'),
            'index'     => 'product_name'
        ));
        $this->addColumn('customer', array(
            'header'    => Mage::helper('askit')->__('Customer'),
            'index'     => 'customer_name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('askit')->__('Email'),
            'index'     => 'email'
        ));

        $this->addColumn('hint', array(
          'header'    => Mage::helper('askit')->__('Votes'),
          'align'     =>'right',
          'width'     => '50px',
          'type'      => 'number',
          'index'     => 'hint',
        ));

        $this->addColumn('status', array(
          'header'    => Mage::helper('askit')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => Mage::getSingleton('askit/status')->getQuestionOptionArray()
        ));
        $this->addColumn('private', array(
          'header'    => Mage::helper('askit')->__('Private'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'private',
          'type'      => 'options',
          'options'   => array(
                0     => Mage::helper('askit')->__('Public'),
                1     => Mage::helper('askit')->__('Private')
            )
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('askit')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('action',
        array(
            'header'    =>  Mage::helper('askit')->__('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('askit')->__('Edit'),
                    'url'       => array('base'=> '*/*/edit'),
                    'field'     => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('askit')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('askit')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('askit');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('askit')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('askit')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('askit/status')->getQuestionOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('askit')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('askit')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }
//////////////////
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
//        Zend_Debug::dump($column->getFilter()->getValue());
//        die;
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
////////////////
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}