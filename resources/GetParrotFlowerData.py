""""
Read data from Mi Flora plant sensor.

Reading from the sensor is handled by the command line tool "gatttool" that
is part of bluez on Linux.
No other operating systems are supported at the moment
inspired by #  https://github.com/open-homeautomation/miflora

usage:
cd [path plugin ou copy de ce script]/jeedom_MiFlora/3rparty
/usr/bin/python ./getParrotFlowerData.py C4:7C:8D:60:E8:21 2.6.6 0 hci0 high
"""

from threading import Lock
import re
import subprocess
import logging
import time
import math
import sys
from ctypes import *

logger = logging.getLogger(__name__)
lock = Lock()

# pylint: disable=too-many-arguments


def convertHexToFloat(s):
    i = int(s, 16)                   # convert from hex to a Python int
    cp = pointer(c_int(i))           # make this into a c integer
    fp = cast(cp, POINTER(c_float))  # cast the int pointer to a float pointer
    return fp.contents.value         # dereference the pointer, get the float


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
            #print cmd
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

def read_ble(mac, handle, read_adpater="hci0", read_security="high", retries=3):
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
            #print cmd
            with lock:
                result = subprocess.check_output(cmd,
                                                 shell=True)

            result = result.decode("utf-8").strip(' \n\t')
            # print("Got ",result, " from gatttool")
            # Parse the output
            res = re.search("( [0-9a-fA-F][0-9a-fA-F])+", result)

            if res:
                return [int(x, 16) for x in res.group(0).split()]

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

def read_ble_float(mac, handle, read_adpater="hci0", read_security="high", retries=3):
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
            #print cmd
            with lock:
                result = subprocess.check_output(cmd,
                                                 shell=True)

            result = result.decode("utf-8").strip(' \n\t')
            # print("Got ",result, " from gatttool")
            # Parse the output
            res = re.search("( [0-9a-fA-F][0-9a-fA-F])+", result)
            resSplit=res.group(0).split()
            resInv=resSplit[3]+resSplit[2]+resSplit[1]+resSplit[0]

            if res:
                return round(convertHexToFloat(resInv),1)

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
    try:
        rawValueInt = rawValue[1] * 255 + rawValue[0]
    except (TypeError, AttributeError):
        rawValueInt = 0

    temperatureVal = 0.00000003044 * math.pow(rawValueInt, 3.0) - 0.00008038 * math.pow(rawValueInt,2.0) + rawValueInt * 0.1149 - 30.449999999999999

    return round(temperatureVal * 10) / 10


def convert_Lux(rawValue):
    try:
        rawValueInt = ((rawValue[1] * 255) + rawValue[0]) * 1.0
    except (TypeError, AttributeError):
        rawValueInt = 0

    if rawValueInt>0:
        sunlight = 0.08640000000000001 * (192773.17000000001 * math.pow(rawValueInt, -1.0606619))
    else:
        sunlight = 0
    return round(sunlight * 10) / 10

def convert_SoilEC(rawValue):
    try:
        rawValueInt = rawValue[1] * 255 + rawValue[0]
    except (TypeError, AttributeError):
        rawValueInt = 0

    soil_EC = (rawValueInt * 3.3) / (pow(2, 11) - 1)

    # Est ce que cette conversion est bonne ??
    # TODO: convert raw(0 - 1771) to 0 to 10(mS / cm)

    return round(soil_EC* 10) / 10

def convert_SoilMoisture(rawValue):
    try:
        moisture = rawValue[1] * 255 + rawValue[0] * 1.0
    except (TypeError, AttributeError):
        moisture = 0

#    moisture = (moisture * 3.3) / (pow(2, 11) - 1)

    soilMoisture = 11.4293 + (0.0000000010698 * math.pow(moisture, 4.0) - 0.00000152538 * math.pow(moisture, 3.0) +  0.000866976 * math.pow(moisture, 2.0) - 0.169422 * moisture)

    soilMoisture = 100.0 * (0.0000045 * math.pow(soilMoisture, 3.0) - 0.00055 * math.pow(soilMoisture, 2.0) + 0.0292 * soilMoisture - 0.053)

    if soilMoisture < 0.0:
        soilMoisture = 0.0
    elif soilMoisture > 60.0:
        soilMoisture = 60.0

    return round(soilMoisture, 1)

