#!/bin/sh
##
#       Reset Magento DB structure
##

# local specific environment
DB_NAME=${CFG_DB_NAME}
DB_USER=${CFG_DB_USER}
DB_PASS=${CFG_DB_PASS}
LOCAL_ROOT=${LOCAL_ROOT}

# instance independend environment
BIN_ROOT=$LOCAL_ROOT/bin/clone_db
DB_DUMP=mage_app_prxgt_store_dump.sql

if [ ! -e $BIN_ROOT/$DB_DUMP ]; then
    echo "'$DB_DUMP' does not exist. Place Magento DB SQL dump to '$BIN_ROOT/$DB_DUMP' and launch this script again."
    exit
fi

echo "Restoring Magento db '$DB_NAME' from dump '$BIN_ROOT/$DB_DUMP'..."
mysql --user=$DB_USER --password=$DB_PASS -e "source $BIN_ROOT/migrate.sql"

echo "DB '$DB_NAME' is restored."
