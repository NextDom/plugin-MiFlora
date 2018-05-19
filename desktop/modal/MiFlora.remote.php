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

if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

$remotes = MiFlora_remote::all();
$id = init('id');
sendVarToJS('plugin', $id);
?>
<div id='div_MiFloraRemoteAlert' style="display: none;"></div>
<div class="row row-overflow">

	 <div class="col-lg-10 col-md-9 col-sm-8 col-xs-8 remoteThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
<legend><i class="fa fa-table"></i>  {{Mes Antennes}}</legend>

<div class="eqLogicThumbnailContainer">
	<div class="cursor MiFloraRemoteAction pull-left" data-action="add" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
     <center>
      <i class="fa fa-plus-circle" style="font-size : 9em;color:#94ca02;"></i>
    </center>
    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>{{Ajouter}}</center></span>
  </div>
  <?php
foreach ($remotes as $remote) {
	echo '<div class="eqLogicDisplayCard cursor pull-left" data-remote_id="' . $remote->getId() . '" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
    echo '<div class="cursor li_MiFloraRemote" data-MiFloraRemote_id="' . $remote->getId() . '"></div>';

	echo "<center>";
	echo '<img class="lazy" src="plugins/MiFlora/plugin_info/antenna.png" height="105" width="95" />';
	echo "</center>";
	echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02""><center>' . $remote->getRemoteName() . '</center></span>';
	echo '</div>';
}
?>
</div>
</div>

	<div class="col-lg-10 col-md-9 col-sm-8 col-xs-8 MiFloraRemote" style="border-left: solid 1px #EEE; padding-left: 25px;display:none;">
		<a class="btn btn-success MiFloraRemoteAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
		<a class="btn btn-danger MiFloraRemoteAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>

			<form class="form-horizontal">
					<fieldset>
						<legend><i class="fa fa-arrow-circle-left returnAction cursor"></i> {{Général}}</legend>
						<div class="form-group">
							<label class="col-sm-2 control-label">{{Nom}}</label>
							<div class="col-sm-3">
                                <input type="text" class="MiFloraRemoteAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="MiFloraRemoteAttr form-control" data-l1key="remoteName" placeholder="{{Nom de l'antenne}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">{{Ip}}</label>
							<div class="col-sm-3">
								<input type="text" class="MiFloraRemoteAttr form-control" data-l1key="configuration" data-l2key="remoteIp"/>
							</div>
							<label class="col-sm-1 control-label">{{Port}}</label>
							<div class="col-sm-3">
								<input type="text" class="MiFloraRemoteAttr form-control" data-l1key="configuration" data-l2key="remotePort"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">{{User}}</label>
							<div class="col-sm-3">
								<input type="text" class="MiFloraRemoteAttr form-control" data-l1key="configuration" data-l2key="remoteUser"/>
							</div>
							<label class="col-sm-1 control-label">{{Password}}</label>
							<div class="col-sm-3">
								<input type="password" class="MiFloraRemoteAttr form-control" data-l1key="configuration" data-l2key="remotePassword"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">{{Device}}</label>
							<div class="col-sm-3">
								<input type="text" class="MiFloraRemoteAttr form-control" data-l1key="configuration" data-l2key="remoteDevice" placeholder="{{ex : hci0}}"/>
							</div>
						</div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-3">
                                <label class="checkbox-inline"><input type="checkbox" class="MiFloraRemoteAttr" data-l1key="configuration" data-l2key="ScanMode" checked/>{{Mode auto et Scan }}</label>
                               </div>
                        </div>
                        <?php
                        if (method_exists( $id ,'dependancyRemote')){
                                echo '<label class="col-sm-2 control-label">{{Installation des dépendances}}</label>
						<div class="col-sm-3">
							<a class="btn btn-warning MiFloraRemoteAction" data-action="dependancyRemote"><i class="fa fa-spinner"></i> {{Lancer les dépendances}}</a>
						</div>
						<div class="col-sm-2">
							<a class="btn btn-success MiFloraRemoteAction" data-action="getRemoteLogDependancy"><i class="fa fa-file-text-o"></i> {{Log dépendances}}</a>
						</div>';
                            }
                            echo'</div>';
                         ?>

                    </fieldset>
				</form>
	</div>
</div>

<script>
	$('.MiFloraRemoteAction[data-action=add]').on('click',function(){
		$('.MiFloraRemote').show();
		$('.remoteThumbnailDisplay').hide();
		$('.MiFloraRemoteAttr').value('');
	});

	$('.eqLogicDisplayCard').on('click',function(){
		displayMiFloraRemote($(this).attr('data-remote_id'));
	});

	function displayMiFloraRemote(_id){
		$('.li_MiFloraRemote').removeClass('active');
		$('.li_MiFloraRemote[data-MiFloraRemote_id='+_id+']').addClass('active');
		$.ajax({
			type: "POST",
			url: "plugins/MiFlora/core/ajax/MiFlora.ajax.php",
			data: {
				action: "get_MiFloraRemote",
				id: _id,
			},
			dataType: 'json',
			async: true,
			global: false,
			error: function (request, status, error) {
			},
			success: function (data) {
				if (data.state != 'ok') {
					return;
				}
				$('.MiFloraRemote').show();
				$('.remoteThumbnailDisplay').hide();
				$('.MiFloraRemoteAttr').value('');
				$('.MiFloraRemote').setValues(data.result,'.MiFloraRemoteAttr');
			}
		});
	}

	function displayMiFloraRemoteComm(_id){
		$('.li_MiFloraRemote').removeClass('active');
		$('.li_MiFloraRemote[data-MiFloraRemote_id='+_id+']').addClass('active');
		$.ajax({
			type: "POST",
			url: "plugins/MiFlora/core/ajax/MiFlora.ajax.php",
			data: {
				action: "get_MiFloraRemote",
				id: _id,
			},
			dataType: 'json',
			async: true,
			global: false,
			error: function (request, status, error) {
			},
			success: function (data) {
				if (data.state != 'ok') {
					return;
				}
				$('.MiFloraRemote').show();
				$('.MiFloraRemoteAttrcomm').value('');
				$('.MiFloraRemote').setValues(data.result,'.MiFloraRemoteAttrcomm');
			}
		});
	}

	$('.li_MiFloraRemote').on('click',function(){
		displayMiFloraRemote($(this).attr('data-MiFloraRemote_id'));
		$('.remoteThumbnailDisplay').hide();
	});

	$('.returnAction').on('click',function(){
		$('.MiFloraRemote').hide();
		$('.li_MiFloraRemote').removeClass('active');
		setTimeout(function() { $('.remoteThumbnailDisplay').show() }, 100);
		;
	});

	$('.MiFloraRemoteAction[data-action=save]').on('click',function(){
		var MiFlora_remote = $('.MiFloraRemote').getValues('.MiFloraRemoteAttr')[0];
		$.ajax({
			type: "POST",
			url: "plugins/MiFlora/core/ajax/MiFlora.ajax.php",
			data: {
				action: "save_MiFloraRemote",
				MiFlora_remote: json_encode(MiFlora_remote),
			},
			dataType: 'json',
			error: function (request, status, error) {
				handleAjaxError(request, status, error,$('#div_MiFloraRemoteAlert'));
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_MiFloraRemoteAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('#div_MiFloraRemoteAlert').showAlert({message: '{{Sauvegarde réussie}}', level: 'success'});
				$('#md_modal').dialog('close');
				$('#md_modal').dialog({title: "{{Gestion des antennes bluetooth}}"});
				$('#md_modal').load('index.php?v=d&plugin=MiFlora&modal=MiFlora.remote&id=MiFlora').dialog('open');
				setTimeout(function() { displayMiFloraRemote(data.result.id) }, 200);

			}
		});
	});

    $('.MiFloraRemoteAction[data-action=remove]').on('click',function(){
        bootbox.confirm('{{Etês-vous sûr de vouloir supprimer cette Antenne ?}}', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "plugins/MiFlora/core/ajax/MiFlora.ajax.php",
                    data: {
                        action: "remove_MiFloraRemote",
                        id: $('.li_MiFloraRemote.active').attr('data-MiFloraRemote_id'),
                    },
                    dataType: 'json',
                    error: function (request, status, error) {
                        handleAjaxError(request, status, error,$('#div_MiFloraRemoteAlert'));
                    },
                    success: function (data) {
                        if (data.state != 'ok') {
                            $('#div_MiFloraRemoteAlert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        $('.li_MiFloraRemote.active').remove();
                        $('.MiFloraRemote').hide();
                        $('.remoteThumbnailDisplay').show();
                        $('#md_modal').dialog('close');
                        $('#md_modal').dialog({title: "{{Gestion des antennes bluetooth}}"});
                        $('#md_modal').load('index.php?v=d&plugin=MiFlora&modal=MiFlora.remote&id=MiFlora').dialog('open');
                    }
                });
            }
        });
    });
    $('.MiFloraRemoteAction[data-action=dependancyRemote]').on('click',function(){
        var MiFlora_remote = $('.MiFloraRemote').getValues('.MiFloraRemoteAttr')[0];
        $.ajax({
            type: "POST",
            url: "plugins/"+plugin+"/core/ajax/"+plugin+".ajax.php",
            data: {
                action: "dependancyRemote",
                remoteId: $('.li_MiFloraRemote.active').attr('data-MiFloraRemote_id'),
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error,$('#div_MiFloraRemoteAlert'));
            },
            success: function (data) {
                if (data.state != 'ok') {
                    $('#div_MiFloraRemoteAlert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_MiFloraRemoteAlert').showAlert({message: '{{Envoie réussie}}', level: 'success'});
            }
        });
    });

    $('.MiFloraRemoteAction[data-action=getRemoteLogDependancy]').on('click',function(){
        var MiFlora_remote = $('.MiFloraRemote').getValues('.MiFloraRemoteAttr')[0];
        $.ajax({
            type: "POST",
            url: "plugins/"+plugin+"/core/ajax/"+plugin+".ajax.php",
            data: {
                action: "getRemoteLogDependancy",
                remoteId: $('.li_MiFloraRemote.active').attr('data-MiFloraRemote_id'),
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error,$('#div_MiFloraRemoteAlert'));
            },
            success: function (data) {
                if (data.state != 'ok') {
                    $('#div_MiFloraRemoteAlert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_MiFloraRemoteAlert').showAlert({message: '{{Log récupérée}}', level: 'success'});
            }
        });
    });
window.setInterval(function () {
    displayMiFloraRemoteComm($('.li_MiFloraRemote.active').attr('data-MiFloraRemote_id'));
}, 5000);
</script>
