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
        <div class="form-group"> <br>
           <a href=https://jeedom-plugins-extra.github.io/plugin-MiFlora/fr_FR target="_blank"><font size="+1"><center>Cliquer pour voir la documentation du plugin</center></font></a>
       </div>
	</span>
        <div class="form-group"> <br>

          <label class="col-lg-4 control-label">{{frequence de recuperation des données}}</label>
          <div class="col-lg-2">
              <select id="frequence" class="configKey form-control"  data-l1key="frequence" >
              <option value="5mn">{{1/12.0}}</option>
              <option value="10mn">{{1/6.0}}</option>
              <option value="15mn">{{0.25}}</option>
              <option value="30mn">{{0.5}}</option>
              <option value="1h">{{1}}</option>
              <option value="2h">{{2}}</option>
              <option value="3h">{{3}}</option>
              <option value="4h">{{4}}</option>
              <option value="5h">{{5}}</option>
              <option value="6h">{{6}}</option>
              <option value="7h">{{7}}</option>
              <option value="8h">{{8}}</option>
              <option value="9h">{{9}}</option>
              <option value="10h">{{10}}</option>
              <option value="11h">{{11}}</option>
              <option value="12h">{{12}}</option>
                </select>
          </div>
        </div>

        <div class="form-group"> <br>
          <label class="col-lg-4 control-label">{{niveau de sécurité Bluetooth (high)}}</label>
          <div class="col-lg-2">
              <select id="seclvl" class="configKey form-control"  data-l1key="seclvl" >
              <option value="low">{{low}}</option>
              <option value="medium">{{medium}}</option>
              <option value="high">{{high}}</option>
                </select>
          </div>
        </div>

        <div class="form-group"> <br>
          <label class="col-lg-4 control-label">{{adaptateur Bluetooth (hci0)}}</label>
          <div class="col-lg-2">
              <select id="adapter" class="configKey form-control"  data-l1key="adapter" >
              <option value="hci0">{{hci0}}</option>
              <option value="hci1">{{hci1}}</option>
              <option value="hci2">{{hci2}}</option>
              <option value="hci3">{{hci3}}</option>
              <option value="hci4">{{hci4}}</option>
              <option value="hci5">{{hci5}}</option>
                </select>
          </div>
        </div>

          <div class="form-group"> <br>

              <label class="col-lg-4 control-label">{{Équipement local ou déporté ?}}</label>
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
                <input class="configKey form-control"  data-l1key="password" type="password" placeholder="{{saisir le mot de passe}}" />
            </div>
            </div>
            </div>

</div>
  </fieldset>
</form>
