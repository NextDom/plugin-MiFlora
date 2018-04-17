""""
Read data from Mi Flora plant sensor.

Reading from the sensor is handled by the command line tool "gatttool" that
is part of bluez on Linux.
No other operating systems are supported at the moment
inspired by #  https://github.com/open-homeautomation/miflora

usage:
cd [path plugin ou copy de ce script]/jeedom_MiFlora/3rparty
/usr/bin/python ./getMiFloraData.py C4:7C:8D:60:E8:21 2.6.6 0 hci0 high
"""
# from struct import *
# from datetime import datetime, timedelta
from threading import Lock
import sys
import re
import subprocess
import logging
import time
logger = logging.getLogger(__name__)
lock = Lock()

def parse_data(data):
    """
    @param: data - result of gatttool
    When debug mode is on, display gatttool result in human readable format
    """
    print("MI_TEMPERATURE=", float(data[1] * 256 + data[0]) / 10)
    print("MI_MOISTURE=", data[7])
    print("MI_LIGHT=", data[4] * 256 + data[3])
    print("MI_CONDUCTIVITY=", data[9] * 256 + data[8])
    return data

# pylint: disable=too-many-arguments
def write_ble(mac, handle, value, write_adpater="hci0", \
write_security="high", retries=3):
    """
    Read from a BLE address

    @param: mac - MAC address in format XX:XX:XX:XX:XX:XX
    @param: handle - BLE characteristics handle in format 0xXX
    @param: value - value to write to the handle
    @param: timeout - timeout in seconds
    """

    global lock # pylint: disable=global-statement
    attempt = 0
    delay = 10
    while attempt <= retries:
        try:
            cmd = "gatttool --adapter={} --device={} --char-write-req -a {} -n {} \
            --sec-level={} ".format(write_adpater, mac, handle, value, write_security)
            #cmd = "gatttool --device={} --char-read -a {} 2>/dev/null".format(mac, handle)
            with lock:
                result = subprocess.check_output(cmd, shell=True)
            result = result.decode("utf-8").strip(' \n\t')
            #print("Got ",result," from gatttool")

        except subprocess.CalledProcessError as err:
            print("Error ", err.returncode, " from gatttool (", err.output, ")")

        attempt += 1
        # print("Waiting for ",delay," seconds before retrying")
        if attempt < retries:
            time.sleep(delay)
            delay *= 2

    return None

def read_ble(mac, handle, read_adpater="hci0", read_security="high", \
read_flora_debug=0, retries=3):
    """
    Read from a BLE address

    @param: mac - MAC address in format XX:XX:XX:XX:XX:XX
    @param: handle - BLE characteristics handle in format 0xXX
    @param: timeout - timeout in seconds
    """

    global lock # pylint: disable=global-statement
    attempt = 0
    delay = 10
    while attempt <= retries:
        try:
            cmd = "gatttool --adapter={} --device={} --char-read -a {} \
            --sec-level={} 2>/dev/null".format(read_adpater, mac, handle, read_security)
            with lock:
                result = subprocess.check_output(cmd,
                                                 shell=True)

            result = result.decode("utf-8").strip(' \n\t')
            # print("Got ",result, " from gatttool")
            # Parse the output
            res = re.search("( [0-9a-fA-F][0-9a-fA-F])+", result)

            if res:
                if read_flora_debug == "1":
                    return [int(x, 16) for x in res.group(0).split()]
                return result

        except subprocess.CalledProcessError as err:
            print("Error ", err.returncode, " from gatttool (", err.output, ")")

        #except subprocess.TimeoutExpired:
        #    print("Timeout while waiting for gatttool output")

        attempt += 1
        # print("Waiting for ",delay," seconds before retrying")
        if attempt < retries:
            time.sleep(delay)
            delay *= 2

    return None

#from gattlib import GATTRequester, GATTResponse

#address = "C4:7C:8D:60:E8:21"
#requester = GATTRequester(address)
#Read battery and firmware version attribute
#def main(argv):

timeout = 20
#macAdd="C4:7C:8D:60:E8:21"
mac_add = sys.argv[1]
handlerd = "0x0035"
handlewr = "0x0033"
firmware = sys.argv[2]
flora_debug = sys.argv[3]
adpater = sys.argv[4]
security = sys.argv[5]

if firmware != "2.6.2":
    write_ble(mac_add, handlewr, "A01F", adpater, security, 3)
result_flora = read_ble(mac_add, handlerd, adpater, security, flora_debug)

if flora_debug == "1":
    print ("read_ble:", parse_data(result_flora))

if flora_debug == "0":
    print (result_flora) # pylint: disable=superfluous-parens
