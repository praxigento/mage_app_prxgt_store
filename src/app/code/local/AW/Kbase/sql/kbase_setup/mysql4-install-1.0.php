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
    $installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('kbase/article')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('kbase/article')}` (
 `article_id` int(10) unsigned NOT NULL auto_increment,
 `article_title` varchar(254) NOT NULL default '',
 `article_author_id` int(10) unsigned NOT NULL default '0',
 `article_text` text NOT NULL,
 `article_date_created` datetime NOT NULL,
 `article_date_updated` datetime NOT NULL,
 `article_status` tinyint(1) NOT NULL default '1',
 `article_url_key` varchar(254) NOT NULL default '',
 `article_attachment` varchar(254) NOT NULL default '',
 `article_rating` float NOT NULL default '0',
 `article_rating_votes` int(10) unsigned NOT NULL default '0',
 PRIMARY KEY  (`article_id`),
 KEY `{$this->getTable('kbase/article')}_index_title` (`article_title`),
 KEY `{$this->getTable('kbase/article')}_index_url_key` (`article_url_key`),
 KEY `{$this->getTable('kbase/article')}_index_rating` (`article_rating`),
 KEY `{$this->getTable('kbase/article')}_index_updated` (`article_date_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AW Knowledge Base Articles';


-- DROP TABLE IF EXISTS {$this->getTable('kbase/category')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('kbase/category')}` (
 `category_id` int(10) unsigned NOT NULL auto_increment,
 `category_name` varchar(254) NOT NULL default '',
 `category_status` tinyint(1) NOT NULL default '1',
 `category_url_key` varchar(254) default NULL,
 `category_order` int(10) NOT NULL default '1',
 PRIMARY KEY  (`category_id`),
 KEY `{$this->getTable('kbase/category')}_index_name` (`category_name`),
 KEY `{$this->getTable('kbase/category')}_index_url_key` (`category_url_key`),
 KEY `{$this->getTable('kbase/category')}_index_order` (`category_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AW Knowledge Base Categories';


-- DROP TABLE IF EXISTS {$this->getTable('kbase/tag')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('kbase/tag')}` (
 `tag_article_id` int(10) unsigned NOT NULL,
 `tag_name` varchar(254) NOT NULL default '',
 PRIMARY KEY  (`tag_article_id`,`tag_name`),
 KEY `{$this->getTable('kbase/tag')}_index_tag` (`tag_name`),
 CONSTRAINT `{$this->getTable('kbase/tag')}_fk_article` FOREIGN KEY (`tag_article_id`) REFERENCES {$this->getTable('kbase/article')} (`article_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AW Knowledge Base Article tags';


-- DROP TABLE IF EXISTS {$this->getTable('kbase/category_article')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('kbase/category_article')}` (
 `article_id` int(10) unsigned NOT NULL,
 `category_id` int(10) unsigned NOT NULL,
 PRIMARY KEY  (`article_id`,`category_id`),
 KEY `{$this->getTable('kbase/category_article')}_fk_category` (`category_id`),
 KEY `{$this->getTable('kbase/category_article')}_fk_article` (`article_id`),
 CONSTRAINT `{$this->getTable('kbase/category_article')}_fk_category` FOREIGN KEY (`category_id`) REFERENCES {$this->getTable('kbase/category')} (`category_id`) ON DELETE CASCADE,
 CONSTRAINT `{$this->getTable('kbase/category_article')}_fk_article` FOREIGN KEY (`article_id`) REFERENCES {$this->getTable('kbase/article')} (`article_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AW Knowledge Base Article to Category relation';


-- DROP TABLE IF EXISTS {$this->getTable('kbase/category_store')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('kbase/category_store')}` (
 `category_id` int(10) unsigned NOT NULL,
 `store_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY  (`category_id`,`store_id`),
 KEY `{$this->getTable('kbase/category_store')}_index_store_id` (`store_id`),
 CONSTRAINT `{$this->getTable('kbase/category_store')}_fk_category` FOREIGN KEY (`category_id`) REFERENCES {$this->getTable('kbase/category')} (`category_id`) ON DELETE CASCADE,
 CONSTRAINT `{$this->getTable('kbase/category_store')}_fk_store` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AW Knowledge Base Category to Store relation';

");
} catch (Exception $e) {
     /* Mage::logException($e); */
}

$installer->endSetup();
