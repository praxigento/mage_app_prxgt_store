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


$installer = $this;
$installer->startSetup();

try {
    $installer->run("ALTER TABLE {$this->getTable('kbase/article')}
                        ADD `meta_title` VARCHAR( 255 ) NOT NULL ,
                        ADD `meta_description` VARCHAR( 255 ) NOT NULL ,
                        ADD `meta_keywords` TEXT NOT NULL;");
    $installer->run("ALTER TABLE {$this->getTable('kbase/category')}
                        ADD `meta_title` VARCHAR( 255 ) NOT NULL ,
                        ADD `meta_description` VARCHAR( 255 ) NOT NULL ,
                        ADD `meta_keywords` TEXT NOT NULL;");
} catch (Exception $e) {
    /*    Mage::logException($e); */
}


$installer->endSetup();
