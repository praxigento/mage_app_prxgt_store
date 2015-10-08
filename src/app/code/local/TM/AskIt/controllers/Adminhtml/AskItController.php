<?php

class TM_AskIt_Adminhtml_AskItController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction() {

        $this->loadLayout();
        $this->_setActiveMenu('askit/items')
            ->_addBreadcrumb(
                Mage::helper('askit')->__('Questions Manager'),
                Mage::helper('askit')->__('Question')
            );
        
        return $this;
    }
 
    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        
        $model = Mage::getModel('askit/askIt')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('askit_data', $model);

            $this->loadLayout(array('default', 'editor'));
            $this->_setActiveMenu('askit/items');

            $this->_addBreadcrumb(
                Mage::helper('askit')->__('Question Manager'),
                Mage::helper('askit')->__('Question Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('askit')->__('Item News'),
                Mage::helper('askit')->__('Item News')
            );

            $this->getLayout()->getBlock('head')
                ->setCanLoadExtJs(true)
                ->setCanLoadRulesJs(true)
                ;
//            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $parentId = (int)$model->getParentId();
            if (0 == $parentId) {
                $this->_addContent(
                        $this->getLayout()->createBlock('askit/adminhtml_askIt_edit')
                    )
                    ->_addLeft(
                        $this->getLayout()->createBlock('askit/adminhtml_askIt_edit_tabs')
                    );
            } else {
                $this->_addContent(
                        $this->getLayout()->createBlock('askit/adminhtml_askIt_editAnswer')
                )
                    ->_addContent(
                        $this->getLayout()->createBlock('askit/adminhtml_askIt_editAnswer_form')
                    )
                ;
            }
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('askit')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction() {
        $this->_forward('edit');
    }
 
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

//            $id = $this->getRequest()->getParam('id');

            $model = Mage::getModel('askit/askIt');

            //update exist
            if (!empty($data['id'])) {

                $model->setData($data);
                $id = (int)$data['id'];
                $model->setId($id);
            } else {
                //new question create 
                
                $model
                    ->setText(strip_tags($data['text']))
                    ->setProductId((int)$data['product_id'])
                    ->setHint((int)$data['hint'])
                    ->setParentId(null)
                    ->setStoreId($data['store_id'])
                    ->setEmail($data['email'])
                    ->setStatus((int)$data['status'])
                    ->setCreatedTime($data['created_time'])
//                    ->setUpdateTime(now())
                    ;
                
                $customer = Mage::getModel('customer/customer')->getCollection()
                    ->addAttributeToFilter(array(
                        array('attribute' => 'email', 'like' => $data['email'])
                    ))->getFirstItem();
                    
                
                if ($customer) {
                    $model->setCustomerName($customer->getName())
                        ->setCustomerId($customer->getId())
                        ;
                }
                $model->save();
                
                $id = (int)$model->getId();
            }
            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                //add new answer
                if (!empty($data['new_answer_text'])) {

                    $newAnswer = Mage::getModel('askit/askIt');
                    $adminUser = Mage::getSingleton('admin/session')->getUser();
                    $newAnswer
                        ->setParentId($id)
                        ->setStatus(2)
                        ->setStoreId($data['store_id'])
                        ->setText($data['new_answer_text'])
                        ->setProductId($data['product_id'])
                        ->setHint(0)
                        ->setCustomerName($adminUser->getFirstname() . ' ' . $adminUser->getLastname())
                        ->setEmail($adminUser->getEmail())
                        ->setCreatedTime(now())
                        ->setUpdateTime(now())
                        ->save()
                        ;
                        
                    if (Mage::getStoreConfig('askit/email/enableCustomerNotification')) {
                        $emailData = new Varien_Object();
                        $question = Mage::getModel('askit/askIt')->load($id);
                        $product = Mage::getSingleton('catalog/product')
                            ->load($newAnswer->getProductId());
                        $storeId = $question->getStoreId();

                        $emailData->setName($question->getCustomerName())
                            ->setProduct($product->getName())
                            ->setProductLink(
                                $product->getUrlModel()->getUrl($product, array('_store' => $storeId))
                            )
                            ->setText($model->getText())
                            ->setAnswer($newAnswer->getText())
                            ->setAdminUserEmail($adminUser->getEmail())
                            ->setCustomerEmail($question->getEmail())
                            ->setStoreId($storeId);

                        $this->_sendCustomerNotification($emailData);
                    }
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('askit')->__('Item was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);


                if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('askit')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }

    protected function _sendCustomerNotification(Varien_Object $data)
    {
        $mailTemplate = Mage::getModel('core/email_template');

        $storeId = $data->getStoreId();
        $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
            ->setReplyTo($data->getAdminUserEmail())
            ->sendTransactional(
                    Mage::getStoreConfig('askit/email/customer_template', $storeId),
                    Mage::getStoreConfig('askit/email/sender', $storeId),
                    $data->getCustomerEmail(), 
                    //Mage::getStoreConfig('askit/email/adminEmail'),
                    null,
                    array('data' => $data)
            );
        return $mailTemplate->getSentSuccess();
    }
 
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('askit/askIt');

                $model->setId($this->getRequest()->getParam('id'))
                ->delete();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $askitIds = $this->getRequest()->getParam('askit');
        if(!is_array($askitIds)) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($askitIds as $askitId) {
                    $askit = Mage::getModel('askit/askIt')->load($askitId);
                    $askit->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully deleted', count($askitIds)
                    )
                );
            } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $askitIds = $this->getRequest()->getParam('askit');
        if(!is_array($askitIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('askit')->__(
                    'Please select item(s)'
            ));
        } else {
            try {
                foreach ($askitIds as $askitId) {
                    $askit = Mage::getSingleton('askit/askIt')
                    ->load($askitId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('askit')->__(
                        'Total of %d record(s) were successfully updated',
                        count($askitIds)
                    )
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'askit.csv';
        $content    = $this->getLayout()->createBlock('askit/adminhtml_askit_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'askit.xml';
        $content    = $this->getLayout()->createBlock('askit/adminhtml_askit_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse(
        $fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
    public function productAction() 
    {
        $query = $this->getRequest()->getParam('query');
        
        $model = new Mage_Adminhtml_Model_Search_Catalog();
        $results = $model->setStart(1)
            ->setLimit(10)
            ->setQuery($query)
            ->load()
            ->getResults();
        
        $html = '';
        foreach ($results as $item) {
            list( , , $productId) = explode('/', $item['id']);
            $html .= "<li id=\"{$productId}\" title=\"{$item['name']}\">
                <strong>{$item['name']}</strong><br/>
                <span class=\"informal\">{$item['description']}</span>
            </li>"
            ;
        }
        echo '<ul>' . $html . '</ul>';       
    }
}