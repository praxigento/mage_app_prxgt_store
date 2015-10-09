#!/bin/sh
##
#   Setup Magento instance after update with Composer.
##

# local specific environment
LOCAL_ROOT=${LOCAL_ROOT}
LOCAL_ROOT_LOGS=${LOCAL_ROOT_LOGS}
# Owner must be group of web server and
# web server must be in group of owner.
LOCAL_OWNER=${LOCAL_OWNER}
LOCAL_GROUP=${LOCAL_GROUP}


##
#   Create new folders
##
# create lock folder
mkdir -p $LOCAL_ROOT/mage/var/locks/
# create folder for logs and for old logs
mkdir -p $LOCAL_ROOT_LOGS/
mkdir -p $LOCAL_ROOT_LOGS/old/
unlink $LOCAL_ROOT/mage/var/log
ln -sT $LOCAL_ROOT_LOGS $LOCAL_ROOT/mage/var/log

##
# Change rights to folders and files.
##
chown $LOCAL_OWNER:$LOCAL_GROUP -R $LOCAL_ROOT/mage/
chown $LOCAL_OWNER:$LOCAL_GROUP -R $LOCAL_ROOT_LOGS/

chmod g+r -R $LOCAL_ROOT/mage/
chmod g+w -R $LOCAL_ROOT/mage/media/
chmod g+w -R $LOCAL_ROOT/mage/var/
chmod og+w $LOCAL_ROOT_LOGS/

find $LOCAL_ROOT/bin/ -type f -name "*.sh" -exec chmod u+x {} \;
find $LOCAL_ROOT/mage/ -type d -exec chmod g+x {} \;
