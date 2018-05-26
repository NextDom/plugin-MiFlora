touch /tmp/dependancy_MiFlora_in_progress
echo 0 > /tmp/dependancy_MiFlora_in_progress
sudo apt-get update
echo 25 > /tmp/dependancy_MiFlora_in_progress
sudo apt-get --yes upgrade
echo "Launch install of MiFlora dependancy"
echo 50 > /tmp/dependancy_MiFlora_in_progress
sudo apt-get install -y python-pip python-dev build-essential python-requests bluetooth libffi-dev libssl-dev
sudo apt-get install python-pip libglib2.0-dev
echo 70 > /tmp/dependancy_MiFlora_in_progress
sudo pip install pyudev
sudo pip install pyserial
sudo pip install requests
echo 80 > /tmp/dependancy_MiFlora_in_progress
sudo pip install cryptography
echo 90 > /tmp/dependancy_MiFlora_in_progress
sudo pip install pycrypto
sudo pip install bluepy
echo 95 > /tmp/dependancy_MiFlora_in_progress
sudo connmanctl enable bluetooth >/dev/null 2>&1
sudo hciconfig hci0 up >/dev/null 2>&1
sudo hciconfig hci1 up >/dev/null 2>&1
echo 100 > /tmp/dependancy_MiFlora_in_progress
echo "Everything is successfully installed!"
rm /tmp/dependancy_MiFlora_in_progress