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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/MiFlora.class.php';



class MiFloraCmd extends cmd
{
    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */


    
    public function execute($_options = null)
    {
     log::add('MiFlora', 'info', 'debut refresh ' . $_options['message']);
      if ($this->getType() != 'action') {
			return;
		}
        $miflora = new MiFlora() ;
    	log::add('MiFlora', 'debug', 'Commande recue : ' . $_options['message']);
		$eqLogic = $this->getEqLogic();
		if ($this->getLogicalId() == 'refresh' ){
	        $adapter = config::byKey('adapter', 'MiFlora');
            $seclvl = config::byKey('seclvl', 'MiFlora'); 		
		    $macAdd = $eqLogic->getConfiguration('macAdd');
            log::add('MiFlora', 'info', 'refresh mi flora mac add:' . $macAdd);
            $FirmwareVersion = $eqLogic->getConfiguration('firmware_version');
            $MiFloraBatteryAndFirmwareVersion = '';
            $MiFloraNameString = '';
            $MiFloraName = '';
            $battery = -1;
            $miflora->getMiFloraStaticData($macAdd, $MiFloraBatteryAndFirmwareVersion, $MiFloraNameString, $adapter, $seclvl);
            $miflora->traiteMiFloraBatteryAndFirmwareVersion($macAdd, $MiFloraBatteryAndFirmwareVersion, $battery, $FirmwareVersion);
            $miflora->traiteMiFloraName($macAdd, $MiFloraNameString, $MiFloraName);
            log::add('MiFlora', 'debug', 'refresh  '.$macAdd.' , '.$battery.' , '.$FirmwareVersion.' , '.$MiFloraName);
            $eqLogic->updateStaticData($macAdd, $battery, $FirmwareVersion, $MiFloraName);
            $tryGetData = 0;
            $MiFloraData = '';
            $loopcondition = true;
            while ($loopcondition) {
                    if ($tryGetData > 3) { // stop after 4 try
                        break;
                    }
                    if ($tryGetData > 0) {
                       log::add('MiFlora', 'info', 'mi flora data for ' . $macAdd . ' is empty or null, trying again, nb retry:' . $tryGetData);
                    }
                    $miflora->getMesure($macAdd, $MiFloraData, $FirmwareVersion, $adapter, $seclvl);
                    log::add('MiFlora', 'debug', 'mi flora data:' . $MiFloraData . ':');
                    $tryGetData++;
                    $miflora->traiteMesure($macAdd, $MiFloraData, $temperature, $moisture, $fertility, $lux);
                    if ($MiFloraData == '' or ($temperature == 0 and $moisture == 0 and $fertility == 0 and $lux == 0)) {
                        // wait 10 s hopping it'll be better ...
                        log::add('MiFlora', 'info', 'wait 10 s hopping it ll be better ...');
                        sleep(10);
                    } else {
                        $loopcondition = false;
                    }
                }
               if ($MiFloraData == '' or ($temperature == 0 and $moisture == 0 and $fertility == 0 and $lux == 0)) {
                    message::add('MiFlora','refresh update failed check module '.$macAdd ) ;
                    log::add('MiFlora', 'debug', 'refresh error '.$macAdd.' , '.$temperature.' , '.$moisture.' , '.$fertility.' , '.$lux);
                    log::add('MiFlora', 'warning', 'mi flora refresh data is empty, retried ' . $tryGetData . ' times, stop');

				} else {
                    log::add('MiFlora', 'debug', 'refresh '.$macAdd.' , '.$temperature.' , '.$moisture.' , '.$fertility.' , '.$lux);
                    $eqLogic->updateJeedom($macAdd, $temperature, $moisture, $fertility, $lux);
                    $eqLogic->refreshWidget();
                    log::add('MiFlora','debug','fin de refresh ok ');
                }				
		
		}
		
        return true;
    }

}

