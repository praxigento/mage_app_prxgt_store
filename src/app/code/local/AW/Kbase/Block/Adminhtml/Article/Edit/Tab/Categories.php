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


class AW_Kbase_Block_Adminhtml_Article_Edit_Tab_Categories extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('categories', array('legend' => $this->__('Categories')));

        $fieldset->addField('category_ids', 'multiselect', array(
            'name'      => 'category_ids[]',
            'label'     => $this->__('Categories'),
            'values'    => AW_Kbase_Model_Mysql4_Category::toOptionArray(),
            'required'  => true,
        ));

        $data = Mage::registry('kbase_article');
        if(!is_array($data)) $data = array();

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}