<?php
class TM_AskIt_Block_AskIt extends Mage_Core_Block_Template
{
    protected $_collection;

    public function getCount()
    {
        return count($this->getItems());
    }

    public function getItems()
    {
        return $this->_getCollection()->getItems();
    }

    public function getProductId()
    {
        if ($product = Mage::registry('current_product')) {
            return $product->getId();
        }
        
        if ($product = $this->getRequest()->getParam('productId', false)) {
            return (int) $product;
        }
//        $requestPath = trim($this->getRequest()->getPathInfo(), '/');
//        $parts = explode('/', $requestPath);
//        $urlPath = end($parts);
//        $product = Mage::getModel('catalog/product')
//            ->loadByAttribute('url_path', $urlPath)
//            ->getId();
//
        return /*$product ? $product :*/ false;
    }

    protected function _getCollection()
    {
        if(!$this->_collection && $this->getProductId() ) {
            $this->_collection = Mage::getModel('askit/askIt')
                ->getCollection()
                ->addStatusFilter(array(
                    2/*aprowed*/,
                    4/*closed*/
                ))
                ->addProductFilter($this->getProductId())
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addPrivateFilter()
                ->setorder('created_time','DESC')
                ->load();

        } else {
            throw new Exception('Set productId');
        }
        return $this->_collection;
    }

    protected function _beforeToHtml()
    {
        if (!$this->getProductId()) {
            return false;
        }

        return parent::_beforeToHtml();
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getAskIt()     
    { 
        if (!$this->hasData('askit')) {
            $this->setData('askit', Mage::registry('askit'));
        }
        return $this->getData('askit');
    }
 
    public function getNewQuestionFormAction()
    {
        $params = array(
            'product' => $this->getProductId(),
            '_secure' => $this->getRequest()->isSecure(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED =>
                Mage::helper('core/url')->getEncodedUrl()
        );
        return Mage::getUrl('askit/index/saveQuestion', $params);
    }

    public function getNewAnswerFormAction($questionId = null)
    {
        $params = array(
            'product' => $this->getProductId(),
            '_secure' => $this->getRequest()->isSecure(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED =>
                Mage::helper('core/url')->getEncodedUrl()
        );
        if (null !== $questionId) {
            $params['question'] = $questionId;
        }
        return Mage::getUrl('askit/index/saveAnswer', $params);
    }

    public function getHintAction($item, $add = true)
    {
        $params = array(
            'item' => $item,
            'add' => $add ? 1 : -1,
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED =>
                Mage::helper('core/url')->getEncodedUrl()
        );
        return Mage::getUrl('askit/index/saveHint', $params);
    }

    public function getAnswersCountTitle($countAnswers)
    {
        return $countAnswers < 1 ?
             Mage::helper('askit')->__('Not Answerred') : $countAnswers . ' ' . ($countAnswers > 1 ?
                 Mage::helper('askit')->__('Answers') :  Mage::helper('askit')->__('Answer'));
    }

    public function canVoted($itemId)
    {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return false;
        }
        $customerId = (int) Mage::getSingleton('customer/session')->getCustomerId();

        $model = Mage::getModel('askit/vote');
        if ($model->isVoted($itemId, $customerId)) {
            return false;
        }
        return true;
    }
}