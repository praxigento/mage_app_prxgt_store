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


class AW_Kbase_Model_Observers {
    public static function checkConfiguration($observer) {
        $request = Mage::app()->getFrontController()->getRequest();
        $params = new Varien_Object($request->getParams());
        $urlSuffix = $params->getData('groups/url_rewrite/fields/url_suffix/value');
        if($urlSuffix && !preg_match('/^[a-z0-9_.-]*$/', $urlSuffix))
            throw new Mage_Core_Exception(Mage::helper('kbase')->__('The following symbols are allowed to be used in the \'URL Suffix\' field: a-z 0-9 - _ .'));
    }
}
