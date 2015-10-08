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
 * Sorting options
 */
class AW_Kbase_Model_Source_Sorting
{
    /*
     * Sorting articles by date
     */
    const BY_DATE = 'date';

    /*
     * Sorting articles by rating
     */
    const BY_RATING = 'rating';

    /*
     * Ascending sorting direction
     */
    const SORT_ASC = 'ASC';

    /*
     * Descending sorting direction
     */
    const SORT_DESC = 'DESC';


    public static function toOptionArray()
    {
        $res = array();

        foreach(self::toShortOptionArray() as $key => $value)
            $res[] = array('value' => $key, 'label' => $value);

        return $res;
    }

    /*
     * Returns array of sorting options
     * @return array Options array like id => name
     */
    public static function toShortOptionArray()
    {
        $res = array(
            self::BY_DATE => Mage::helper('kbase')->__('Date'),
        );

        if(Mage::getStoreConfig('kbase/general/rating_enabled'))
            $res[self::BY_RATING] = Mage::helper('kbase')->__('Rating');

        return $res;
    }

    /*
     * Returns sorting direction name
     * @param int @dir Sorting direction
     * @return string Sorting name
     */
    public static function getSortDirDescription($dir)
    {
        $helper = Mage::helper('kbase');
        switch($dir)
        {
            case self::SORT_ASC:  return $helper->__('Ascending'); break;
            case self::SORT_DESC: return $helper->__('Descending'); break;
        }
        return 'Unknown';
    }
}
