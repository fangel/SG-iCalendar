#!/bin/sh
cat SG_iCal.php | grep -v "BUILD: Remove line"
find ./helpers | grep SG_iCal | grep -v svn | xargs cat | grep -v "BUILD: Remove line"
find ./blocks | grep SG_iCal | grep -v svn | xargs cat | grep -v "BUILD: Remove line"
