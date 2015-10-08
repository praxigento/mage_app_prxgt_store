<?php

class TM_AskIt_Block_Adminhtml_AskIt_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $id)),
            'method' => 'post'
        ));
        
        if (Mage::registry('askit_data') ) {
            $data = Mage::registry('askit_data')->getData();
        }

        $fieldset = $form->addFieldset(
            'askit_form',
            array('legend' => Mage::helper('askit')->__('Item information'))
        );
        $fieldset->addField('id', 'hidden', array(
            'name'      => 'id'
        ));
        $fieldset->addField('text', 'textarea', array(
            'label'     => Mage::helper('askit')->__('Text'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'text',
        ));
        $fieldset->addField('hint', 'text', array(
            'label'     => Mage::helper('askit')->__('Hint'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'hint',
        ));
        
        if (null !== $id) {
            $fieldset->addField('customer_name', 'label', array(
                'label'     => Mage::helper('askit')->__('Customer'),
                'class'     => 'required-entry',
//                'disabled'  => null !== $id ? true : false,
                'name'      => 'customer_name'
            ));
            
            $data['product_name'] = Mage::getModel('catalog/product')
                ->load($data['product_id'])->getName();
            
            $link = Mage::helper("adminhtml")->getUrl(
                "adminhtml/catalog_product/edit/",
                array('id' => Mage::registry('askit_data')->getProductId())
            );
            
            $fieldset->addField('product_name', 'link', array(
                'href'      => $link,
                'label'     => Mage::helper('askit')->__('Product Name'),
                'disabled'  => true,
                'name'      => 'product_name'
            ));
            $fieldset->addField('product_id', 'hidden', array(
                'class'     => 'required-entry',
                'name'      => 'product_id'
            ));
        } else {
            $fieldset->addType(
                'autocompleter', 
                'TM_AskIt_Block_Adminhtml_AskIt_Edit_Form_Element_Autocompleter'
            );
             $fieldset->addField('product_id', 'autocompleter', array(
            'label'              => Mage::helper('askit')->__('Product'),
            'name'               => 'product_id',
            'autocompleterUrl'   => Mage::getUrl('*/*/product'),
            'autocompleterValue' => '',
//                'required'           => true,
            ));
//            $fieldset->addField('product_id', 'text', array(
//                'label'     => Mage::helper('askit')->__('Product Id'),
//                'class'     => 'required-entry',
//                'name'      => 'product_id'
//            ));
        }
        
        if (!Mage::app()->isSingleStoreMode()) {
        	$fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')
                    ->getStoreValuesForForm(false, true),
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id'
            ));
            Mage::getModel('askit/askIt')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        //
        $fieldset->addField('email', null !== $id ? 'label' : 'text', array(
            'label'     => Mage::helper('askit')->__('Email'),
            'class'     => 'required-entry',
            'name'      => 'email'
        ));
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('askit')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('askit')->__('Pending'),
                ),
                array(
                    'value'     => 2,
                    'label'     => Mage::helper('askit')->__('Approved'),
                ),
                array(
                    'value'     => 3,
                    'label'     => Mage::helper('askit')->__('Disapprowed'),
                ),
                array(
                    'value'     => 4,
                    'label'     => Mage::helper('askit')->__('Closed'),
                ),
            ),
        ));
        $fieldset->addField('private', 'select', array(
            'label'     => Mage::helper('askit')->__('Private'),
            'name'      => 'private',
            'values'    => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('askit')->__('Public'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('askit')->__('Private'),
                )
            ),
        ));
        $fieldset->addField('created_time', 'date', array(
            'label'     => Mage::helper('askit')->__('Create date'),
            'required'  => true,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'name'      => 'created_time',
        ));

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
