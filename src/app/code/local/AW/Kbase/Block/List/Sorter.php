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
 * Article container list sorter
 */
class AW_Kbase_Block_List_Sorter extends Mage_Core_Block_Template
{
    /*
     * @static Sorting order parameter name
     */
    protected static $_orderVarName = 'orderby';

    /*
     * @static Sorting direction parameter name
     */
    protected static $_dirVarName = 'dir';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('aw_kbase/list/sorter.phtml');
        self::$_dirVarName = AW_Kbase_Helper_Url::URL_PARAM_NAME_SORT_DIR;
        self::$_orderVarName = AW_Kbase_Helper_Url::URL_PARAM_NAME_SORT;
    }

    /*
     * Retrieves list of allowed sorting fields
     * @result array List of allowed sorting fields
     */
    public static function getAllowedSorting()
    {
        return AW_Kbase_Model_Source_Sorting::toShortOptionArray();
    }

    /*
     * Retrieves parameters of current sorting
     * @result array Array containing sorting order at [0] and direction at [1]
     */
    public static function getCurrentSorting()
    {
        $session = Mage::getSingleton('core/session');

        $allowedSorting = array_keys(self::getAllowedSorting());

        if(empty($allowedSorting))
            return array(false, false);

        // processing sort order
        $saveToSession = false;
        if($sortOrder = AW_Kbase_Helper_Url::getParam(self::$_orderVarName))
            $saveToSession = true;
        elseif($sortOrder = Mage::app()->getRequest()->getParam(self::$_orderVarName))
            $saveToSession = true;
        else
            $sortOrder = $session->getKbaseSortOrder();

        if( !$sortOrder
        &&  !in_array($sortOrder, $allowedSorting)
        )   $sortOrder = reset($allowedSorting);

        if($saveToSession)
            $session->setKbaseSortOrder($sortOrder);


        // processing sort direction
        $saveToSession = false;
        if($sortDir = AW_Kbase_Helper_Url::getParam(self::$_dirVarName))
            $saveToSession = true;
        elseif($sortDir = Mage::app()->getRequest()->getParam(self::$_dirVarName))
            $saveToSession = true;
        else
            $sortDir = $session->getKbaseSortDir();

        if( AW_Kbase_Model_Source_Sorting::SORT_ASC != $sortDir
        &&  AW_Kbase_Model_Source_Sorting::SORT_DESC != $sortDir
        )   $sortDir = AW_Kbase_Model_Source_Sorting::SORT_ASC;

        if($saveToSession)
            $session->setKbaseSortDir($sortDir);


        return array($sortOrder, $sortDir);
    }

    /*
     * Returns an URL with sorting order given encoded
     * @param int $sortOrder Sorting order
     * @return string URL with new sorting order encoded
     */
    public static function getSortOrderUrl($sortOrder)
    {
        return AW_Kbase_Helper_Url::secureUrl(self::getSorterUrl(array(self::$_orderVarName => $sortOrder)));
    }

    /*
     * Returns sorting direction inverted from given
     * @param string Sorting direction
     * @return string Inverted sorting direction
     */
    public static function getInvertedDir($dir)
    {
        return (AW_Kbase_Model_Source_Sorting::SORT_ASC == $dir)
                ? AW_Kbase_Model_Source_Sorting::SORT_DESC
                : AW_Kbase_Model_Source_Sorting::SORT_ASC;
    }

    /*
     * Returns an URL with sorting direction given encoded
     * @param int $dir Sorting direction
     * @return string URL with new sorting direction encoded
     */
    public static function getSortDirUrl($dir)
    {
        return AW_Kbase_Helper_Url::secureUrl(self::getSorterUrl(array(self::$_dirVarName => $dir)));
    }

    /*
     * Returns an URL with sorting parameters given encoded
     * @param array $params Sorting parameters
     * @return string URL with new sorting parameters encoded
     */
    public static function getSorterUrl($params)
    {
        return AW_Kbase_Helper_Url::getUrlWithParams($params);
    }

}
