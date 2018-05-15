
### Version 2.0.4 - April 14, 2018
* Refresh management by object:
    * Refreshing by object complements the global one in the plugin configuration
    * Refreshing by object has an additional value *default*, in which case the global value is taken into account
    * Each MiFlora has its refresh rate which replaces the global value when it is not at the value *default*
    * In debug mode, all objects are read every minute regardless of these values

### Version 2.0.3 - April 6, 2018
* Deleting the PayPal link in the documentation so that the plugin is no longer obsolete

### Version 2.0.2 - April 2, 2018
* Global refresh management less than one hour
* Improved documentation and Readme

### Version 2.0.1: March 2018

* Code improvement without functional change
  * Moving the python script in resources
  * setting up travis-ci
  * lint python warnings fix
  * Correction of the documentation

### Version 2.0.0: March 2018

* Migration of the documentation in markdown format and integration into the Jeedom-Plugins-Extra template

### Version 1.2.5: May 31, 2017

* fixed a bug on fertility (recovery of 2 bytes instead of one)
* Changing the icon to match Jeedom chart

### Version 1.2.4: March 18, 2017

* Inversions of the change log for more readability
* Firmware Management 2.9.2 - Inversion of Test Only 2.6.2 is managed with the old method

### Version 1.2.3: January 16

* Improved management of reading errors

### Version 1.2.2:

* Fixed python script bug

### Version 1.2.1: January 7, 2017

* Negative temperature correction (frederic34 pull request)

### Version 1.2: January 5, 2017

* multiple spelling corrections
* migration of data recovery to the python script
* FAQ improvement

### Version 1.1: December 11, 2016

* Use of the hci and sec-level parameter for firmware 2.6.6 and 2.7.0. (modification of the python script)

### Version 1.0.1: December 11, 2016

* Fixed bug for new firmware in local mode

### Version 1.0: December 10, 2016

* 2.6.6 and 2.7.0 firmware support in local and remote mode
* Choice of the hci in the config, very useful if the hci0 is assigned to an exclusive plugin or if it does not manage the Bluetooth BLE
* Choice of the Bluetooth security level to solve the problems of some dongles that do not manage the high level
* the hci and security level is not taken into account for the firmware 2.6.6 and 2.7.0. (modification of the python script not yet tested)

### Version 0.8: December 9, 2016

* Display of Configuration items in read only field.
* Desktop and mobile dedicated widget.
* adding date of last collection
* force the recovery of the firmware if it is empty
* Debug mode causes a collection of data every minute, be careful not to leave it permanently especially if you are on an SD card
* Set new devices visible and enabled by default
* Addition of the unit for 'fertility', this field actually measures the conductivity which allows to deduce the fertility of the soil

### Versions 0.1 ... 0.7: 24 November 2016 - 2 December 2016

* Initial version of the plugin.

### Ideas for the following versions:

* Manage other brands than Xiaomi for plants (Parrot)
* Manage multiple remote devices and associate them with devices to increase the Bluetooth port by adding multiple receivers
* Add the config test: hci and sec-level must be filled
* Choice of error log or info in case of connection problem after 4 tests
* Test that macadd is <> between Equipments (glue copy error)
* Test the Bluetooth status and restart it if there is a problem (sudo hciconfig hci0 down, sudo hciconfig hci0 up) for example the error error error: Connection refused (111)
* Cause data recovery right after adding the hardware? or a command to force the update
* Button to detect a new MiFlora (avoid finding the address by hand)

This list of questions comes primarily from your questions on the forum's MiFlora thread.
