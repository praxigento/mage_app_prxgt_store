<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('askit_item')};
CREATE TABLE {$this->getTable('askit_item')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `customer_name` varchar(128) NOT NULL DEFAULT '',
  `email` varchar(128) NOT NULL DEFAULT '',
  `text` TEXT NOT NULL default '',
  `hint` smallint(6) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default 1,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  `private` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `FK_LINK_PRODUCT_ASKIT` (`product_id`),
  CONSTRAINT `FK_LINK_CUSTOMER_ASKIT` FOREIGN KEY (`customer_id`)
    REFERENCES {$this->getTable('customer_entity')} (`entity_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_LINK_PRODUCT_ASKIT` FOREIGN KEY (`product_id`)
    REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINK_STORE_ASKIT` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('askit_vote')};
CREATE TABLE  {$this->getTable('askit_vote')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `item_id` int(11) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_ASKIT_VOTE_ITEM_ID` FOREIGN KEY (`item_id`)
    REFERENCES {$this->getTable('askit_item')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ASKIT_VOTE_CUSTOMER_ID` FOREIGN KEY (`customer_id`) 
    REFERENCES {$this->getTable('customer_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