def convert_Soil_RJ(rawValue): # Verifier quelle est la bonne vs les 2 avant
    try:
        rawValueInt = rawValue[1] * 255 + rawValue[0] * 1.0
    except (TypeError, AttributeError):
        rawValueInt = 0

    soilMoisture = 11.4293 + (0.0000000010698 * math.pow(rawValueInt, 4.0) - 0.00000152538 *
                              math.pow(rawValueInt, 3.0) + 0.000866976 * math.pow(rawValueInt, 2.0) - 0.169422 * rawValueInt)
    soilMoisture = 100.0 * (0.0000045 * math.pow(soilMoisture, 3.0) - 0.00055 *
                            math.pow(soilMoisture, 2.0) + 0.0292 * soilMoisture - 0.053)
    return round(soilMoisture)

def convert_Battery(rawValue):
    try:
        rawValueInt = rawValue[0]
    except (TypeError, AttributeError):
        rawValueInt = 0
    return rawValueInt


def convert_Name(rawValue):
    str1 = ''.join(chr(e) for e in rawValue)
    return str1


def convert_2Bytes(rawValue):
    try:
        rawValueInt = rawValue[1] * 255 + rawValue[0]
    except (TypeError, AttributeError):
        rawValueInt = 0
    return rawValueInt

########################
## Debut du programme ##
########################

if len(sys.argv) == 1:
    print(sys.argv[0], ",A0:14:3D:7D:77:26,data,0")
    print(sys.argv[0], "macAdd:[A0:14:xx:xx:xx:xx], action:[all|data|static|watering],FlowerPowerOrPot:[0|1], debug:[0|1],security:[low|medium|high],adapter:hci[0-9]")
    exit(0)

timeout = 20
adpater = "hci0"
security = "high"
flora_debug=0
FlowerPowerOrPot=0
flora_action="all"
mac_add = "A0:14:3D:7D:77:26"

# test des valeurs calibres, ne marche pas encore, mettre a 1 pour tester
flora_calibre="0"

if len(sys.argv) >= 7:
    adpater = sys.argv[6]
if len(sys.argv) >= 6:
    security = sys.argv[5]
if len(sys.argv) >= 5:
    flora_debug = sys.argv[4]
# 0 flower power, 1 pot"
if len(sys.argv) >= 4:
    FlowerPowerOrPot = sys.argv[3]
# Action: all, data, static
if len(sys.argv) >= 3:
    flora_action = sys.argv[2]
if len(sys.argv) >= 2:
    mac_add = sys.argv[1]

if flora_debug == "1":
    print("Fetching: ", mac_add," action: ",flora_action," FlowerPowerOrPot: ",FlowerPowerOrPot," debug:",FlowerPowerOrPot," security:",security," adpater:",adpater)



