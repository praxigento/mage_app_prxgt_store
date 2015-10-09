#!/bin/sh
##
#       Dump Magento DB and create archive (tar.gz)
##

# local specific environment
DB_NAME=${CFG_DB_NAME}
DB_USER=${CFG_DB_USER}
DB_PASS=${CFG_DB_PASS}
LOCAL_ROOT=${LOCAL_ROOT}

# instance independend environment
BIN_ROOT=$LOCAL_ROOT/bin/dump_db
DB_DUMP=mage_app_prxgt_store_dump.sql

echo "Clean up dump '$DB_DUMP'..."
rm -f $BIN_ROOT/$DB_DUMP
rm -f $BIN_ROOT/$DB_DUMP.tar.gz

echo "Dumping Magento db '$DB_NAME' into '$DB_DUMP'..."
mysqldump --skip-tz-utc --user=$DB_USER --password=$DB_PASS $DB_NAME > $BIN_ROOT/$DB_DUMP
tar -zcf $BIN_ROOT/$DB_DUMP.tar.gz -C $BIN_ROOT/ $DB_DUMP

echo "DB '$DB_NAME' is dumped."
