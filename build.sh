#!/bin/sh
find . | grep SG_iCal | xargs cat | grep -v "BUILD: Remove line"
