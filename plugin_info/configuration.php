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
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>

<form class="form-horizontal">
    <fieldset>
    	<span>
	  Entrer la config d'un device distant ou la config du device ou tourne jeedom, je ne gere que l access par ssh actuellement
	  <br>
	  il faut installer le bluetooth et s assurer que <b>gatttool -b macAddMiFlora --char-read -a 0x35</b> fonctionne sur le device cible
	</span>
        <div class="form-group"> <br>

          <label class="col-lg-4 control-label">{{frequence de recuperation des données}}</label>
          <div class="col-lg-2">
              <select id="frequence" class="configKey form-control"  data-l1key="frequence" >
              <option value="1">{{1}}</option>
              <option value="2">{{2}}</option>
              <option value="3">{{3}}</option>
              <option value="4">{{4}}</option>
              <option value="5">{{5}}</option>
              <option value="6">{{6}}</option>
              <option value="7">{{7}}</option>
              <option value="8">{{8}}</option>
              <option value="9">{{9}}</option>
              <option value="10">{{10}}</option>
              <option value="11">{{11}}</option>
              <option value="12">{{12}}</option>
                </select>
          </div>
        </div>
          <div class="form-group"> <br>

              <label class="col-lg-4 control-label">{{Equipement local ou déporté ?}}</label>
              <div class="col-lg-2">
                <select id="maitreesclave" class="configKey form-control" data-l1key="maitreesclave"
                onchange="if(this.selectedIndex == 1) document.getElementById('deporte').style.display = 'block';
                else document.getElementById('deporte').style.display = 'none';">
                <option value="local">{{Local}}</option>
                <option value="deporte">{{Déporté}}</option>
              </select>
            </div>

            <div id="deporte">
            <div class="form-group"> <br>


            <label class="col-lg-4 control-label">{{Remote IP}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control"  data-l1key="addressip" type="text" placeholder="{{saisir l'adresse IP}}" />
            </div>

            <label class="col-lg-4 control-label">{{Port SSH}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control"  data-l1key="portssh" type="text" placeholder="{{saisir le port SSH (22)}}" />
            </div>

            <label class="col-lg-4 control-label">{{User Id}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control"  data-l1key="user" type="text" placeholder="{{saisir le login}}" />
            </div>

            <label class="col-lg-4 control-label">{{Password}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control"  data-l1key="password" type="password" placeholder="{{saisir le password}}" />
            </div>
            </div>
            </div>

</div>
  </fieldset>
</form>
