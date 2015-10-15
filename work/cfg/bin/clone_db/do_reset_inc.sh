#!/bin/sh
##
#       Reset Magento DB structure
##

# local specific environment
DB_NAME=${CFG_DB_NAME}
DB_USER=${CFG_DB_USER}
DB_PASS=${CFG_DB_PASS}
LOCAL_ROOT=${LOCAL_ROOT}


# echo "Replace next incremental ID for the orders."
# mysql -u $DB_USER --password=$DB_PASS -D $DB_NAME -e "DELETE FROM eav_entity_store WHERE entity_type_id=5 AND store_id=1 AND increment_prefix=1"
# mysql -u $DB_USER --password=$DB_PASS -D $DB_NAME -e "REPLACE INTO eav_entity_store SET entity_type_id=5, store_id=1, increment_prefix=1, increment_last_id=100000100"

echo "Done."
echo