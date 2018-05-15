# Changelog

### Version 3.0.0 - May 2018 - under development
* Management of remote antennas
   - Possibility of creating antennas
   - Possibility for each object to specify by which antenna it will be read
   -  Warning:
        - the plugin now has dependencies to launch, they are only useful to manage the migration to version 3.0.0 for current users if they do not use the Market for the update. For all other cases, the dependencies are not useful.
        - it may be necessary to disable it and then activate it to update the new fields, the data is retained during this process.
* Health tab allowing to see in a synthetic way the state of MiFlora.
* Adding default values ​​for the low battery alert.
* Manage the Parrot flower in a dedicated Python script for future integration into the plugin.
* Added the refresh functionality and transition from the minimum of 5 to 15 minutes.
    - This feature can be used from a scenario or by clicking on the widget in desktop mode.
    - Be careful to put a frequency of at least 15 minutes for your existing objects.
