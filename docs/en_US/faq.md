# FAQ

### What is the difference between MiFlora and BLEA
> MiFlora only manages plants, BLEA is a plugin for all Bluetooth LE objects, so it is much more complex,
it needs dependencies, has a daemon system, it is suitable to handle a multitude of Bluetooth LE object types
but needs more monitoring and maintenance, mostly demons and dependencies when updating

### Does this plugin rely on third-party APIs?

> The plugin uses Bluetooth to retrieve information from MiFlora.
You must install Bluetooth and make sure that `gatttool -b macAddMiFlora --char-read -a 0x35` is running on the target device.

### Does this plugin monopolize Bluetooth?

> Not at all, he needs Bluetooth for each statement, see next question for more details on the number of readings per day.


### How many times a day are the measurements retrieved?

> It is defined in the global configuration of the plugin, for all objects: every 15 minutes to every 12 hours.
It is possible to configure a different frequency for each MiFlora, `default` allows to use the global frequency.

> I use the modulo of the current time with the frequency entered in parameter. +
Warning: in debug mode, data is retrieved continuously regardless of the configuration.

> The static information (battery, device name, firmware version) is retrieved every 12 hours: at midnight and noon.


### Which firmware version is this plugin compatible with?

> It is compatible with all versions known to date (2.9.2) since version 1.0 of the plugin.


### I have a RPI3, I had to disable the internal Bluetooth to not have interference with the Zwave (razberry). Should I always keep the internal Bluetooth off to solve this problem? If not, does any BT USB stick fit the MiFlora and RPI3?

> Dans ce cas il faut prendre un dongle BLE. Le problème avec le razberry c'est seulement si on utilise le contrôleur interne.


### I wish to contribute to the improvement of this plugin, is it possible?

> Bien sur, le code est sur GitHub : rjullien/plugin-MiFlora, vous pouvez soumettre des pull requests.

### gatttool is unstable and hangs on RPI

> There is a lot of configuration that can cause this problem. With Pixel you have to be careful to have a single bluetooth manager.
BlueZ is incompatible with blueman (sudo apt-get remove blueman)

### The plugin works well: what can I do with it?

> Humidity, fertility, brightness and temperature values ​​are accessible from scenarios.

> It is possible to read these values, to compare them to a threshold and to warn if the threshold is exceeded, for example to water a plant.

> Alerts can be given by text to speech (plugin playTTS for example), by notification on smartphone (pushbullet plugin), by SMS ...

> Thresholds can be found using Xiaomi or Parrot database or a threshold between 14 and 16 seems to be suitable for a majority of houseplants.

> It is also possible to regulate an automatic watering, MiFlora seems to be resistant to bad weather
