#!/bin/bash
# Script a lancer depuis le repertoire root du plugin

# Local setup
PROD_IP="10.182.207.57"
TEST_IP="10.182.207.59"
TARGET_DIR="/home/pi"
USER_ID=pi

# Prod or Test
TARGET_IP=$TEST_IP
# TARGET_IP=$TEST_IP
JEEDOM_PLUGIN_PATH="/var/www/html/plugins/MiFlora"
# Script
TARGET=$USER_ID@$TARGET_IP
TIMESTAMP=$(date +%d%m%y-%H%M%S)

# Push plugin into homedir
echo "Publish to: "$TARGET" in $TARGET_DIR/"
echo ""
ssh $TARGET rm -rf $TARGET_DIR/MiFloraCandidate
ssh $TARGET mkdir $TARGET_DIR/MiFloraCandidate
scp -r ./3rparty $TARGET:$TARGET_DIR/MiFloraCandidate 
scp -r ./core $TARGET:$TARGET_DIR/MiFloraCandidate 
scp -r ./desktop $TARGET:$TARGET_DIR/MiFloraCandidate 
scp -r ./docs $TARGET:$TARGET_DIR/MiFloraCandidate 
scp -r ./plugin_info $TARGET:$TARGET_DIR/MiFloraCandidate 
scp -r ./resources $TARGET:$TARGET_DIR/MiFloraCandidate 
# ssh $TARGET ls -latr MiFloraCandidate
# ssh $TARGET ls -latr MiFloraCandidate/resources
echo "Published to: "$TARGET" in $TARGET_DIR/"
echo ""

# backup prod plugin
echo "Backing up current version to "$TARGET_DIR/MiFloraBck/$TIMESTAMP
echo ""
echo "To clean backup dir: ssh $TARGET rm -rf $TARGET_DIR/MiFloraBck/*"
ssh $TARGET mkdir $TARGET_DIR/MiFloraBck
ssh $TARGET mkdir $TARGET_DIR/MiFloraBck/$TIMESTAMP
ssh $TARGET cp -r $JEEDOM_PLUGIN_PATH $TARGET_DIR/MiFloraBck/$TIMESTAMP
ssh $TARGET ls -latr $TARGET_DIR/MiFloraBck/$TIMESTAMP
ssh $TARGET ls -latr $JEEDOM_PLUGIN_PATH

# backup prod plugin
### To tests comment out next
# JEEDOM_PLUGIN_PATH="/home/pi/TestDeploy/MiFlora"
# ssh $TARGET sudo mkdir /home/pi/TestDeploy/MiFlora

#start actual Publishing 
echo "mv $JEEDOM_PLUGIN_PATH $TARGET_DIR/MiFloraBck"
echo "Moving candiate to production"
echo "Fall back command:"
echo "ssh $TARGET sudo cp -r $TARGET_DIR/MiFloraBck/$TIMESTAMP/MiFlora/* $JEEDOM_PLUGIN_PATH"
echo ""
echo "move old plugin:"
ssh $TARGET sudo mv $JEEDOM_PLUGIN_PATH $TARGET_DIR/MiFloraBck
echo "Publishing from: $TARGET_DIR/MiFloraCandidate to $EEDOM_PLUGIN_PATH"
ssh $TARGET sudo mkdir $JEEDOM_PLUGIN_PATH
ssh $TARGET sudo cp -r  $TARGET_DIR/MiFloraCandidate/* $JEEDOM_PLUGIN_PATH
ssh $TARGET ls -altr $JEEDOM_PLUGIN_PATH
ssh $TARGET sudo chmod -R a+x $JEEDOM_PLUGIN_PATH
ssh $TARGET sudo chgrp -R www-data $JEEDOM_PLUGIN_PATH
ssh $TARGET sudo chown -R www-data $JEEDOM_PLUGIN_PATH
ssh $TARGET ls -altr $JEEDOM_PLUGIN_PATH
ssh $TARGET ls -altr /var/www/html/plugins
# Copie diff tool
echo "Start diffing"
scp -r ./tests/tools/diffPluginFiles.sh $TARGET:$TARGET_DIR/MiFloraBck
ssh $TARGET $TARGET_DIR/MiFloraBck/diffPluginFiles.sh $TARGET_DIR/MiFloraBck/$TIMESTAMP/MiFlora $TARGET_DIR/MiFloraCandidate 

  