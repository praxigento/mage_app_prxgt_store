#!/bin/sh
##
#   Setup Magento instance after update with Composer.
##

# local specific environment
LOCAL_ROOT=${LOCAL_ROOT}
# Owner must be group of web server and
# web server must be in group of owner.
LOCAL_OWNER=${LOCAL_OWNER}
LOCAL_GROUP=${LOCAL_GROUP}


##
#   Create new folders
##
# create lock folder
mkdir -p $LOCAL_ROOT/htdocs/var/locks/
# create folder for logs and for old logs

##
# Change rights to folders and files.
##
chown $LOCAL_OWNER:$LOCAL_GROUP -R $LOCAL_ROOT/htdocs/

chmod g+r -R $LOCAL_ROOT/htdocs/
chmod g-w -R $LOCAL_ROOT/htdocs/
chmod g+w -R $LOCAL_ROOT/htdocs/media/
chmod g+w -R $LOCAL_ROOT/htdocs/var/

find $LOCAL_ROOT/bin/ -type f -name "*.sh" -exec chmod u+x {} \;
find $LOCAL_ROOT/htdocs/ -type d -exec chmod g+x {} \;
