<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
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
 * @package    AW_Blog
 * @version    1.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Blog_Helper_Cat extends Mage_Core_Helper_Abstract {

    /**
     * Renders CMS page
     *
     * Call from controller action
     *
     * @param Mage_Core_Controller_Front_Action $action
     * @param integer $pageId
     * @return boolean
     */
    public function renderPage(Mage_Core_Controller_Front_Action $action, $identifier=null) {
        if (!$cat_id = Mage::getSingleton('blog/cat')->load($identifier)->getcatId()) {
            return false;
        }

        $page_title = Mage::getSingleton('blog/cat')->load($identifier)->getTitle();
        $blog_title = Mage::getStoreConfig('blog/blog/title') . " - ";

        $action->loadLayout();
        if ($storage = Mage::getSingleton('customer/session')) {
            $action->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }
        $action->getLayout()->getBlock('head')->setTitle($page_title);
        /*
          if (Mage::getStoreConfig('blog/rss/enable'))
          {
          Mage::helper('blog')->addRss($action->getLayout()->getBlock('head'), Mage::getUrl(Mage::getStoreConfig('blog/blog/route') . "/cat/" .$identifier) . "rss");
          }
         */
        $action->getLayout()->getBlock('root')->setTemplate(Mage::getStoreConfig('blog/blog/layout'));
        $action->renderLayout();

        return true;
    }

}
