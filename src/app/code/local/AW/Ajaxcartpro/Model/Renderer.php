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


class AW_Ajaxcartpro_Model_Renderer extends Varien_Object
{

    protected $_sources = array(
        'cart'          => 'ajaxcartpro/renderer_cart',
        'sidebar'       => 'ajaxcartpro/renderer_sidebar',
        'topLinks'      => 'ajaxcartpro/renderer_toplinks',
        'options'       => 'ajaxcartpro/renderer_options',
        'wishlist'      => 'ajaxcartpro/renderer_wishlist',
        'miniWishlist'  => 'ajaxcartpro/renderer_miniwishlist',
        'addProductConfirmation'     => 'ajaxcartpro/renderer_confirmation_addproduct',
        'removeProductConfirmation'  => 'ajaxcartpro/renderer_confirmation_removeproduct',
    );

    public function renderPartsFromLayout($layout, $partsToRender)
    {
        $html = array();
        $renderers = $this->_getRenderers($partsToRender);
        foreach($renderers as $name => $renderer) {
            $renderer->setActionData($this->getActionData());
            $html[$name] = $renderer->renderFromLayout($layout);
        }
        return $html;
    }

    protected function _getRenderers($partsToRender)
    {
        if (!is_array($partsToRender)) {
            return array();
        }
        $renderers = array();
        foreach ($partsToRender as $partName) {
            if (!isset($this->_sources[$partName])) {
                throw new Exception('Renderer is not specified');
            }
            $renderers[$partName] = Mage::getModel($this->_sources[$partName]);
        }
        return $renderers;
    }

}
