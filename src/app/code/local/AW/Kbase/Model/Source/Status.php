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
 * Article & category status
 */
class AW_Kbase_Model_Source_Status
{
    /*
     * Article 'disabled' status
     */
    const STATUS_DISABLED = 0;


    /*
     * Article 'enabled' status
     */
    const STATUS_ENABLED = 1;


    /*
     * Returns array of status options
     * @return array Options array like id => name
     */
    public static function toShortOptionArray()
    {
        $helper = Mage::helper('kbase');
        $result = array(
            self::STATUS_DISABLED   => $helper->__('Disabled'),
            self::STATUS_ENABLED    => $helper->__('Enabled'),
        );
        return $result;
    }

    public static function toOptionArray()
    {
        $options = self::toShortOptionArray();
        $res = array();

        foreach($options as $k => $v)
            $res[] = array(
                'value' => $k,
                'label' => $v
            );

        return $res;
    }

}