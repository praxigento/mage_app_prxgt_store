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


class AW_Kbase_Block_Adminhtml_Article_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('meta', array(
            'legend' => $this->__('Meta Information'),
            'class' => 'fieldset-wide')
        );

        $fieldset->addField('meta_title', 'text', array(
            'name' => 'meta_title',
            'label' => $this->__('Meta Title'),
        ));

        $fieldset->addField('meta_keywords', 'textarea', array(
            'name' => 'meta_keywords',
            'label' => Mage::helper('cms')->__('Meta Keywords'),
            'title' => Mage::helper('cms')->__('Meta Keywords'),
        ));

        $fieldset->addField('meta_description', 'textarea', array(
            'name' => 'meta_description',
            'label' => Mage::helper('cms')->__('Meta Description'),
            'title' => Mage::helper('cms')->__('Meta Description'),
        ));


        $data = Mage::registry('kbase_article');
        if (!is_array($data)) {
            $data = array();
        }

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}