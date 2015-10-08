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
	ADD `description` TEXT NOT NULL AFTER `title` ;
");

$installer->run("
ALTER TABLE {$this->getTable('forum_users')}
	ADD `total_posts` INT (10) NOT NULL AFTER `first_post` ;
");

$installer->run("
ALTER TABLE {$this->getTable('forum_users')}
	ADD `user_nick` VARCHAR (255) NOT NULL AFTER `system_user_name` ;
");


$installer->run("
ALTER TABLE {$this->getTable('forum_users')} CHANGE `first_post` `first_post` DATETIME;
");

$installer->run("
ALTER TABLE {$this->getTable('forum_users')} CHANGE `system_user_name` `system_user_name` VARCHAR( 255 );
");

$installer->endSetup();
