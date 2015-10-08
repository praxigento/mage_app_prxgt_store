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
 * @package    AW_Ajaxcartpro
 * @version    3.0.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Ajaxcartpro_Model_Observer
{

    public function beforeRenderLayout($observer)
    {
        $request = Mage::app()->getFrontController()->getRequest();
        if ($request->getParam('awacp', false)) {
            $layout = Mage::app()->getFrontController()->getAction()->getLayout();
            $response = Mage::getModel('ajaxcartpro/response');

            $parts = $request->getParam('block');
            if (is_array($parts)) {
                $actionData = Zend_Json::decode($request->getParam('actionData', '[]'));
                $renderer = Mage::getModel('ajaxcartpro/renderer')->setActionData($actionData);
                try {
                    $html = $renderer->renderPartsFromLayout($layout, $parts);
                    $response->setBlock($html);
                } catch(AW_Ajaxcartpro_Exception $e) {
                    $response->addError($e->getMessage());
                } catch(Exception $e) {
                    $response->addError($e->getMessage());
                    Mage::logException($e);
                }
            }

            $this->_sendResponse($response);
        }
    }

    public function sendResponseBefore($observer)
    {
        $request = Mage::app()->getFrontController()->getRequest();
        if ($request->getParam('awacp', false)) {
            $response = Mage::getModel('ajaxcartpro/response');
            $messages = $this->_getErrorMessages();
            if ( count($messages) > 0 ) {
                if ( $url = $this->_getRedirectUrl($response) ) {
                    $response->setRedirectTo($url);
                    $response->addMsg($messages);
                } else {
                    $response->addError($messages);
                }
            }

            $actionData = array();

            //JUST ADD TO DATA
            if (!is_null(Mage::registry('awacp_removed_product_id'))) {
                $actionData['removed_product'] = Mage::registry('awacp_removed_product_id');
            } else if(!is_null(Mage::registry('awacp_added_product_id'))){
                $actionData['added_product'] = Mage::registry('awacp_added_product_id');
            }

            $response->setData('action_data', $actionData);
            $this->_sendResponse($response);
        }
    }

    public function loadLayoutBefore($observer)
    {
        $controllerAction = $observer->getAction();
        $layout = $observer->getLayout();
        //add wysiwyg on system config section
        if ($controllerAction->getFullActionName() === 'adminhtml_system_config_edit' &&
            $controllerAction->getRequest()->getParam('section', false) === 'ajaxcartpro') {
            $layout->getUpdate()->addHandle('editor');
        }
        //remove ACP from checkout (cart page is exception)
        if (strpos($controllerAction->getFullActionName(), 'checkout_') === 0 &&
            strpos($controllerAction->getFullActionName(), 'checkout_cart') !== 0) {
            $layout->getUpdate()->addHandle('remove_ajaxcartpro');
        }
    }

    //REMOVE FROM CART HACK!
    public function salesQuoteRemoveItem($observer)
    {
        $quoteItem = $observer->getQuoteItem();
        if (Mage::registry('awacp_removed_product_id')) {
            return;
        }
        Mage::register('awacp_removed_product_id', $quoteItem->getProductId());
    }

    //ADD TO CART HACK
    public function checkoutCartProductAddAfter($observer)
    {
        $product = $observer->getProduct();
        if (Mage::registry('awacp_added_product_id')) {
            return;
        }
        Mage::register('awacp_added_product_id', $product->getId());
    }

    private function _sendResponse($response)
    {
        echo $response->toJson();
        exit();
    }

    private function _getRedirectUrl()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        $action = Mage::app()->getFrontController()->getAction();

        if ($action instanceof Mage_Checkout_CartController && $request->getActionName() === 'add') {

            $productId = (int)$request->getParam('product', false);
            if (!$productId) {
                return false;
            }
            $product = Mage::getModel('catalog/product')->load($productId);
            if (!$product->isGrouped() && !$product->getTypeInstance(true)->hasRequiredOptions($product)) {
                return false;
            }
            $url = Mage::helper('ajaxcartpro/catalog')->getProductUrl($product, array('_query' => array('options' => 'cart')));
            return $url;

        } else if ($action instanceof Mage_Wishlist_IndexController && $request->getActionName() === 'cart') {
            $itemId = (int)$request->getParam('item', false);
            if (!$itemId) {
                return false;
            }
            $item = Mage::getModel('wishlist/item')->load($itemId);
            $productId = $item->getProductId();
            if (!$productId) {
                return false;
            }
            $product = Mage::getModel('catalog/product')->load($productId);
            if (!$product->isGrouped() && !$product->getTypeInstance(true)->hasRequiredOptions($product)) {
                return false;
            }
            $url = Mage::getUrl('wishlist/index/configure', array('id' => $itemId));
            return $url;
        }

        return false;
    }

    private function _getErrorMessages()
    {
        $allMessages = array_merge(
            $this->_getErrorMessagesFromSession(Mage::getSingleton('checkout/session')),
            $this->_getErrorMessagesFromSession(Mage::getSingleton('wishlist/session')),
            $this->_getErrorMessagesFromSession(Mage::getSingleton('catalog/session'))
        );
        return $allMessages;
    }

    private function _getErrorMessagesFromSession($session)
    {
        $messages = $session->getMessages();
        $sessionMessages = array_merge(
            $messages->getItems(Mage_Core_Model_Message::ERROR),
            $messages->getItems(Mage_Core_Model_Message::WARNING),
            $messages->getItems(Mage_Core_Model_Message::NOTICE)
        );
        return $sessionMessages;
    }

}
