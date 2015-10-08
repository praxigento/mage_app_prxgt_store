<?php

class TM_AskIt_Block_Adminhtml_AskIt_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('askItGrid_answers');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        //$this->_filterVisibility = false;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('askit/askIt')->getCollection();
        $collection->addParentIdFilter(Mage::registry('askit_data')->getId());
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('child_id', array(
          'header'    => Mage::helper('askit')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
           'type'      => 'number'
        ));
        
        $this->addColumn('child_text', array(
          'header'    => Mage::helper('askit')->__('Text'),
          'align'     =>'left',
          'index'     => 'text',
        ));
        $this->addColumn('child_customer', array(
            'header'    =>Mage::helper('askit')->__('Customer'),
            'index'     => 'customer_name'
        ));
        $this->addColumn('child_email', array(
            'header'    =>Mage::helper('askit')->__('Email'),
            'index'     => 'email'
        ));

        $this->addColumn('child_hint', array(
          'header'    => Mage::helper('askit')->__('Hint'),
          'align'     =>'right',
          'width'     => '50px',
          'type'      => 'number',
          'index'     => 'hint',
        ));

        $this->addColumn('child_status', array(
          'header'    => Mage::helper('askit')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => Mage::getSingleton('askit/status')->getAnswerOptionArray()
        ));
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

        return parent::_prepareColumns();
    }

//    protected function _prepareMassaction()
//    {
//        $this->setMassactionIdField('id');
//        $this->getMassactionBlock()->setFormFieldName('askit');
//
//        $this->getMassactionBlock()->addItem('delete', array(
//             'label'    => Mage::helper('askit')->__('Delete'),
//             'url'      => $this->getUrl('*/*/massDelete'),
//             'confirm'  => Mage::helper('askit')->__('Are you sure?')
//        ));
//
//        $statuses = Mage::getSingleton('askit/status')->getAnswerOptionArray();
//
//        array_unshift($statuses, array('label'=>'', 'value'=>''));
//        $this->getMassactionBlock()->addItem('status', array(
//             'label'=> Mage::helper('askit')->__('Change status'),
//             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//             'additional' => array(
//                    'visibility' => array(
//                         'name' => 'status',
//                         'type' => 'select',
////                         'class' => 'required-entry',
//                         'label' => Mage::helper('askit')->__('Status'),
//                         'values' => $statuses
//                     )
//             )
//        ));
//        return $this;
//    }

    public function getRowUrl($row)
    {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}