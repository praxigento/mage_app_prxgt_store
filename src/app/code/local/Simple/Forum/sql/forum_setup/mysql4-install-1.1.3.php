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

$installer->startSetup();
$sql = " DROP TABLE IF EXISTS " . $this->getTable('forum_post'). ";
CREATE TABLE " . $this->getTable('forum_post'). " (
`post_id` INT( 12 ) NOT NULL AUTO_INCREMENT ,
`parent_id` INT( 12 ) NOT NULL ,
`system_user_id` INT( 12 ) NOT NULL  ,
`user_name` VARCHAR( 255 ) NOT NULL ,
`post` TEXT NOT NULL ,
`post_orig` TEXT NOT NULL default '',
`created_time` datetime NULL,
`update_time` datetime NULL,
`status` smallint(6) NOT NULL default '0',
PRIMARY KEY ( `post_id` )
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS " . $this->getTable('forum_topic'). ";
CREATE TABLE " . $this->getTable('forum_topic'). " (    
`topic_id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`parent_id` INT( 12 ) NOT NULL ,
`system_user_id` INT( 12 ) NOT NULL  ,
`is_category` smallint(1) NOT NULL default 0,
`user_name` VARCHAR( 255 ) NOT NULL ,
`created_time` datetime NULL,
`update_time` datetime NULL,
`title` TEXT NOT NULL ,
`url_text` TEXT NOT NULL ,
`status` smallint(1) NOT NULL default 0,
PRIMARY KEY ( `topic_id` )
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS " . $this->getTable('forum_entity'). ";
CREATE TABLE " . $this->getTable('forum_entity'). " (    
`id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`entity_id` INT( 10 ) NOT NULL default 0,
`total_views` INT( 10 ) NOT NULL default 0,
`last_view` datetime NULL,
PRIMARY KEY ( `id` )
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS " . $this->getTable('forum_visitors'). ";
CREATE TABLE " . $this->getTable('forum_visitors'). " (    
`visitor_id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`topic_id` INT( 10 ) NOT NULL default 0,
`system_user_id` INT( 10 ) NOT NULL default 0,
`session_id` VARCHAR( 100 ) NOT NULL default '',
`time_visited` datetime NULL,
PRIMARY KEY ( `visitor_id` )
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS " . $this->getTable('forum_users'). ";
CREATE TABLE " . $this->getTable('forum_users'). " (    
`user_id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`system_user_id` INT( 10 ) NOT NULL default 0,
`system_user_name` INT( 10 ) NOT NULL default 0,
`first_post` INT( 10 ) NOT NULL default 0,
PRIMARY KEY ( `user_id` )
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
$installer->run($sql);

$installer->endSetup();

?>