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


/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_CMS
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('blog/lblog')}
	ADD `comments` TINYINT( 11 ) NOT NULL AFTER `meta_description` ;
");
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('blog/lstore')};
CREATE TABLE {$this->getTable('blog/lstore')} (
`post_id` smallint(6) unsigned,
`store_id` smallint(6) unsigned
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS {$this->getTable('blog/lcat_store')};
CREATE TABLE {$this->getTable('blog/lcat_store')} (
`cat_id` smallint(6) unsigned ,
`store_id` smallint(6) unsigned
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS {$this->getTable('blog/lpost_cat')};
CREATE TABLE {$this->getTable('blog/lpost_cat')} (
`cat_id` smallint(6) unsigned ,
`post_id` smallint(6) unsigned
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();
