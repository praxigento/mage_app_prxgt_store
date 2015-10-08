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


class Simple_Forum_Block_Adminhtml_Forum_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('forumForumGrid');
        $this->setDefaultSort('topic_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('forum/forum')->getCollection();
        $collection->getSelect()->where('is_category=1');
        $store_id   = Mage::registry('store_id'); 
        $collection->addStoreFilterToCollection($store_id, 1);
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
         'header'    => Mage::helper('forum/topic')->__('Forum Name'),
         'align'     => 'left',
         'index'     => 'title',
        ));
        
        $this->addColumn('url_text', array(
            'header'    => Mage::helper('forum/topic')->__('URL Key'),
            'align'     => 'left',
            'width'     => '140px',
            'index'     => 'url_text',
        ));
        
        
        $this->addColumn('created_time', array(
            'header'    => Mage::helper('forum/topic')->__('Creation Time'),
            'align'     => 'left',
            'width'     => '140px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'created_time',
        ));
        
        $this->addColumn('update_time', array(
            'header'    => Mage::helper('forum/topic')->__('Updated Time'),
            'align'     => 'left',
            'width'     => '140px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'update_time',
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
        $this->getMassactionBlock()->setFormFieldName('forum');

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
        return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store'=>$this->getStoreId()));
    }
    
    public function getStoreId()
    {
		return Mage::registry('store_id');
	}
}