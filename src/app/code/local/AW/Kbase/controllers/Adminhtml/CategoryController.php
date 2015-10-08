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


class AW_Kbase_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_Action
{   
    
     protected function _displayTitle($data = null, $root = 'Knowledge Base') {
        if (version_compare(Mage::getVersion(),'1.4.0.0','>=')) {           
            if ($data) {
                if(!is_array($data)) { $data = array($data); }
                foreach ($data as $title) {
                    $this->_title($this->__($title));
                }
                $this->_title($this->__($root));
            } else {
                $this->_title($this->__($root))->_title($root);
            }
        }
        return $this;
    }
    
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('kbase/category');
    }
    /*
     * Preparing layout
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('kbase/category')
            ->_displayTitle('Categories')
            ->_addBreadcrumb($this->__('Category Manager'), $this->__('Category Manager'));
        return $this;
    }

    public function indexAction() { $this->_initAction()->renderLayout(); }

    public function newAction() { $this->_forward('edit'); }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $data = Mage::getModel('kbase/category')->load($id)->getData();
        $session = Mage::getSingleton('adminhtml/session');

        if(isset($data['category_id']) || $id == 0)
        {
            $sessionData = $session->getKBaseCategoryData(true);
            $session->setKBaseCategoryData(false);

            if(is_array($sessionData)) $data = array_merge($data, $sessionData);

            // for compatibility with previous KB versions
            if(isset($data['category_url_key']))
                $data['category_url_key'] = urldecode($data['category_url_key']);

            Mage::register('kbase_category', $data);

            $this->loadLayout();
            $this->_setActiveMenu('kbase/category');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('kbase/adminhtml_category_edit'))
                ->_addLeft($this->getLayout()->createBlock('kbase/adminhtml_category_edit_tabs'));
            
            if($id === null) {
                $this->_displayTitle('Add new category');
            } else {
                $this->_displayTitle($this->__('Edit category #%d', $id));
            }

            $this->renderLayout();
        }
        else
        {
            $session->addError($this->__('Category does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');

        if($data = $this->getRequest()->getPost())
        {
            try
            {
                $id = $this->getRequest()->getParam('id');

                // checking store IDs
                if(!isset($data['category_store_ids']))
                    $data['category_store_ids'] = Mage::getSingleton('core/store')->getGroup()->getStoreIds();

                if(!is_array($data['category_store_ids']))
                    $data['category_store_ids'] = implode(',', $data['category_store_ids']);

                if(in_array(0, $data['category_store_ids']))
                    $data['category_store_ids'] = array_keys(Mage::app()->getStores(false));

                // checking URL key
                if( !isset($data['category_url_key'])
                ||  !$data['category_url_key']
                )   $data['category_url_key'] = $data['category_name'];

                $data['category_url_key'] = AW_Kbase_Helper_Url::nameToUrlKey($data['category_url_key']);

                $model = Mage::getModel('kbase/category')
                            ->setData($data)
                            ->setId($id);

                if($model->isUrlKeyUsed())
                {
                    $session->addError($this->__('URL key is not unique within category store views'));
                    $session->setKBaseCategoryData($data);
                    $this->_redirect('*/*/edit', array('id' => $id));
                    return;
                }

                $model->save();

                $session->addSuccess($this->__('Category was successfully saved'));
                $session->setKBaseCategoryData(false);

                if($this->getRequest()->getParam('back'))
                {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e)
            {
                Mage::logException($e);
                $session->addError($e->getMessage());
                $session->setKBaseCategoryData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        else
        {
            $session->addError($this->__('Unable to find an category to save'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        $session = Mage::getSingleton('adminhtml/session');

        if($id = $this->getRequest()->getParam('id'))
        {
            try
            {
                Mage::getModel('kbase/category')
                    ->setId($id)
                    ->delete();

                $session->addSuccess($this->__('Category was successfully deleted'));
                $this->_redirect('*/*/');
            }
            catch (Exception $e)
            {
                Mage::logException($e);
                $session->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $kbaseIds = $this->getRequest()->getParam('category_ids');

        if(!is_array($kbaseIds)) {
            $session->addError($this->__('Please select category(s)'));
        }
        else
        {
            try
            {
                $model = Mage::getModel('kbase/category');

                foreach($kbaseIds as $kbaseId)
                    $model
                        ->setId($kbaseId)
                        ->delete();

                $session->addSuccess($this->__('Total of %d category(s) were successfully deleted', count($kbaseIds)));
            }
            catch (Exception $e)
            {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $kbaseIds = $this->getRequest()->getParam('category_ids');
        if(!is_array($kbaseIds)) {
            $session->addError($this->__('Please select item(s)'));
        }
        else
        {
            try
            {
                foreach($kbaseIds as $kbaseId)
                    Mage::getSingleton('kbase/category')
                        ->load($kbaseId)
                        ->setCategoryStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();

                $session->addSuccess($this->__('Total of %d category(s) were successfully updated', count($kbaseIds)));
            }
            catch (Exception $e)
            {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}