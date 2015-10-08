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
 * Main layout block
 */
class AW_Kbase_Block_Main extends Mage_Core_Block_Template
{
    /*
     * @var array Main blocks
     */
    protected $_blocks = array();

    /*
     * Reads block settings from config and adds the block to internal array
     * @param string $name Block name in config
     */
    protected function _readBlock($name)
    {
        if(!Mage::getStoreConfig('kbase/'.$name.'/enabled')) return;
        $sortOrder = Mage::getStoreConfig('kbase/'.$name.'/order');
        if(!$sortOrder) $sortOrder = 0;
        $this->_blocks[$name] = $sortOrder;
    }

    protected function _toHtml()
    {
        if(!AW_Kbase_Helper_Data::getFrontendEnabled()) return '';

        $this->_readBlock('search');
        $this->_readBlock('category');
        $this->_readBlock('top');
        $this->_readBlock('latest');
        $this->_readBlock('tag');

        asort($this->_blocks);

        return parent::_toHtml();
    }

}