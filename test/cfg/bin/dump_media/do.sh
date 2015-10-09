#!/bin/sh
##
#       Create archive of the Magento media files (tar.gz)
##

# local specific environment
LOCAL_ROOT=${LOCAL_ROOT}
LOCAL_ROOT_MEDIA=${LOCAL_MEDIA_ROOT}

# instance independend environment
ARCHIVE_NAME=mage_app_prxgt_store_media.tar.gz
MEDIA_ARCHIVE=$LOCAL_ROOT/bin/dump_media/$ARCHIVE_NAME

echo "Remove archive of the Magento media data ($MEDIA_ARCHIVE)."
rm -f $MEDIA_ARCHIVE

echo "Archive Magento media data from '$LOCAL_ROOT_MEDIA' to '$MEDIA_ARCHIVE'."
tar -zcf $MEDIA_ARCHIVE -C $LOCAL_ROOT_MEDIA --exclude="*/cache/*" --exclude="*/.thumbs/*" --exclude="import/*" .

echo "Media data is archived to '$MEDIA_ARCHIVE'."
