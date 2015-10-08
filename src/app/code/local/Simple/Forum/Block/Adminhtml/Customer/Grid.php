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


class Simple_Forum_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('moderatorCustomerGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email');
		$collection->getSelect()->where('website_id != 0');
		$table_moderators = $collection->getTable('forum/moderator');
		$collection->getSelect()->joinLeft(array('table_moderators'=>$table_moderators), '`e`.entity_id = table_moderators.system_user_id', 'table_moderators.moderator_id as Moder_Id');
		$collection->getSelect()->where('table_moderators.moderator_id IS NULL');
		$this->setCollection($collection);
        $r = parent::_prepareCollection();
        return $r;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
         'header'    => Mage::helper('forum/topic')->__('ID'),
         'align'     => 'right',
         'width'     => '50px',
         'index'     => 'entity_id',
        ));

        $this->addColumn('firstname', array(
         'header'    => Mage::helper('forum/topic')->__('First Name'),
         'align'     => 'left',
         'index'     => 'firstname',
        ));
        
        $this->addColumn('lastname', array(
         'header'    => Mage::helper('forum/topic')->__('Last Name'),
         'align'     => 'left',
         'index'     => 'lastname',
        ));
        
        $this->addColumn('email', array(
         'header'    => Mage::helper('forum/topic')->__('Email'),
         'align'     => 'left',
         'index'     => 'email',
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '180px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('moderators');
        //$this->getMassactionBlock()->setUseAjax(true);

        $this->getMassactionBlock()->addItem('assign_to_moder', array(
             'label'=> Mage::helper('forum/topic')->__('Assign To Moderator'),
             'url'  => $this->getUrl('*/*/massAssign'),
             //'useAjax' => true,
        ));
        return $this;
    }
    
    public function getGridUrl()
    {
		return  $this->getCurrentUrl() . 'is_customers/1/';
	}
}