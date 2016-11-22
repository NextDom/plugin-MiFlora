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
	  <br>
	  cette version ne recupere pas la config ci dessous, elle est hardcode dans core/class/MiFlora.class.php, il faut la changer et mettre vos valeurs
	</span>
        <div class="form-group"> <br>
            <label class="col-lg-4 control-label">{{Remote IP}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control"  data-l1key="configuration" data-l2key="addressip" type="text" placeholder="{{saisir l'adresse IP}}" />
            </div>
            <label class="col-lg-4 control-label">{{Port SSH}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control"  data-l1key="configuration" data-l2key="portssh" type="text" placeholder="{{saisir le port SSH (22)}}" />
            </div>
            <label class="col-lg-4 control-label">{{User Id}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control"  data-l1key="configuration" data-l2key="user" type="text" placeholder="{{saisir le login}}" />
            </div>
            <label class="col-lg-4 control-label">{{Password}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control"   data-l1key="configuration" data-l2key="password" type="password" placeholder="{{saisir le password}}" />
            </div>

</div>
  </fieldset>
</form>

<script>

        function MiFlora_postSaveConfiguration(){
          $.ajax({// fonction permettant de faire de l'ajax
          type: "POST", // methode de transmission des données au fichier php
          url: "plugins/MiFlora/core/ajax/MiFlora.ajax.php", // url du fichier php
          data: {
            action: "postSave",
          },
          dataType: 'json',
          error: function (request, status, error) {
            handleAjaxError(request, status, error);
          },
          success: function (data) { // si l'appel a bien fonctionné
          if (data.state != 'ok') {
            $('#div_alert').showAlert({message: data.result, level: 'danger'});
            return;
          }
        }
      });
    }

</script>






