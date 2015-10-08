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
 * Article list pager
 */
class AW_Kbase_Block_List_Pager extends Mage_Page_Block_Html_Pager
{
    /*
     * Used for array_filter
     */
    public function noEmptyValues($var)
    {
        return trim($var);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_limitVarName = AW_Kbase_Helper_Url::URL_PARAM_NAME_LIMIT;
        $this->_pageVarName = AW_Kbase_Helper_Url::URL_PARAM_NAME_PAGE;

        if( ($pageLimit = trim(Mage::getStoreConfig('kbase/general/articles_per_page')))
        &&  count($pageLimit = array_filter(explode(',', $pageLimit), array($this, 'noEmptyValues')))
        )   $this->_availableLimit = array_combine($pageLimit, $pageLimit);
    }

    /*
     * Retrieves current page number
     * @return int
     */
    public function getCurrentPage()
    {
        if ($page = (int) AW_Kbase_Helper_Url::getParam($this->getPageVarName())) {
            return $page;
        }
        else if ($page = (int) $this->getRequest()->getParam($this->getPageVarName())) {
            return $page;
        }
        return 1;
    }

    /*
     * Returns current URL with pager parameters encoded
     * @see AW_Kbase_Helper_Data::getUrlWithParams()
     */
    public function getPagerUrl($params=array())
    {
        return AW_Kbase_Helper_Url::getUrlWithParams($params);
    }

    /*
     * Returns quantity of page items
     * If the parameter presents in URL then saves its value to session
     * If the parameter does not present in URL then reads its value from session
     * @return int
     */
    public function getLimit()
    {
        if ($this->_limit !== null) {
            return $this->_limit;
        }
        $limits = $this->getAvailableLimit();
        $session = Mage::getSingleton('core/session');
        $saveToSession = false;

        if ($limit = (int) AW_Kbase_Helper_Url::getParam($this->getLimitVarName())) {
            $saveToSession = true;
        }
        elseif($limit = $this->getRequest()->getParam($this->getLimitVarName())) {
            $saveToSession = true;
        } else {
            $limit = $session->getKbasePagerLimit();
        }
        if ($limit && isset($limits[$limit])) {
            if($saveToSession)
                $session->setKbasePagerLimit($limit);

            return $limit;
        }
        $limits = array_keys($limits);
        return $limits[0];
    }

}
