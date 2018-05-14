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

//require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../../core/class/MiFlora.class.php';

if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}
$eqLogics = MiFlora::byType('MiFlora');
?>

<table class="table table-condensed tablesorter" id="table_ScanMiFlora">
	<thead>
		<tr>
			<th>{{Image}}</th>
			<th>{{Type}}</th>
			<th>{{Mac}}</th>
            <th>{{Antenne}}</th>
            <th>{{ RSSI }}</th>
        </tr>
	</thead>
	<tbody>
 <?php

     $rssi_value = MiFlora::get_MiFlora_rssi (8) ;

     foreach ($rssi_value as $i => $value) {
         log::add('MiFlora','debug','traitement de  ' . $i. " valeur " . $rssi_value[$i]) ;
         $found = 0;
         foreach (eqLogic::byType('MiFlora', true) as $mi_flora) {
             log::add('MiFlora','debug','comparaison avec  de  ' . $mi_flora->getConfiguration('macAdd') . ' pour device ' . $mi_flora->getHumanName(false, false)) ;
             $dev_value = explode(';',$rssi_value[$i]) ;
             if (strtolower($mi_flora->getConfiguration('macAdd') ) == strtolower($dev_value[2]) ){
                 $found = 1 ;
                 log::add('MiFlora','info','adresse deja la  ' .  $dev_value[2]) ;
                 break ;
             }
         }

        if ($found == 0 and ($dev_value[5] == "Flower care"  or $dev_value[5] == "Flower mate" or substr($dev_value[5],0,12) =="Flower power")){


             log::add('MiFlora','info','on a trouver une nouvelle mac adresse ' .  $dev_value[2]) ;

             $img = '<img class="lazy" src="plugins/MiFlora/plugin_info/MiFlora_icon.png" height="55" width="55"  />';

             echo '<tr><td>' . $img  . '</td>';

             echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $dev_value[5] . '</span></td>';

             echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $dev_value[2] . '</span></td>';

             echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $dev_value[1] . '</span></td>' ;

             echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $dev_value[4] . '</span></td>' ;

         }

     }







?>
	</tbody>
</table>
