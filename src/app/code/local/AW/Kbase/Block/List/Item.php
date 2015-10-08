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
 * Article container list item
 */
class AW_Kbase_Block_List_Item extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $listingType = Mage::getStoreConfig('kbase/general/listing_type');
        $this->setTemplate('aw_kbase/list/item_'.$listingType.'.phtml');
        $this->setRatingEnabled(Mage::getStoreConfig('kbase/general/rating_enabled'));
    }

    public function getProccessedText($text) {
        $processorModelName = AW_Kbase_Helper_Data::mageVersionIsAbove13()
                                ? 'widget/template_filter'
                                : 'core/email_template_filter';
        $processor = Mage::getModel($processorModelName);
        if($processor instanceof Mage_Core_Model_Email_Template_Filter)
            $text = $processor->filter($text);
        return $text;
    }
}
