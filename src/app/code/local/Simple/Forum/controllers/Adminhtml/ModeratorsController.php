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


class Simple_Forum_Adminhtml_ModeratorsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('forum/topic')
            ->_addBreadcrumb(Mage::helper('forum/topic')->__('Manage Moderators'), Mage::helper('forum/topic')->__('Manage Moderators'));
        return $this;
    }

 	/**
     * default action
     */
    public function indexAction()
    {
    	if ($this->getRequest()->getQuery('isAjax')) {
            $this->loadLayout();
            $is_customers     = $this->getRequest()->getParam('is_customers');
            if($is_customers) $l = $this->getLayout()->createBlock('forum/adminhtml_moderators_index_tab_fromcustomermoder') ;
            else $l = $this->getLayout()->createBlock('forum/adminhtml_moderators_index_tab_gridmoderators') ;
            $this->getResponse()->setBody($l->getGridHtml());
            return;
        }
        $this->_initAction();
        //$l = $this->getLayout()->createBlock('forum/adminhtml_moderators_index_tab_gridmoderators') ;
		$this->_addContent($this->getLayout()->createBlock('forum/adminhtml_moderators_index'))
                 ->_addLeft($this->getLayout()->createBlock('forum/adminhtml_moderators_index_tabs'));
   		$this->renderLayout();
    }

    public function massAssignAction()
    {
		$post  = $this->getRequest()->getPost();
		$moder = $post['moderators'];
		if(is_array($moder))
		{
			foreach($moder as $customer_id)
			{
				$model = Mage::getModel('forum/moderator');
				$cust  =  Mage::getResourceModel('customer/customer_collection');
				$cust->getSelect()->where('entity_id = ?', $customer_id);
				$website_id = $cust->getItemById($customer_id)->getWebsite_id();
				$model->setId(NULL)
				->setSystem_user_id($customer_id)
				->setUser_website_id($website_id);
				$model->save();
			}
			$total = count($moder);
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/topic')->__('%s customers assign to moderators', $total));
		}
		if ($this->getRequest()->getQuery('isAjax')) {
            $this->loadLayout();
            $l = $this->getLayout()->createBlock('forum/adminhtml_moderators_index_tab_fromcustomermoder') ;
            $this->getResponse()->setBody($l->getGridHtml());
            return;
        }
        $this->_redirect('*/*/');
	}

	public function massDeleteAction()
	{
		$post  = $this->getRequest()->getPost();
		$moder = $post['moderators'];
		if(is_array($moder))
		{
			foreach($moder as $customer_id)
			{
				$model = Mage::getModel('forum/moderator')->load($customer_id, 'system_user_id');
				$model->delete();
			}

			$total = count($moder);
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/topic')->__('%s moderators are deleted', $total));
		}
		if ($this->getRequest()->getQuery('isAjax')) {
            $this->loadLayout();
            $l = $this->getLayout()->createBlock('forum/adminhtml_moderators_index_tab_fromcustomermoder') ;
            $this->getResponse()->setBody($l->getGridHtml());
            return;
        }
        $this->_redirect('*/*/');
	}

}

?>