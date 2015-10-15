--
--      Drop/create MAGENTO DB and restore data from dump.
--
drop database if exists ${CFG_DB_NAME};
create database ${CFG_DB_NAME} character set utf8 collate utf8_unicode_ci;
use ${CFG_DB_NAME};
source ${LOCAL_ROOT}/bin/clone_db/mage_ad_dump.sql;

--
--      Change instance specific config.
--
REPLACE INTO core_config_data SET value = '${CFG_URL}', path ='web/unsecure/base_url';
REPLACE INTO core_config_data SET value = '${CFG_URL_SECURED}', path ='web/secure/base_url';
REPLACE INTO core_config_data SET value = '0', path ='web/secure/use_in_frontend';
REPLACE INTO core_config_data SET value = '0', path ='web/secure/use_in_adminhtml';
REPLACE INTO core_config_data SET value = '0', path ='web/seo/use_rewrites';
REPLACE INTO core_config_data SET value = '${CFG_SMTP_HOST}', path ='system/smtp/host';
--- REPLACE INTO core_config_data SET value = '${CFG_LOG4PHP_XML}', path ='dev/log/prxgt_log4php_config_file';
REPLACE INTO core_config_data SET value = '1', path ='dev/template/allow_symlink';
REPLACE INTO core_config_data SET value = '0', path ='dev/js/merge_files';
REPLACE INTO core_config_data SET value = '0', path ='dev/css/merge_css_files';
--
-- Replace store emails
--
REPLACE INTO core_config_data SET value = 'support@praxigento.com', path ='trans_email/ident_general/email';
REPLACE INTO core_config_data SET value = 'support@praxigento.com', path ='trans_email/ident_sales/email';
REPLACE INTO core_config_data SET value = 'support@praxigento.com', path ='trans_email/ident_support/email';
REPLACE INTO core_config_data SET value = 'support@praxigento.com', path ='trans_email/ident_custom1/email';
REPLACE INTO core_config_data SET value = 'support@praxigento.com', path ='trans_email/ident_custom2/email';
--
-- Switch off CDN
--
REPLACE INTO core_config_data SET value = '{{unsecure_base_url}}js/', path ='web/unsecure/base_js_url';
REPLACE INTO core_config_data SET value = '{{unsecure_base_url}}media/', path ='web/unsecure/base_media_url';
REPLACE INTO core_config_data SET value = '{{unsecure_base_url}}skin/', path ='web/unsecure/base_skin_url';
--
-- Replace real emails for customers
--
-- UPDATE customer_entity ce SET ce.email = CONCAT('${MAGE_CFG_CUST_EMAILS_PREFIX}_', ce.nmmlm_core_mlm_id, '@praxigento.com');
--
-- Change counters for Increment IDs:
--
-- DELETE FROM eav_entity_store WHERE entity_type_id=5 AND store_id=1 AND increment_prefix=1;
-- REPLACE INTO eav_entity_store SET entity_type_id=5, store_id=1, increment_prefix=1, increment_last_id=100000100;