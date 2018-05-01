# Configuration

The configuration part of the plugin allows:

* to choose the frequency of MiFlora information readings every 5, 10, 15, 30 minutes and from every hour up to every 12 hours,
* to choose the dongle / Bluetooth port (**_hci0_** in general, `hciconfig` allows to list those available on your system),
* to choose the security level of the Bluetooth communication (`high` unless it is not supported by your system),
* to choose between a local mode and a remote mode.

For each device, you must enter the Bluetooth address of the device.

It is possible to find this one using the commands:

```
bluetoothctl
scan on
```

The MiFlora addresses appear as below:
```
[NEW] Device C4: 7C: 8D: xx: xx: xx Flower mate
[NEW] Device C4: 7C: 8D: xx: xx: xx Flower care
scan off
quit
```
The reading frequency of the data is by default that of the global configuration. It can be changed for each device.

### Remote use

It is possible that Jeedom pilots a remote device that will handle Bluetooth communication with MiFlora.

In this case, you must enter the SSH connection parameters between Jeedom and this remote device:

Ticking the remote box activates the configuration section, which allows you to enter an IP address, a port, a user name and the associated password for the remote device.
The plugin will then connect in SSH to the previously entered IP and retrieve the information of MiFlora in Bluetooth thanks to the command `gatttools`

No Jeedom is needed on the remote device.

### Prerequisites

You have to install the Bluetooth and make sure that `gatttool --device = hci0 -b _macAddMiFlora_ --char-read -a 0x35 --sec-level = high` works on the target device (depending on local or remote choice) .

### Advanced configuration

**_hci:_** allows you to choose the Bluetooth dongle for those who have more than one.

**_security level:_** allows to choose the level of Bluetooth security, `high` seems fine in the majority of cases, however changing the security level seems to solve some connection problems.

### Debug mode

The debug mode is used to run constantly (every minute) the MiFlora data recovery. It should be limited to debugging.
Leaving the debug mode permanently will affect the life of the storage medium, especially SD cards, and drain the battery of the device more quickly.
