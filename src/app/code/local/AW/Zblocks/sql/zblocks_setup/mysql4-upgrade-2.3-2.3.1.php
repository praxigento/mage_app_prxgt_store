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

$installer = $this;
$installer->startSetup(); 

    $installer->getConnection()->modifyColumn($this->getTable('zblocks/zblocks'), 'category_ids', 'mediumtext');

try {    
    $installer->run("
        CREATE TABLE IF NOT EXISTS {$this->getTable('zblocks/condition')} (
        `rule_id` int(10) NOT NULL auto_increment,
        `zblock_id` int(10) NOT NULL,
        `conditions_serialized` mediumtext,
        PRIMARY KEY  (`rule_id`),
        KEY `FK_zblocks_condition` (`zblock_id`),
        CONSTRAINT `FK_zblocks_condition` FOREIGN KEY (`zblock_id`) REFERENCES {$this->getTable('zblocks/zblocks')} (`zblock_id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
} catch(Exception $e) { Mage::logException($e); }

$installer->endSetup();