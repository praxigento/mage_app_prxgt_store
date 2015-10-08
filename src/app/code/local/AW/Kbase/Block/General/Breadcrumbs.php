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
 * Page breadcrumbs
 */
class AW_Kbase_Block_General_Breadcrumbs extends Mage_Core_Block_Template
{
    /*
     * @var array Breadcrumb items
     */
    protected $_items = array();

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('aw_kbase/general/breadcrumbs.phtml');
    }

    /*
     * Retrieves items as page title
     * @param bool $includeFirst Whether to include first item (usually "Home")
     * @return string Page title
     */
    public function getPageTitle($includeFirst = false)
    {
        $title = '';
        $first = true;
        foreach($this->_items as $index => $item)
            if($index || $includeFirst)
                $title .= ($first ? ($first = false) : ' - ') . $item['name'];

        return $title;
    }

    /*
     * Adds breadcrumbs
     * @param string $name Breadcrumb visible name
     * @param string|empty $url Breadcrumb URL
     * @param string $title Breadcrumb link title
     */
    public function addItem($name, $url=false, $title='')
    {
        $this->_items[] = array(
                'name'  => $name,
                'url'   => $url,
                'title' => $title,
            );

        return $this;
    }

    protected function _toHtml()
    {
        if(!AW_Kbase_Helper_Data::getFrontendEnabled()) return '';

        return parent::_toHtml();
    }

}