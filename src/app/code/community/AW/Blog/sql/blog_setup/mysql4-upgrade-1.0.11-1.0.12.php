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
 * @package    AW_Blog
 * @version    1.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("

CREATE TABLE IF NOT EXISTS  {$this->getTable('blog/tag')} (
  `id` int(11) NOT NULL auto_increment,
  `tag` varchar(255) NOT NULL,
  `tag_count` int(11) NOT NULL default '0',
  `store_id` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `tag` (`tag`,`tag_count`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();
