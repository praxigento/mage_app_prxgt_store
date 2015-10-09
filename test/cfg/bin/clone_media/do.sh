#!/bin/sh
##
#   Clone media files from the primary server (pilot or production).
##

# local specific environment
LOCAL_ROOT=${LOCAL_ROOT}
LOCAL_ROOT_MEDIA=${LOCAL_MEDIA_ROOT}
REMOTE_SSH_URL=${REMOTE_SSH_URL}
REMOTE_SSH_ROOT=${REMOTE_SSH_ROOT}
LOCAL_OWNER=${LOCAL_OWNER}
LOCAL_GROUP=${LOCAL_GROUP}

# instance independend environment
BIN_ROOT=$LOCAL_ROOT/bin/clone_media
MEDIA_ROOT=$LOCAL_MEDIA_ROOT
ARCHIVE_NAME=mage_app_prxgt_store_media.tar.gz


ssh $REMOTE_SSH_URL  < $BIN_ROOT/remote_dump.sh
scp $REMOTE_SSH_URL:$REMOTE_SSH_ROOT/bin/dump_media/$ARCHIVE_NAME $BIN_ROOT/
rm -fr $MEDIA_ROOT
mkdir -p $MEDIA_ROOT
tar -zxf $BIN_ROOT/$ARCHIVE_NAME -C $MEDIA_ROOT
chown -R $LOCAL_OWNER:$LOCAL_GROUP $MEDIA_ROOT
ln -sT $MEDIA_ROOT $LOCAL_ROOT/mage/media