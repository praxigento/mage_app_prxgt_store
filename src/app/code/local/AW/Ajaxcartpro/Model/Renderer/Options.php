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


class AW_Ajaxcartpro_Model_Renderer_Options extends Varien_Object implements AW_Ajaxcartpro_Model_Renderer_Interface
{
    const BLOCK_NAME = 'product.info.options.wrapper';

    protected $_supportedProductTypes = array(
        'simple',
        'configurable',
        'virtual',
        'downloadable',
        'bundle',
        'grouped',
        'giftcard',
        'subscription_simple'
    );

    public function renderFromLayout($layout)
    {
        $block = $layout->getBlock(self::BLOCK_NAME);
        if (!$block) {
            return null;
        }

        $block = $this->_grabData($block, $layout);
        $block = $this->_customBlock($block, $layout);
        $block = $this->_addConfigurableBlock($block, $layout);
        $block = $this->_addDownloadableBlock($block, $layout);
        $block = $this->_addGroupedBlock($block, $layout);
        $block = $this->_addBundleBlock($block, $layout);
        $block = $this->_addGiftBlock($block, $layout);

        if (!in_array($block->getProduct()->getTypeId(), $this->_supportedProductTypes)) {
            throw new AW_Ajaxcartpro_Exception('Product type is not supported');
        }
        if (Mage::helper('ajaxcartpro/catalog')->hasFileOption($block->getProduct())) {
            throw new AW_Ajaxcartpro_Exception('File option is not supported');
        }

        return $this->_renderBlock($block);
    }

    protected function _grabData($block, $layout)
    {
        $newBlock = $layout->getBlock('product.info');
        $block->addData($newBlock->getData());
        return $block;
    }

    protected function _customBlock($block, $layout)
    {
        $block->setTemplate('ajaxcartpro/options.phtml');
        $price = $layout->getBlock('product.clone_prices');
        //remove catalog_msrp
        $block->append($price, 'product_price');
        return $block;
    }

    protected function _addConfigurableBlock($block, $layout)
    {
        $configurableData = $layout->getBlock('product.info.configurable');
        if (!$configurableData) {
            return $block;
        }
        $configurable = $layout->getBlock('product.info.options.configurable');
        $configurable->setTemplate('ajaxcartpro/options/configurable.phtml');

        $block->append($configurableData, 'product_type_data');
        $block->append($configurable, 'product_configurable_options');
        return $block;
    }

    protected function _addDownloadableBlock($block, $layout)
    {
        $downloadableData = $layout->getBlock('product.info.downloadable');
        if (!$downloadableData) {
            return $block;
        }
        $downloadable = $layout->getBlock('product.info.downloadable.options');
        $downloadable->setTemplate('ajaxcartpro/options/downloadable.phtml');

        $block->append($downloadableData, 'product_type_data');
        $block->append($downloadable, 'product_downloadable_options');
        return $block;
    }

    protected function _addGroupedBlock($block, $layout)
    {
        $grouped = $layout->getBlock('product.info.grouped');
        if (!$grouped) {
            return $block;
        }
        $block->append($grouped, 'product_type_data');
        return $block;
    }

    protected function _addBundleBlock($block, $layout)
    {
        $bundleData = $layout->getBlock('product.info.bundle');
        if (!$bundleData) {
            return $block;
        }
        $bundle = $layout->getBlock('product.info.bundle.options');

        $block->append($bundleData, 'product_type_data');
        $block->append($bundle, 'product_bundle_options');
        return $block;
    }

    protected function _addGiftBlock($block, $layout)
    {
        $giftData = $layout->getBlock('product.info.giftcard');
        if (!$giftData) {
            return $block;
        }

        $block->append($giftData, 'product_type_data');
        return $block;
    }

    protected function _renderBlock($block)
    {
        $path = 'sales/msrp/enabled';
        $oldMsrpConfig = Mage::app()->getStore()->getConfig($path);
        Mage::app()->getStore()->setConfig($path, '0');
        $html = $block->toHtml();
        Mage::app()->getStore()->setConfig($path, $oldMsrpConfig);
        return $html;
    }
}