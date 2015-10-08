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

class Simple_Forum_Block_Adminhtml_Forum_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('post_form', array('legend'=>Mage::helper('forum/topic')->__('Forum Details')));
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('forum/topic')->__('Forum'),
            'class'     => 'required-entry',
           	'required'  => true,
            'name'      => 'title',
        ));
        
        $fieldset->addField('priority', 'text', array(
            'label'     => Mage::helper('forum/topic')->__('Priority'),
            'name'      => 'priority',
        ));
        
        $fieldset->addField('url_text_short', 'text', array(
            'name'      => 'url_text_short',
            'label'     => Mage::helper('forum/topic')->__('URL Key'),
            'class'     => 'validate-identifier',
            'note'      => Mage::helper('cms')->__('Relative to Website Base URL/forum')
        ));
        
        $array_stores = array(
            'name'      => 'store_id',
            'label'     => Mage::helper('forum/topic')->__('Store View'),
            'values'    => $this->getSelectWebsitesStores()
        );
        
        if(!Mage::registry('forum_data')->getId() && Mage::registry('store_id'))
        {
        	Mage::registry('forum_data')->setStore_id(Mage::registry('store_id'));
		}
        
        $fieldset->addField('store_id', 'select', $array_stores);
        
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('forum/topic')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('forum/post')->__('Active'),
                ),
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('forum/post')->__('Inactive'),
                ),
            ),
        ));
        
        $fieldset->addField('description', 'editor', array(
            'name'      => 'description',
            'label'     => Mage::helper('forum/topic')->__('Description')
        ));
        
        if ( Mage::getSingleton('adminhtml/session')->getPostData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
            Mage::getSingleton('adminhtml/session')->setPostData(null);
        } elseif ( Mage::registry('forum_data') ) {
            $form->setValues(Mage::registry('forum_data')->getData());
        }
        return parent::_prepareForm();
    }
    
    private function getSelectWebsitesStores()
    {
		$websites = Mage::getModel('core/website')->getCollection();
        $allgroups = Mage::getModel('core/store_group')->getCollection();
        $groups = array();
        $groups[] = array('label'=>Mage::helper('forum/topic')->__('All Store Views'), 'value'=>0);
        foreach ($websites as $website) {
            $values = array();
            foreach ($allgroups as $group) {
                if ($group->getWebsiteId() == $website->getId()) {
                    $values[] = array('label'=>'&nbsp;&nbsp;' . $group->getName(),'value'=>$this->getStoreViews($group));
                }
            }
            $groups[] = array('label'=>$website->getName(),'value'=>$values);
        }
        return $groups;
	}
	
	private function getStoreCollection($group)
    {
        if (!$group instanceof Mage_Core_Model_Store_Group) {
            $group = Mage::getModel('core/store_group')->load($group);
        }
        $stores = $group->getStoreCollection();
        return $stores;
    }
    
    private function getStoreViews($group)
    {
		$stores = $this->getStoreCollection($group);
		$ret    = array();
		if($stores->getSize())
		{
			foreach($stores as $val)
			{
				$values[] = array('label'=>"" . $val->getName(),'value'=>$val->getId(), 'style'=>'margin-left:15px;');
			}
			$ret    = $values;
		} 
		return $ret;
	}
}

?>