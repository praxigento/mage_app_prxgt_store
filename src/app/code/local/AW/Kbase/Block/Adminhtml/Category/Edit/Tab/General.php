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


class AW_Kbase_Block_Adminhtml_Category_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('general', array(
            'legend'    => $this->__('General information'),
        ));

        $fieldset->addField('category_name', 'text', array(
            'name'      => 'category_name',
            'label'     => $this->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
        ));

        if(Mage::app()->isSingleStoreMode())
        {
            $fieldset->addField('category_store_ids', 'hidden', array(
                'name'      => 'category_store_ids[]',
                'value'     => Mage::app()->getStore()->getId(),
            ));
        }
        else {
            $fieldset->addField('category_store_ids', 'multiselect', array(
                'name'      => 'category_store_ids[]',
                'label'     => $this->__('Store View'),
                'title'     => $this->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }

        $fieldset->addField('category_url_key', 'text', array(
            'name'      => 'category_url_key',
            'label'     => $this->__('URL key'),
            'note'      => $this->__('Used to address the category from URL. If left empty, will be converted from category title during saving. URL key must be unique within selected store views.'),
        ));

        $fieldset->addField('category_status', 'select', array(
            'name'      => 'category_status',
            'label'     => $this->__('Status'),
            'values'    => AW_Kbase_Model_Source_Status::toOptionArray(),
        ));

        $fieldset->addField('category_order', 'text', array(
            'name'      => 'category_order',
            'label'     => $this->__('Order'),
        ));

        $data = Mage::registry('kbase_category');
        if(!is_array($data)) $data = array();

        if(isset($data['category_store_ids']))
        {
            if(Mage::app()->isSingleStoreMode())
            {
                if(is_array($data['category_store_ids']))
                    $data['category_store_ids'] = isset($data['category_store_ids'][0]) ? $data['category_store_ids'][0] : '';
            }
        }

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}