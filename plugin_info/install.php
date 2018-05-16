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

    $sql = file_get_contents(dirname(__FILE__) . '/install.sql');
    log::add('MiFlora', 'info', 'sql - '.$sql);
    DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
    foreach (MiFlora::byType('MiFlora') as $miflora) {
        $miflora->save();
    }
    if (config::byKey('maitreesclave', 'MiFlora') == "deporte"){
        $remote = MiFlora_remote::byRemoteName("deporte");
        if ($remote == "") {
            log::add('MiFlora', 'info', 'config - remote not created - migrate existing remote collection to antenna');
            $remoteA = new MiFlora_remote();
            $remoteA->setRemoteName('deporte'  );
            $remoteA->setConfiguration('remoteIp', config::byKey('addressip', 'MiFlora'));
            $remoteA->setConfiguration('remotePort',config::byKey('portssh', 'MiFlora'));
            $remoteA->setConfiguration('remoteUser',config::byKey('user', 'MiFlora'));
            $remoteA->setConfiguration('remotePassword',config::byKey('password', 'MiFlora'));
            $remoteA->setConfiguration('remoteDevice',config::byKey('adapter', 'MiFlora'));
            $remoteA->save();

            // log::add('MiFlora', 'info', 'config - remote not created '. serialize($remoteA));

            $antenneAncienneMethode = "deporte";
        } else {
            log::add('MiFlora', 'info', 'config - antenna exist');
            $antenneAncienneMethode = "local";
            // $remote -> remove();
        }
        // log::add('MiFlora', 'info', 'config - migrate existing remote collection to antenna');
    } else {
        $antenneAncienneMethode = "local";
    }

    # TODO - after Beta - Sept 18
    # Effacer maitreesclave, addressip, portssh, user, password

    if (config::byKey('frequence', 'MiFlora') == ""){
        config::save('frequence', '1', 'MiFlora');
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
    if (config::byKey('battery_type', 'MiFlora') == "") {
        config::save('battery_type', '1x3V CR2032', 'MiFlora');
    }

    // Set default values for each existing equipments
    foreach (eqLogic::byType('MiFlora') as $eqLogic) {
        $frequenceItem = $eqLogic->getConfiguration('frequence');
        if ($frequenceItem == "") {
            $frequenceItem = 0;
            $eqLogic->setConfiguration('frequence', $frequenceItem); //default value in config::
            log::add('MiFlora', 'info', 'frequenceItem-Install: '.$eqLogic->getHumanName(false, false) . ' : ' . $frequenceItem);
        }

        $antenne = $eqLogic->getConfiguration('antenna');
        $real_antenne = $eqLogic->getConfiguration('real_antenna');
        if ($antenne == "") {
            $antenne = $antenneAncienneMethode;
            $eqLogic->setConfiguration('antenna', $antenne); //default value in config::
            log::add('MiFlora', 'info', '$antenneItem-Install: '.$eqLogic->getHumanName(false, false) . ' : ' .$antenne);
        }
        if ($real_antenne == "") {
            $eqLogic->setConfiguration('real_antenna', 'local'); //default value in config::
            log::add('MiFlora', 'info', '$antenneItem-Install: '.$eqLogic->getHumanName(false, false) . ' : ' .$antenne);
        }

        if ($eqLogic->getConfiguration('battery_danger_threshold') == "") {
            $eqLogic->setConfiguration('battery_danger_threshold', '10');
            log::add('MiFlora', 'info', 'battery_danger_threshold-Install: 10');
        }
        if ($eqLogic->getConfiguration('battery_warning_threshold') == "") {
            $eqLogic->setConfiguration('battery_warning_threshold', '15');
            log::add('MiFlora', 'info', 'battery_warning_threshold-Install: 15');
        }
        if ($eqLogic->getConfiguration('devicetype') == "") {
            $eqLogic->setConfiguration('devicetype', 'MiFlora');
            log::add('MiFlora', 'info','set device type to MiFlora');
        }

        ## Cree la commande refresh pour les objets existants
        $refresh = $eqLogic->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = new MiFloraCmd();
            $refresh->setLogicalId('refresh');
            $refresh->setIsVisible(1);
            $refresh->setName(__('Rafraîchir', __FILE__));
            $refresh->setType('action');
            $refresh->setSubType('other');
            $refresh->setEqLogic_id($this->getId());
            $refresh->save();
            log::add('MiFlora', 'info', 'Refresh-Install: add ' . $eqLogic->getHumanName(false, false));
        }

        $eqLogic->save();

    }

}
function MiFlora_remove() {
    log::add('MiFlora', 'info', 'config - remove started');
    // config::remove('frequence', 'MiFlora');
}


