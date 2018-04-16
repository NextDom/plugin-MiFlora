#!/bin/bash
# Script pour comparer les versions de plugin avant publication
# Local setup
OLDPLUGIN="$1"
NEWPLUGIN="$2"
cd $OLDPLUGIN
LISTORI=`find .  \( -name "*.php" -or  -name "*.py" -or -name "*.js*" -or -name "*.html" -type f \)|grep -v docs|grep -v "remote-sync"|grep -v "i18n"`
cd $NEWPLUGIN
LISTDEST=`find .  \( -name "*.php" -or  -name "*.py" -or -name "*.js*" -or -name "*.html" -type f \)|grep -v docs|grep -v "remote-sync"|grep -v "i18n"`
LISTTOT="$LISTORI $LISTDEST" 

#LISTMERGED=$LISTTOT
#LISTMERGED=`printf "%s\n" $LISTORI $LISTDEST | sort -n | xargs`
LISTMERGED=`for i in $LISTTOT
do
    echo $i
done | sort -u`

for i in $LISTMERGED
do
    echo "compare $OLDPLUGIN/$i $NEWPLUGIN/$i :"
    diff $OLDPLUGIN/$i $NEWPLUGIN/$i
done
  