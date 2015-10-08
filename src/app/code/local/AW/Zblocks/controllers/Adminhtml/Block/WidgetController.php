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


class AW_Zblocks_Adminhtml_Block_WidgetController extends Mage_Adminhtml_Controller_Action
{
    
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $pagesGrid = $this->getLayout()->createBlock('zblocks/adminhtml_cms_block_widget_chooser', '', array(
            'id' => $uniqId,
        )); 
        $this->getResponse()->setBody($pagesGrid->toHtml());
    }
    
}
