@SET OUTPUT=.\sgical.php

cat SG_iCal.php | grep -v "BUILD: Remove line" > %OUTPUT%
DIR /B /S helpers | grep SG_iCal | grep -v svn | sed -e "s/\\/\//g" | xargs cat | grep -v "BUILD: Remove line" >> %OUTPUT%
DIR /B /S blocks | grep SG_iCal | grep -v svn | sed -e "s/\\/\//g" | xargs cat | grep -v "BUILD: Remove line" >> %OUTPUT%
