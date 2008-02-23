#!/bin/sh
find . | grep SG_iCal | grep -v svn | xargs cat | grep -v "BUILD: Remove line"
