<?php
class TM_AskIt_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveQuestionAction()
    {
        $author = (string) $this->getRequest()->getParam('askitCustomer');
        $email = (string) $this->getRequest()->getParam('askitEmail');
        if (!$author || !$email) {
            Mage::getSingleton('core/session')->addError('Email and Name required');
            $this->_redirectReferer();
            return;
        }

        $isLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        if (!$isLoggedIn && !Mage::getStoreConfig('askit/general/allowedGuestQuestion')) {
            Mage::getSingleton('core/session')->addError('Your must login');
            $this->_redirectReferer();
            return;
        }

        $question   = (string) $this->getRequest()->getParam('askitQuestion');
        
        if (Mage::getStoreConfig('askit/general/enableAkismet') &&
            Mage::getModel('akismet/service')->isSpam($author, $email, $question)) {

            $this->_redirectReferer();
            return;
        }
        $productId  = (int) $this->getRequest()->getParam('product');
        $isPrivate  = false;

        $model = Mage::getModel('askit/askIt');
        if($isLoggedIn) {
            $model->setCustomerId(
                Mage::getSingleton('customer/session')->getCustomerId()
            );
            $isPrivate = (bool) $this->getRequest()->getParam('askitPrivate', 0);
        }
        $defaultQuestionStatus = Mage::getStoreConfig('askit/general/defaultQuestionStatus');//pending
        $model
            ->setText($question)
            ->setProductId($productId)
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setHint(0)
            ->setParentId(null)
            ->setCustomerName($author)
            ->setEmail($email)
            ->setCreatedTime(now())
            ->setUpdateTime(now())
            ->setStatus($defaultQuestionStatus)
            ->setPrivate($isPrivate)
            ->save()
            ;

        /* Now send email to admin about new question */
        Mage::getSingleton('core/session')->addSuccess(
            Mage::helper('askit')->__('Your question has been accepted for moderation')
        );

        if (Mage::getStoreConfig('askit/email/enableAdminNotification')) {
        //prepare
            $emailData = new Varien_Object();
            $qlink = Mage::getSingleton('adminhtml/url')->getUrl(
                'askit_admin/adminhtml_askIt/edit',
                array('id' => $model->getId())
            );
            $plink = Mage::getSingleton('adminhtml/url')->getUrl(
                'adminhtml/catalog_product/edit',
                array('id' => $productId)
            );
            $emailData
                ->setQlink($qlink)
                ->setPlink($plink)
                ->setCustomerName($author)
                ->setEmail($email)
                ->setText($question)
                ->setProduct(Mage::getSingleton('catalog/product')->load($productId)->getName())
            ;
            
            $this->_sendAdminNotification($emailData);
        }

        $this->_redirectReferer();
    }

    protected function _sendAdminNotification(Varien_Object $data)
    {
        
        $mailTemplate = Mage::getModel('core/email_template');
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        
        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
            ->setReplyTo($data->getEmail())
            ->sendTransactional(
                    Mage::getStoreConfig('askit/email/admin_template'),
                    Mage::getStoreConfig('askit/email/sender'),
                    Mage::getStoreConfig('askit/email/admin_email'),
                    null,
                    array('data' => $data)
        );
//        return $mailTemplate->getSentSuccess();
        
        // admin notfication wasn't send
        // if (!$mailTemplate->getSentSuccess()) {
        //    throw new Exception('mail not send');
        //}
            
    }

    public function saveAnswerAction()
    {
        if(!Mage::getSingleton('customer/session')->authenticate($this)) {
            Mage::getSingleton('core/session')->addError(
                 Mage::helper('askit')->__('Sorry, only logined customer can add self answer.')
            );
            $this->_redirectReferer();
            return;
        }
        $customerName = (string) $this->getRequest()->getParam('askitCustomer');
        $email = (string) $this->getRequest()->getParam('askitEmail');
        if (!$customerName 
            || !$email
            || !Mage::getStoreConfig('askit/general/allowedCustomerAnswer')) {

            $this->_redirectReferer();
            return;
        }

        $answer     = (string) $this->getRequest()->getParam('askitAnswer');
        $questionId = (string) $this->getRequest()->getParam('question');
        $productId  = (int) $this->getRequest()->getParam('product');
        $customerId = (int) Mage::getSingleton('customer/session')->getCustomerId();

        $model = Mage::getModel('askit/askIt');

        $storeId = Mage::getModel('askit/askIt')->load($questionId)->getStoreId();
        $defaultAnswerStatus = Mage::getStoreConfig('askit/general/defaultAnswerStatus');//pending
        $model
            ->setText(strip_tags($answer))
            ->setStoreId($storeId)
            ->setProductId($productId)
            ->setCustomerId($customerId)
            ->setHint(0)
            ->setParentId($questionId)
            ->setCustomerName($customerName)
            ->setEmail($email)
            ->setCreatedTime(now())
            ->setUpdateTime(now())
            ->setStatus($defaultAnswerStatus)
            ->save();

        Mage::getSingleton('core/session')->addSuccess(
            Mage::helper('askit')->__('Your answer has been accepted')
        );    
        $this->_redirectReferer();
    }

    public function saveHintAction()
    {
        if(!Mage::getSingleton('customer/session')->authenticate($this)) {
            Mage::getSingleton('core/session')->addError(
                 Mage::helper('askit')->__('Sorry, only logined customer can hint.')
            );
            $this->_redirectReferer();
        }

        $itemId = (string) $this->getRequest()->getParam('item');
        $customerId = (int) Mage::getSingleton('customer/session')->getCustomerId();

        $vote = Mage::getModel('askit/vote');

        if ($vote->isVoted($itemId, $customerId)) {
            Mage::getSingleton('core/session')->addError(
                 Mage::helper('askit')->__('Sorry, already voted')
            );
            $this->_redirectReferer();
        }   
        $add = (int) $this->getRequest()->getParam('add');
        $add = $add > 0 ? 1 : -1;
        
        $item = Mage::getModel('askit/askIt')->load($itemId);
        $item->setHint($item->getHint() + $add);
        $item->save();

        $vote->setData(array(
            'item_id' => $itemId,
            'customer_id' => $customerId,
        ));
        $vote->save();
        $this->_redirectReferer();
    }

    public function rssAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
        
    }
}
