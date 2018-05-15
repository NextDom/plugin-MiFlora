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
require_once __DIR__ . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/MiFlora.class.php';



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
        if ($this->getType() != 'action') {
            return;
        }
        if ($this->getLogicalId() == 'refresh') {
         log::add('MiFlora', 'debug', 'Commande recue : ' . $_options['message'].' logicalId: '.$this->getLogicalId());
         $processBattery = 0;
         $miflora = new MiFlora() ;
         $eqLogic = $this->getEqLogic();
         $devicetype = $eqLogic->getConfiguration('devicetype');
         log::add('MiFlora', 'debug', 'refresh - devicetype: ' . $devicetype);
         MiFlora::processOneMiFlora($eqLogic,$processBattery,$devicetype);
         log::add('MiFlora', 'debug', 'fin de refresh ok ');
         return true;
        }
    }

}

