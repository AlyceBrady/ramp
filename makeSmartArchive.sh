#!/bin/sh

# This script makes an archive of the current Smart
# database tables.  The archive is stored in a file in the dataArchives
# directory, with the current timestamp or day of week as part of the filename.

DIR=/Users/abrady/WebApplications/ramp
ARCHIVEDIR=${DIR}/SmartDataArchives
MYSQLDIR=/usr/local/mysql/bin

# Create a new, unique filename on Sundays so that weekly archives are
# kept indefinitely (or until deleted).  For the other days of the week,
# use the day of the week (e.g., fyregistMon.sql); these files will be
# overwritten by an archive one week later.

DAYOFWEEK=`date '+%a'`
if [ $DAYOFWEEK = "Sun" ]
then
    TODAYSDATE=`date | tr -d ' '`
else
    TODAYSDATE=$DAYOFWEEK
fi

if [ $# -gt 0 ]
then
    ARCHIVENAME=$1
    DATABASES="--databases $1"
else
    ARCHIVENAME=all
    DATABASES="--all-databases"
fi

ARCHIVE=${ARCHIVEDIR}/${ARCHIVENAME}${TODAYSDATE}.sql

# umask 277

OPTIONS="--single-transaction --host=localhost --user=rampdba"
${MYSQLDIR}/mysqldump $OPTIONS $DATABASES >$ARCHIVE

chmod 400 $ARCHIVE

exit 0
