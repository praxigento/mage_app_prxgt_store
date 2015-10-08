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


class AW_Kbase_Block_Adminhtml_Article_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $data = Mage::registry('kbase_article');
        if(!is_array($data)) $data = array();

        $fieldset = $form->addFieldset('general', array(
            'legend'    => $this->__('General information'),
            'class'     => 'fieldset-wide')
        );

        $fieldset->addField('article_date_created', 'hidden', array(
            'name'      => 'article_date_created',
        ));

        $fieldset->addField('article_title', 'text', array(
            'name'      => 'article_title',
            'label'     => $this->__('Title'),
            'required'  => true,
        ));

        if(AW_Kbase_Helper_Data::mageVersionIsAbove13())
        {
            $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
//                    array(
//                        'add_widgets' => false,
//                        'add_variables' => false,
//                    )
                );

            $config->setData(AW_Kbase_Helper_Data::recursiveReplace(
                        '/kbase_admin/',
                        '/'.(string)Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName').'/',
                        $config->getData()
                    )
                );
        }
        else
            $config = false;

        $fieldset->addField('article_text', 'editor', array(
            'name'      => 'article_text',
            'label'     => $this->__('Article'),
            'style'     => 'height:25em',
            'required'  => true,
            'config'    => $config,
        ));

        $fieldset->addField('article_attachment', 'file', array(
            'name'      => 'article_attachment',
            'label'     => $this->__('Attachment'),
            'class'     => 'kbase',
        ));

        $form->getElement('article_attachment')
                ->setRenderer($this->getLayout()
                    ->createBlock('kbase/adminhtml_edit_uploader'));

        $fieldset->addField('article_tags', 'text', array(
            'name'      => 'article_tags',
            'label'     => $this->__('Tags'),
            'note'      => $this->__('Separate tags with commas'),
        ));

        $fieldset->addField('article_url_key', 'text', array(
            'name'      => 'article_url_key',
            'label'     => $this->__('URL key'),
            'note'      => $this->__('URL key must be unique within store views'),
        ));

        $fieldset->addField('article_status', 'select', array(
            'name'      => 'article_status',
            'label'     => $this->__('Status'),
            'values'    => AW_Kbase_Model_Source_Status::toOptionArray(),
        ));

        $fieldset->addField('article_author_id', 'select', array(
            'name'      => 'article_author_id',
            'label'     => $this->__('Author'),
            'values'    => AW_Kbase_Model_Source_User::toShortOptionArray(),
        ));

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}