

import argparse
import sys
from bluepy.btle import Scanner, DefaultDelegate




class ScanDelegate(DefaultDelegate):
    def __init__(self):
        DefaultDelegate.__init__(self)

    def handleDiscovery(self, dev, isNewDev, isNewData):
        if isNewDev:
 #           print "Discovered device", dev.addr
            dummy = 1



#   --------    progarmme principal    -----------------

parser = argparse.ArgumentParser(description='Miflora Daemon for Jeedom plugin')
parser.add_argument("--id", help="id antenne", type=str)
parser.add_argument("--device", help="Device", type=str)
parser.add_argument("--antenne", help="Nom de l antenne", type=str)
parser.add_argument("--timeout", help="delai de scan", type=str)
args = parser.parse_args()

#print "device : " + args.device
#print "antenne : "  + args.antenne




# recupere le numero du controleur
if args.device == "hci0" :
    index = 0
elif args.device == "hci1" :
    index = 1
elif args.device == "hci2" :
    index = 2
elif args.device == "hci3" :
    index = 3
else:
    print "device error (hci0 a hci3) \n"

scanner = Scanner(index).withDelegate(ScanDelegate())

try:
    devices = scanner.scan(int(args.timeout))
except:
    print("Erreur dans le scan le controleur est probablement occupe essayer un autre ")
else:
    file= "/tmp/MiFlora_rssi_" + args.antenne + ".dat"
    #print "file : " + file
    file_out = open (file,"w")

    for dev in devices:
    #    print "%s,%s,(%s),%d;" % (args.antenne,dev.addr, dev.addrType, dev.rssi)
        for (adtype, desc, value) in dev.getScanData():
            if (desc == "Complete Local Name") :
                print "%s;%s;%s;%s;%d;%s," % (args.id,args.antenne, dev.addr, dev.addrType, dev.rssi,value)
                file_out.write("%s;%s;%s;%s;%d;%s\n" % (args.id,args.antenne, dev.addr, dev.addrType, dev.rssi,value))

    file_out.close()
finally:
    sys.exit();


