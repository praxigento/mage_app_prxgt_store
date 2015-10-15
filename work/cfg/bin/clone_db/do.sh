#!/bin/sh
##
#   Clone Magento DB from pilot to current machine.
##

# local specific environment
LOCAL_ROOT=${LOCAL_ROOT}
SSH_URL=${REMOTE_SSH_URL}
SSH_ROOT=${REMOTE_SSH_ROOT}

# instance independend environment
BIN_ROOT=$LOCAL_ROOT/bin/clone_db
DB_DUMP=mage_app_prxgt_store_dump.sql

ssh $SSH_URL  < $BIN_ROOT/remote_dump.sh
scp $SSH_URL:$SSH_ROOT/bin/dump_db/$DB_DUMP.tar.gz $BIN_ROOT/
tar -zxf $BIN_ROOT/$DB_DUMP.tar.gz -C $BIN_ROOT
$BIN_ROOT/do_reset.sh