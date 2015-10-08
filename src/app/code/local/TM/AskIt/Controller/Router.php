<?php
class TM_AskIt_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $router = new TM_AskIt_Controller_Router();

        $front->addRouter(Mage::helper('askit')->getRoute(), $router);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        $identifier = trim($request->getPathInfo(), '/');
        $parts = explode('/', $identifier);
        if (!isset($parts[1])) {
            return false;
        }

        if (!isset($parts[1])) {
            return false;
        }

        $productCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToFilter('url_path', $parts[1]);

        if (!$productCollection->count()) {
            return false;
        }
        
        $product = $productCollection->getFirstItem();
        $product->load($product->getId());
		
        $request->setModuleName('askit')
            ->setControllerName('index')
            ->setActionName('index')
            ->setParam('productId', $product->getId());

        
        $request->setAlias(
            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $identifier
        );
        return true;
    }
}