if flora_action == "all" or flora_action == "data":
    if flora_debug == "1":
        print("=============")
        print("Donnees de base :")
        print("=============")

    # Gestion de la temperature de la terre
    if FlowerPowerOrPot == "0":
        handlerd = "0x002d"
    else:
        handlerd = "0x0034"

    result_flora = read_ble(mac_add, handlerd, adpater, security)
    if flora_debug == "1":
        print("Soil Temperature brute:", result_flora)
    # avec convert_temperature 21.5, app: 22/23 live
    temperature_terre = convert_temperature(result_flora)
    if flora_debug == "1":
        print(" -->Soil Temperature:", temperature_terre)

    # Gestion de la temperature de l'air
    if FlowerPowerOrPot == "0":
        handlerd = "0x0031"
    else:
        handlerd = "0x0037"

    result_flora = read_ble(mac_add, handlerd, adpater, security)
    if flora_debug == "1":
        print("Air Temperature brute:", result_flora)
    temperature_air = convert_temperature(result_flora)
    if flora_debug == "1":
        print(" -->Air Temperature:", temperature_air)

    antoine = 8.07131 - (1730.63 / (233.426 + temperature_terre));
    last_pressure = math.pow(10, antoine - 2)
    # TODO: convert raw(0 - 1771) to 0 to 10(mS / cm)
    # avec convert_Soil: 19,4% avec cette formule, app: 20/21%
    #print " -->Last Pressure:", last_pressure

    # Gestion de la temperature de l'air calibre
    if FlowerPowerOrPot == "0":
        handlerd = "0x0043"
    else:
        handlerd = "0x0044"

    result_flora = read_ble_float(mac_add, handlerd, adpater, security)
    if flora_debug == "1":
        print("Air Temperature brute calibre:", result_flora)
    temperature_air_calibre = result_flora
    if flora_debug == "1":
        print(" -->Air Temperature calibre:", temperature_air_calibre)

    # Gestion des LUX
    handlerd = "0x0025"
    result_flora = read_ble(mac_add, handlerd, adpater, security)
    if flora_debug == "1":
        print("Lux brute:", result_flora)
    # 0.1 app: 0 moyenne 976 (semble bien etre la version live)
    lux = convert_Lux(result_flora)
    if flora_debug == "1":
        print(" -->Lux:", lux)

    # Gestion de Soil ElectricalConductivity
    if FlowerPowerOrPot == "0":
        handlerd = "0x0029"
    else:
        handlerd = "0x0031"

    soil_EC_brut = read_ble(mac_add, handlerd, adpater, security)
    if flora_debug == "1":
        print("Soil EC brut:", soil_EC_brut)
    soilEC = convert_SoilEC(soil_EC_brut)
    soil_moisture_RJ = convert_Soil_RJ(soil_EC_brut)
    if flora_debug == "1":
        print(" -->Soil EC:", soilEC, " (comment utiliser ?)")
        print(" -->relativeHumidity RJ:", soil_moisture_RJ)

    # Gestion de Soil Moisture
    if FlowerPowerOrPot == "0":
        handlerd = "0x0035"
    else:
        handlerd = "0x003a"

    soil_moisture_brut = read_ble(mac_add, handlerd, adpater, security)
    if flora_debug == "1":
        print("Soil Moisture brut: ", soil_moisture_brut)
    soil_moisture = convert_SoilMoisture(soil_moisture_brut)
    if flora_debug == "1":
        print(" -->relativeHumidity-Soil Moisture brut:", soil_moisture)

    # Gestion de Soil Moisture calibre (VWC)
    if FlowerPowerOrPot == "0":
        handlerd = "0x003f"
    else:
        handlerd = "0x0041"

    soil_moisture_brut_calibre = read_ble_float(mac_add, handlerd, adpater, security)
    if flora_debug == "1":
        print("Soil Moisture brut calibre: ", soil_moisture_brut_calibre)
    soil_moisture_calibre = soil_moisture_brut_calibre
    if flora_debug == "1":
        print(" -->relativeHumidity-Soil Moisture brut calibre:", soil_moisture_calibre)

    # Gestion de DLI calibre
    if FlowerPowerOrPot == "0":
        handlerd = "0x0047"
    else:
        handlerd = "0x0047" ## TODO trouver handle

    DLI_calibre = read_ble_float(mac_add, handlerd, adpater, security)
    if flora_debug == "1":
        print("DLI calibre: ", DLI_calibre)

if flora_action == "all" or flora_action == "static":
    # Gestion de la batterie
    if FlowerPowerOrPot == "0":
        handlerd = "0x004c"
    else:
        handlerd = "0x004b"

    result_flora = read_ble(mac_add, handlerd, adpater, security)
    if flora_debug == "1":
        print("Batterie brut: ", result_flora)
    # 30 , app: courbe semble etre vers 35-37
    batterie = convert_Battery(result_flora)
    if flora_debug == "1":
        print(" -->Batterie %: ", batterie)

    # Gestion du nom
    if FlowerPowerOrPot == "0":
        handlerd = "0x0003"
    else:
        handlerd = "0x0070"

    result_flora = read_ble(mac_add, handlerd, adpater, security)
    if flora_debug == "1":
        print("Name brut: ", result_flora)
    Name = convert_Name(result_flora)
    if flora_debug == "1":
        print(" -->Name: ", Name)


