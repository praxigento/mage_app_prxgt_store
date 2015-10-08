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

/*
 * Controller router
 * @see Mage_Core_Controller_Varien_Router_Abstract
 */
class AW_Kbase_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /*
     * Adds module router to cront controller
     */
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();

        $router = new AW_Kbase_Controller_Router();
        $front->addRouter('kbase', $router);
    }

    /*
     * Checks whether the request given matches module URLs
     * @param Zend_Controller_Request_Http $request Request to match
     * @return bool Whether the request given matches module URLs
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if(!Mage::app()->isInstalled())
        {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        return AW_Kbase_Helper_Url::matchUrl($request);
    }

}
