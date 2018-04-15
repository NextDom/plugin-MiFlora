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

from threading import Lock
import re
import subprocess
import logging
import time
import math
logger = logging.getLogger(__name__)
lock = Lock()

# pylint: disable=too-many-arguments


def write_ble(mac, handle, value, write_adpater="hci0",
              write_security="high", retries=3):
    """
    Read from a BLE address

    @param: mac - MAC address in format XX:XX:XX:XX:XX:XX
    @param: handle - BLE characteristics handle in format 0xXX
    @param: value - value to write to the handle
    @param: timeout - timeout in seconds
    """

    global lock  # pylint: disable=global-statement
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


def read_ble(mac, handle, read_adpater="hci0", read_security="high",
             read_flora_debug=0, retries=3):
    """
    Read from a BLE address

    @param: mac - MAC address in format XX:XX:XX:XX:XX:XX
    @param: handle - BLE characteristics handle in format 0xXX
    @param: timeout - timeout in seconds
    """

    global lock  # pylint: disable=global-statement
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

        # except subprocess.TimeoutExpired:
        #    print("Timeout while waiting for gatttool output")

        attempt += 1
        # print("Waiting for ",delay," seconds before retrying")
        if attempt < retries:
            time.sleep(delay)
            delay *= 2

    return None


def convert_temperature(rawValue):
    rawValueInt = rawValue[1] * 255 + rawValue[0]
    temperature = 0.00000003044 * pow(rawValueInt, 3.0) - 0.00008038 * pow(rawValueInt,
                                                                           2.0) + rawValueInt * 0.1149 - 30.449999999999999
    return round(temperature * 10) / 10


def convert_Lux(rawValue):
    rawValueInt = rawValue[1] * 255 + rawValue[0]
    sunlight = 0.08640000000000001 * (192773.17000000001 * math.pow(rawValueInt, -1.0606619))
    return round(sunlight * 10) / 10


def convert_Soil(rawValue):
    rawValueInt = rawValue[1] * 255 + rawValue[0]
    soilMoisture = 11.4293 + (0.0000000010698 * math.pow(rawValueInt, 4.0) - 0.00000152538 *
                              math.pow(rawValueInt, 3.0) + 0.000866976 * math.pow(rawValueInt, 2.0) - 0.169422 * rawValueInt)
    soilMoisture = 100.0 * (0.0000045 * math.pow(soilMoisture, 3.0) - 0.00055 *
                            math.pow(soilMoisture, 2.0) + 0.0292 * soilMoisture - 0.053)
    return round(soilMoisture)


def convert_Battery(rawValue):
    rawValueInt = rawValue[0]
    return rawValueInt


def convert_Name(rawValue):
    # rawValueInt=rawValue[0]
    str1 = ''.join(chr(e) for e in rawValue)
    return str1


def convert_2Bytes(rawValue):
    rawValueInt = rawValue[1] * 255 + rawValue[0]
    return rawValueInt


timeout = 20
#mac_add = sys.argv[1]
#adpater = sys.argv[2]
#security = sys.argv[3]
firmware = "2.6.0"
flora_debug = "1"
adpater = "hci0"
security = "high"
mac_add = "A0:14:3D:7D:77:26"

# Gestion de la temperature de la terre
handlerd = "0x002d"
result_flora = read_ble(mac_add, handlerd, adpater, security, flora_debug)
print "Soil Temperature brute:", result_flora
# avec convert_temperature 21.5, app: 22/23 live
temperature = convert_temperature(result_flora)
print " -->Soil Temperature:", temperature

# Gestion de la temperature de l'air
handlerd = "0x0031"
result_flora = read_ble(mac_add, handlerd, adpater, security, flora_debug)
print "Air Temperature brute:", result_flora
temperature = convert_temperature(result_flora)
print " -->Air Temperature:", temperature

# Gestion des LUX
handlerd = "0x0025"
result_flora = read_ble(mac_add, handlerd, adpater, security, flora_debug)
print "Lux brute:", result_flora
# 0.1 app: 0 moyenne 976 (semble bien etre la version live)
temperature = convert_Lux(result_flora)
print " -->Lux:", temperature

# Gestion de Soil EC (Hygrometrie je pense)
handlerd = "0x0029"
result_flora = read_ble(mac_add, handlerd, adpater, security, flora_debug)
# TODO: convert raw(0 - 1771) to 0 to 10(mS / cm)
# avec convert_Soil: 19,4% avec cette formule, app: 20/21%
print "Soil EC brut:", result_flora
soil = convert_Soil(result_flora)
print " -->Soil EC:", soil

# Gestion de Soil VWC - (Engrais je pense)
handlerd = "0x0035"
result_flora = read_ble(mac_add, handlerd, adpater, security, flora_debug)
print "Soil VWC brut: ", result_flora
# 14.8 --> app: non mesurable
Soil = convert_Soil(result_flora)
print " -->Soil VWC:", Soil

# Gestion de la batterie
handlerd = "0x004c"
result_flora = read_ble(mac_add, handlerd, adpater, security, flora_debug)
print "Batterie brut: ", result_flora
# 30 , app: courbe semble etre vers 35-37
batterie = convert_Battery(result_flora)
print " -->Batterie: ", batterie

# Gestion du nom
handlerd = "0x0003"
result_flora = read_ble(mac_add, handlerd, adpater, security, flora_debug)
print "Name brut: ", result_flora
Name = convert_Name(result_flora)
print " -->Name: ", Name
