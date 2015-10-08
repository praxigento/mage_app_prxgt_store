<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
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
 * @package    AW_Zblocks
 * @version    2.3.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Zblocks_Block_Adminhtml_Zblocks_Edit_Tab_Filter extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        if($this->getRequest()->getParam('id')){
            $model = Mage::getModel('zblocks/condition')->load((int) $this->getRequest()->getParam('id'), 'zblock_id');
        } else {
            $model = Mage::getModel('zblocks/condition');
        }
        
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');
        
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/adminhtml_zblocks/newConditionHtml/form/rule_conditions_fieldset'));
        
        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('zblocks')->__('Conditions (leave blank for all products)'))
        )->setRenderer($renderer);
        
        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('zblocks')->__('Conditions'),
            'title' => Mage::helper('zblocks')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        
        $form->setValues($model->getData());
        
        $this->setForm($form);
      
        
        return parent::_prepareForm();
    }
}
