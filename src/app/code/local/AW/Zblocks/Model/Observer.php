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
 * @package    AW_Zblocks
 * @version    2.3.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Zblocks_Model_Observer extends Mage_Core_Model_Session_Abstract {
    public function onStagingMerge($observer) {
        $staging = $observer->getStaging();
        if($staging) {
            $mapper = $staging->getMapperInstance();
            $stagingItems = $mapper->getStagingItems();
            if(is_array($stagingItems) && isset($stagingItems['zblocks_data'])) {
                $websites = $mapper->getWebsites();
                if($websites) {
                    foreach($websites as $wsFrom => $wsTo) {
                        foreach($wsTo as $_wsTo) {
                            $zblocksCollection = Mage::getModel('zblocks/zblocks')->getCollection()
                                ->addStoreFilter($wsFrom)
                                ->addExcludeStoreFilter($_wsTo);
                            foreach($zblocksCollection as $zblock) {
                                $_zblockStores = explode(',', $zblock->getData('store_ids'));
                                array_push($_zblockStores, $_wsTo);
                                $_zblockStores = implode(',', $_zblockStores);
                                $zblock->setData('store_ids', $_zblockStores)
                                    ->save();
                            }
                        }
                    }
                }
                // getStores?
            }
        }
    }
}
