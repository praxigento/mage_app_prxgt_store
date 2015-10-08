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


class AW_Kbase_Adminhtml_ArticleController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('kbase/article');
    }
    
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

    /*
     * Preparing layout
     */
    protected function _initAction()
    {  
        $this->loadLayout()
            ->_setActiveMenu('kbase/article')
            ->_displayTitle('Articles')
            ->_addBreadcrumb($this->__('Articles Manager'), $this->__('Article Manager'));
        return $this;
    }

    public function indexAction() { $this->_initAction()->renderLayout(); }

    public function newAction() { $this->_forward('edit'); }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $data = Mage::getModel('kbase/article')->load($id)->getData();
        $session = Mage::getSingleton('adminhtml/session');
       
        if(isset($data['article_id']) || $id == 0)
        {
            $sessionData = $session->getKBaseArticleData(true);
            $session->setKBaseArticleData(false);

            if(is_array($sessionData)) $data = array_merge($data, $sessionData);

            // for compatibility with previous KB versions
            if(isset($data['article_url_key']))
                $data['article_url_key'] = urldecode($data['article_url_key']);

            if( isset($data['article_tags'])
            &&  is_array($data['article_tags'])
            )   $data['article_tags'] = implode(', ', $data['article_tags']);
            
            Mage::register('kbase_article', $data);

            $this->loadLayout();
            $this->_setActiveMenu('kbase/article');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('kbase/adminhtml_article_edit'))
                ->_addLeft($this->getLayout()->createBlock('kbase/adminhtml_article_edit_tabs'));
             
            if ($id === null) {
                $this->_displayTitle('Add new article');
            } else {
                $this->_displayTitle($this->__('Edit article #%d', $id));
            }
            
            if(AW_Kbase_Helper_Data::mageVersionIsAbove13())
                if(Mage::getSingleton('cms/wysiwyg_config')->isEnabled())
                    $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

            $this->renderLayout();
        }
        else
        {
            $session->addError($this->__('Article does not exist'));
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
                $data['article_tags'] = implode(', ', Mage::helper('kbase')->prepareTags($data['article_tags']));
                $id = $this->getRequest()->getParam('id');
                
                $_article = Mage::getModel('kbase/article')->load($id);
                $_oldSelectedCategories = $_article->getData('category_ids');
                $_currentAllCategories = AW_Kbase_Model_Mysql4_Category::toOptionArray();
                $_currentAllCategoriesFlat = array();
                foreach($_currentAllCategories as $k => $v)
                    if(isset($v['value'])) $_currentAllCategoriesFlat[] = $v['value'];
                $data['category_ids'] = array_merge($data['category_ids'], array_values(array_diff($_oldSelectedCategories, $_currentAllCategoriesFlat)));
                unset($_article);

                $fileUploadNeeded =     isset($_FILES['article_attachment']['name'])
                                    &&  $_FILES['article_attachment']['name'];

                // delete file if checkbox checked
                if( isset($data['article_attachment_delete'])
                &&  isset($data['article_attachment_old'])
                &&  $data['article_attachment_old']
                ) {
                    @unlink(AW_Kbase_Helper_Data::attachmentDirName($id).DS.$data['article_attachment_old']);
                    $data['article_attachment'] = '';
                }

                // checking creation & updating date
                if( !isset($data['article_date_created'])
                ||  !$data['article_date_created']
                )   $data['article_date_created'] = now();

                $data['article_date_updated'] = now();
                // end of time checking

                // checking rating
                $data['article_rating_votes'] = abs($data['article_rating_votes']);

                if( 1 > $data['article_rating']
                ||  5 < $data['article_rating']
                ) {
                    $session->addError($this->__('Rating should be between 1 and 5'));
                    $session->setKBaseArticleData($data);
                    $this->_redirect('*/*/edit', array(
                                'id'    => $id,
                                'tab'   => 'rating'
                        ));
                    return;
                }

                // checking URL key
                if( !isset($data['article_url_key'])
                ||  !$data['article_url_key']
                )   $data['article_url_key'] = $data['article_title'];

                $data['article_url_key'] = AW_Kbase_Helper_Url::nameToUrlKey($data['article_url_key']);
               
                $model = Mage::getModel('kbase/article')
                            ->setData($data)
                            ->setId($id);

                //checking URL keys uniqueness
                $_origUrlKey = $data['article_url_key'];
                $_cnt = 1;
                $_categoryModel = Mage::getModel('kbase/category');
                $_categoryModel->setCategoryStoreIds($model->getResource()->getCategorySetStoreIds($model->getCategoryIds()))
                    ->setCategoryUrlKey($data['article_url_key']);
                while($model->isUrlKeyUsed() || $_categoryModel->isUrlKeyUsed()) {
                    $model->setData('article_url_key', sprintf("%s-%d", $_origUrlKey, $_cnt));
                    $_categoryModel->setCategoryUrlKey(sprintf("%s-%d", $_origUrlKey, $_cnt++));
                }

                if(!$id && $fileUploadNeeded) // we need to know the ID of the article
                {
                    $model->save();
                    $id = $model->getId();
                }
                
               
                if($fileUploadNeeded)
                {
                    $uploader = new Varien_File_Uploader('article_attachment');

                    $uploader
                        ->setAllowedExtensions(null)    // any extenstion
                        ->setAllowRenameFiles(false)    // don't ask about the file
                        ->setAllowCreateFolders(true)
                        ->setFilesDispersion(false);

                    $uploader->save(AW_Kbase_Helper_Data::attachmentDirName($id), $_FILES['article_attachment']['name']);
                    
                     
                    
                    $model->setArticleAttachment($uploader->getUploadedFileName());
                }

                $model->save();

                $session->addSuccess($this->__('Article was successfully saved'));
                $session->setKBaseArticleData(false);

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
                $session->setKBaseArticleData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        else
        {
            $session->addError($this->__('Unable to find an article to save'));
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
                Mage::getModel('kbase/article')
                    ->setId($id)
                    ->delete();

                $session->addSuccess($this->__('Article was successfully deleted'));
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
        $kbaseIds = $this->getRequest()->getParam('article_ids');

        if(!is_array($kbaseIds)) {
            $session->addError($this->__('Please select article(s)'));
        }
        else
        {
            try
            {
                $model = Mage::getModel('kbase/article');

                foreach($kbaseIds as $kbaseId)
                    $model
                        ->setId($kbaseId)
                        ->delete();

                $session->addSuccess($this->__('Total of %d article(s) were successfully deleted', count($kbaseIds)));
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
        $kbaseIds = $this->getRequest()->getParam('article_ids');
        if(!is_array($kbaseIds)) {
            $session->addError($this->__('Please select item(s)'));
        }
        else
        {
            try
            {
                foreach($kbaseIds as $kbaseId)
                    Mage::getSingleton('kbase/article')
                        ->load($kbaseId)
                        ->setArticleStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();

                $session->addSuccess($this->__('Total of %d article(s) were successfully updated', count($kbaseIds)));
            }
            catch (Exception $e)
            {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function downloadAttachmentAction()
    {
        if(!$id = $this->getRequest()->getParam('id'))
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('No article specified'));
            $this->redirectHome();
            return;
        }
        $fileName = AW_Kbase_Helper_Data::getAttachmentFilename($id);
        AW_Kbase_Helper_Data::downloadFile($fileName);
    }

/*    public function exportCsvAction()
    {
        $fileName   = 'kbase.csv';
        $content    = $this->getLayout()->createBlock('kbase/adminhtml_kbase_grid')
            ->getCsv();

        AW_Kbase_Helper_Data::downloadFile($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'kbase.xml';
        $content    = $this->getLayout()->createBlock('kbase/adminhtml_kbase_grid')
            ->getXml();

        AW_Kbase_Helper_Data::downloadFile($fileName, $content);
    }
*/

}