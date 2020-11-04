#!/bin/bash

#TODAY=`date -d`
#THIS_MONTH_1ST=`date -d "-1 month -$(($(date +%d)-1)) days"`
#THIS_MONTH_1ST=$(date -d "$TODAY" '+%Y-%m-01')
#LAST_MONTH_1ST=$(date -d "$THIS_MONTH_1ST -1 month" '+%F')
#LAST_MONTH_END=$(date -d "$THIS_MONTH_1ST -1 day" '+%F')

#echo $TODAY
#echo "$THIS_MONTH_1ST"
#echo $LAST_MONTH_1ST
#echo $LAST_MONTH_END

Firstday=`date -d "-1 month -$(($(date +%d)-1)) days"`
Lastday=`date -d "-$(date +%d) days"`

echo $Firstday
echo $Lastday
