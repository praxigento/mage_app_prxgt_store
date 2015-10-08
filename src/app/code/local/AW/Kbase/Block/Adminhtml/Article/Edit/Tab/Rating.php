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


class AW_Kbase_Block_Adminhtml_Article_Edit_Tab_Rating extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('rating', array('legend' => $this->__('Rating')));

        $fieldset->addField('article_rating', 'text', array(
            'name'      => 'article_rating',
            'label'     => $this->__('Rating'),
            'note'      => $this->__('From 1 to 5'),
        ));

        $fieldset->addField('article_rating_votes', 'text', array(
            'name'      => 'article_rating_votes',
            'label'     => $this->__('Number of voices'),
            'note'      => $this->__('No negative values allowed'),
        ));

        $data = Mage::registry('kbase_article');
        if(!is_array($data)) $data = array();

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}