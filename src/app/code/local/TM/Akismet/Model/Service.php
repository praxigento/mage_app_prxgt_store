<?php
class TM_Akismet_Model_Service 
{
    /**
     *
     * @var string
     */
    protected $_apiKey = null;

    /**
     *
     * @var Zend_Service_Akismet
     */
    protected $_service = null;

    /**
     *
     * @return string
     */
    protected function _getApiKey()
    {
        if(is_null($this->_apiKey)){
            $this->_apiKey = Mage::getStoreConfig('akismet/general/api_key');
        }
        return $this->_apiKey;
    }

    /**
     *
     * @return Zend_Service_Akismet
     */
    protected function _getService()
    {
        if (null === $this->_service) {
            $this->_service = new Zend_Service_Akismet(
                $this->_getApiKey(), Mage::getUrl('/')
            );
        }
        return $this->_service;
    }

    /**
     *
     * @param string $author
     * @param string $email
     * @param string $content
     * @return bool
     */
    public function isSpam($author, $email, $content)
    {
        if (!Mage::getStoreConfig('akismet/general/enabled')) {
            return false;
        }
        // Verify akismet api key
        $service = $this->_getService();
        if (!$service->verifyKey($this->_getApiKey())) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('akismet')->__(
                    'Invalid Akismet API Key'
                )
            );
            return false;
        }
        
        $helper = Mage::helper('core/http');
        $data = array(
            'user_ip'              => $helper->getRemoteAddr(),
            'user_agent'           => $helper->getHttpUserAgent(),
            'comment_type'         => 'contact',
            'comment_author'       => $author, //'viagra-test-123',
            'comment_author_email' => $email,
            'comment_content'      => $content
        );

        // Check if the submit post is spam
        if ($service->isSpam($data)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('akismet')->__(
                    'Sorry, but we think you\'re a spammer.'
                )
            );
            return true;
        }

        return false;
    }
//        USAGE EXAMPLE
//        $author = (string) $this->getRequest()->getParam('askitCustomer');
//        $email = (string) $this->getRequest()->getParam('askitEmail');
//        if (!$author || !$email) {
//            Mage::getSingleton('core/session')->addError('Email and Name required');
//            $this->_redirectReferer();
//            return;
//        }
//
//        $isLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
//        if (!$isLoggedIn && !Mage::getStoreConfig('askit/general/allowedGuestQuestion')) {
//            Mage::getSingleton('core/session')->addError('Your must login');
//            $this->_redirectReferer();
//            return;
//        }
//
//        $question   = (string) $this->getRequest()->getParam('askitQuestion');
//        if (Mage::getModel('akismet/service')->isSpam($author, $email, $question)) {
//
//                Mage::getSingleton('core/session')->addError('Sorry, but we think you\'re a spammer.');
//                $this->_redirectReferer();
//                return;
//        }
}