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
//is_primary
$installer->startSetup();

$sql = " DROP TABLE IF EXISTS " . $this->getTable('forum_privatemessages'). ";
CREATE TABLE " . $this->getTable('forum_privatemessages'). " (
`pm_id` INT( 12 ) NOT NULL AUTO_INCREMENT ,
`date_sent` datetime NULL,
`message` TEXT NOT NULL ,
`subject` TEXT NOT NULL ,
`parent_id` INT( 12 ) NOT NULL ,
`is_primary` smallint(6) NOT NULL default '0',
`sent_from` INT( 12 ) NOT NULL ,
`sent_to` INT( 12 ) NOT NULL  ,
`is_trash` smallint(6) NOT NULL default '0',
`is_read` smallint(6) NOT NULL default '0',
`is_deletesent` smallint(6) NOT NULL default '0',
`is_deleteinbox` smallint(6) NOT NULL default '0',
PRIMARY KEY ( `pm_id` )
)ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$installer->run($sql);

$installer->endSetup();