if FlowerPowerOrPot == "1":
    if flora_action == "all" or flora_action == "watering":
        if flora_debug == "1":
            print("=============")
            print("Donnees de watering :")
            print("=============")


        # Water Tank Level
        # 0x008b
        handlerd = "0x008b"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        if flora_debug == "1":
          print("Water Tank Level Brut: ", result_flora)
        batterie = convert_Battery(result_flora)
        if flora_debug == "1":
            print(" -->Water Tank Level: ", batterie)

        # Watering Mode
        handlerd = "0x0090"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        if flora_debug == "1":
            print("Watering Mode: ", result_flora)
        batterie = convert_Battery(result_flora)
        if flora_debug == "1":
            print(" -->Watering Mode: ", batterie)


        # Watering Status
        handlerd = "0x009a"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        if flora_debug == "1":
            print("Watering Status: ", result_flora)
        batterie = convert_Battery(result_flora)
        if flora_debug == "1":
            print(" -->Watering Status: ", batterie)

        if flora_debug == "1":
            print("=============")
            print("Autres donnees :")
            print("=============")



        """
        handlerd = "0x0028"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        print "Inconnu brut: ", result_flora
        # 30 , app: courbe semble etre vers 35-37
        resultat = convert_Battery(result_flora)
        print " -->Inconnu : ", resultat
        
        handlerd = "0x002b"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        print "Inconnu brut: ", result_flora
        # 30 , app: courbe semble etre vers 35-37
        resultat = convert_Battery(result_flora)
        print " -->Inconnu : ", resultat
        
        handlerd = "0x002e"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        print "Inconnu brut: ", result_flora
        # 30 , app: courbe semble etre vers 35-37
        resultat = convert_Battery(result_flora)
        print " -->Inconnu : ", resultat
        
        
        handlerd = "0x0016"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        print "Serial Number: ", result_flora
        # 30 , app: courbe semble etre vers 35-37
        resultat = convert_Name(result_flora)
        print " -->Serial Number : ", resultat.encode('utf-8')
        
        handlerd = "0x0012"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        print "System ID: ", result_flora
        # 30 , app: courbe semble etre vers 35-37
        resultat = convert_Battery(result_flora)
        print " -->System ID : ", resultat
        
        
        handlerd = "0x0005"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        print "Appereance: ", result_flora
        # 30 , app: courbe semble etre vers 35-37
        resultat = convert_Battery(result_flora)
        print " -->Appereance : ", resultat
        
        
        # Livemeasure Period
        handlerd = "0x003d"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        print "Live measure Period Brut: ", result_flora
        # 30 , app: courbe semble etre vers 35-37
        resultat = convert_Battery(result_flora)
        print " -->Live measure Period : ", resultat
        
        # Led state, value is 1 or 0
        handlerd = "0x003f"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        print "LED state: ", result_flora
        resultat = convert_Battery(result_flora)
        print " -->LED state : ", resultat

        """


        """
        # Couleur
        handlerd = "0x0072"
        result_flora = read_ble(mac_add, handlerd, adpater, security)
        print "Couleur brut: ", result_flora
        Name = convert_Name(result_flora)
        print " -->Couleur: ", Name
        
        """


if flora_debug == "1":
    if flora_action == "static" or flora_action == "all":
        print(" -->Name: ", Name)
        print(" -->Batterie %: ", batterie)
    if flora_action == "data" or flora_action == "all":
        print(" -->relativeHumidity:", soil_moisture)
        print(" -->relativeHumidity calibre:", soil_moisture_calibre)
        print(" -->relativeHumidity RJ:", soil_moisture_RJ)
        print(" -->Soil EC:", soilEC)
        print(" -->Lux:", lux)
        print(" -->Air Temperature:", temperature_air)
        print(" -->Air Temperature calibre:", temperature_air_calibre)
        print(" -->Soil Temperature:", temperature_terre)
    if FlowerPowerOrPot == 1:
        if flora_action == "watering" or flora_action == "all":
            print("watering TBD")

if flora_debug == "0":
    if flora_action == "data":
        print "{\"Soil_moisture\":", soil_moisture_calibre, ",\"Fertility\":", soilEC, ",\"Lux\":", DLI_calibre, ",\"Air_Temperature\":", temperature_air_calibre, ",\"Soil_Temperature\":", temperature_terre,"}"

    if flora_action ==  "static":
        print "Name: ", Name, ",Batterie: ", batterie
    if FlowerPowerOrPot == "0":
        if flora_action == "all":
            print "Name: ", Name, ",Batterie: ", batterie, ",Soil_moisture:", soil_moisture_RJ, ",Fertility:", soilEC, ",Lux:", lux,",Air_Temperature:", temperature_air,",Soil_Temperature:", temperature_terre
    if FlowerPowerOrPot == "1":
        if flora_action == "all":
            print "Name: ", Name, ",Batterie: ", batterie, ",Soil_moisture:", soil_moisture_RJ, ",Fertility:", soilEC, ",Lux:", lux, ",Air_Temperature:", temperature_air, ",Soil_Temperature:", temperature_terre
            # TODO print watering values at the end
        if flora_action == "watering":
            print "watering TBD"
            # TODO print watering values
