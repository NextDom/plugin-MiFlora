#!/usr/bin/expect -f

set prompt "#"
set port [lindex $argv 0]

spawn bluetoothctl -a
expect -re $prompt
send_user "\n"
send_user "############################################################################\n"
send_user "# Try to power on the Bluetooth device on controller $port\n"
send_user "############################################################################\n"
send "select $port\r"
send "power on\r"
send "scan on\r"
send_user "\n"
send_user "############################################################################\n"
send_user "# Scan bluetooth device in progress\n"
send_user "############################################################################\n"
sleep 5
send_user "############################################################################\n"
send_user "# Scan bluetooth device done\n"
send_user "############################################################################\n"
send "scan off\r"
expect "Controller"
send_user "\n"
send_user "############################################################################\n"
send_user "# Bluetooth device(s) founded  :\n"
send_user "############################################################################\n"
expect -re $prompt
set val $expect_out(buffer)
send_user $val
send "quit\r"
expect eof
