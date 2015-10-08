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

class AW_Zblocks_Block_Adminhtml_Zblocks_Edit_Tab_Cms extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('cms_fieldset', array('legend' => $this->__('CMS Pages')));
        
        $fieldset->addField('show_in_cms', 'select', array(
            'name' => 'show_in_cms',
            'label' => $this->__('Show in CMS pages'),
            'title' => $this->__('Show in CMS pages'),
            'value' => '1',
            'options' => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
        ));
        
        if ($data = Mage::registry('zblocks_data')) { $form->setValues($data); }
        
        $this->setForm($form);

        return parent::_prepareForm();
    }
}