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

ALTER TABLE {$this->getTable('forum_post')}
	ADD `user_nick` VARCHAR( 255 ) NOT NULL AFTER `user_name` ;
");

$installer->run("

ALTER TABLE {$this->getTable('forum_topic')}
	ADD `user_nick` VARCHAR( 255 ) NOT NULL AFTER `user_name`;
");

$installer->run("
ALTER TABLE {$this->getTable('forum_topic')}
	ADD `meta_description` TEXT NOT NULL AFTER `status`;
");

$installer->run("
ALTER TABLE {$this->getTable('forum_topic')}
	ADD `meta_keywords` TEXT NOT NULL AFTER `meta_description`;
");

$installer->run("
ALTER TABLE {$this->getTable('forum_topic')}
	ADD `priority` INT( 10 ) NOT NULL AFTER `meta_keywords`;
");

$installer->run("
ALTER TABLE {$this->getTable('forum_visitors')}
	ADD `parent_id` INT( 10 ) NOT NULL AFTER `topic_id`;
");

$installer->run("
DROP TABLE IF EXISTS " . $this->getTable('forum_moderator') . ";
CREATE TABLE " . $this->getTable('forum_moderator'). " (    
`moderator_id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`system_user_id` INT( 10 ) NOT NULL default 0,
`user_website_id` INT( 10 ) NOT NULL default 0,
PRIMARY KEY ( `moderator_id` )
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS " . $this->getTable('forum_notification') . ";
CREATE TABLE " . $this->getTable('forum_notification'). " (    
`notify_id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`system_user_id` INT( 10 ) NOT NULL default 0,
`topic_id` INT( 10 ) NOT NULL default 0,
`system_user_email` VARCHAR( 255 ) NOT NULL ,
`hash` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `notify_id` )
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
");



$installer->endSetup();
