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
DROP TABLE IF EXISTS " . $this->getTable('forum_usersettings'). ";
CREATE TABLE " . $this->getTable('forum_usersettings'). " (
`id` INT( 12 ) NOT NULL AUTO_INCREMENT ,
`system_user_id` INT( 12 ) NOT NULL  ,
`nickname` VARCHAR( 255 ) NOT NULL ,
`signature` TEXT NOT NULL ,
`avatar_name` TEXT NOT NULL ,
`website_id` INT( 12 ) NOT NULL ,
PRIMARY KEY ( `id` )
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
