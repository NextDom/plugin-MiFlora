<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
# how to test this file:
# su --shell=/bin/bash - www-data -c "/usr/bin/php /var/www/html/core/class/../../core/php/jeePlugin.php plugin_id=MiFlora function=update callInstallFunction=1"
# su --shell=/bin/bash - pi -c "/usr/bin/php /var/www/html/core/class/../../core/php/jeePlugin.php plugin_id=MiFlora function=update callInstallFunction=1"
function MiFlora_install() {
    log::add('MiFlora', 'info', 'config - install started');
    MiFlora_update() ;
}

function MiFlora_update() {
    log::add('MiFlora', 'info', 'config - update started');

    if (config::byKey('frequence', 'MiFlora') == ""){
        config::save('frequence', '0', 'MiFlora');
    }
    if (config::byKey('maitreesclave', 'MiFlora') == "") {
        config::save('maitreesclave', 'local' ,'MiFlora');
    }
    if (config::byKey('adapter', 'MiFlora') == "") {
        config::save('adapter', 'hci0', 'MiFlora');
    }
    if (config::byKey('seclvl', 'MiFlora') == "") {
        config::save('seclvl', 'low', 'MiFlora');
    }

    // Set default values for each existing equipments
    foreach (eqLogic::byType('MiFlora') as $eqLogic) {
      $frequenceItem = $eqLogic->getConfiguration('frequence');
      if ($frequenceItem == ""){
        $eqLogic->setConfiguration('frequence','0'); //default value in config::
      }
      $eqLogic->save();
      log::add('MiFlora', 'info', '$frequenceItemInstall: '.$frequenceItem);

    }

}

function MiFlora_remove() {
    log::add('MiFlora', 'info', 'config - remove started');
    // config::remove('frequence', 'MiFlora');
}

?>
