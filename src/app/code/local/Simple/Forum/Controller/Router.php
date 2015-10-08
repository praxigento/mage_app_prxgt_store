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


class Simple_Forum_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{

	private $_observer_request = false;

    public function ___initialize($observer)
    {
        $front                   = $observer->getEvent()->getFront();
        $forum = new Simple_Forum_Controller_Router();
		$forum->_observer_request = $observer->getEvent()->getData('front')->getRequest();
        $front->addRouter('forum', $forum);
    }

    public function match(Zend_Controller_Request_Http $request)
    {

        if (!Mage::app()->isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

		$route = 'forum';

		$identifier = $request->getPathInfo();
		if (substr(str_replace("/", "",$identifier), 0, strlen($route)) != $route)
		{
			return false;
		}

		//*****************change language if topic url key not exists*****************/

		$___from_store = $this->_observer_request->getParam('___from_store', false);
		$___store      = $this->_observer_request->getParam('___store', false);

		if($___from_store && $___store)
		{
			$request->setModuleName('forum')
				->setControllerName('topic')
				->setActionName('index');
			return true;
		}

		return false;
    }
}
