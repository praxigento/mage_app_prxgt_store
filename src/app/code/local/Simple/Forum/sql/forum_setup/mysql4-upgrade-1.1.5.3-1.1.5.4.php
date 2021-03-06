<?php
/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('forum_topic')}
	ADD `store_id` INT (10) NOT NULL AFTER `priority` ;
");

$installer->run("
ALTER TABLE {$this->getTable('forum_topic')}
	ADD `product_id` INT (10) NOT NULL AFTER `priority` ;
");

$installer->run("
ALTER TABLE {$this->getTable('forum_post')}
	ADD `product_id` INT (10) NOT NULL AFTER `status` ;
");

$installer->run("
ALTER TABLE {$this->getTable('forum_users')}
	ADD `store_id` INT (10) NOT NULL AFTER `first_post` ;
");



$installer->run("
ALTER TABLE {$this->getTable('forum_visitors')}
	ADD `store_id` INT (10) NOT NULL AFTER `topic_id` ;
");


$installer->endSetup();
