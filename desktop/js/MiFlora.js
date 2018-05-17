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



$("#table_cmd").sortable({
    axis: "y",
    cursor: "move",
    items: ".cmd",
    placeholder: "ui-state-highlight",
    tolerance: "intersect",
    forcePlaceholderSize: true
});

 $('#bt_scanMiFlora').on('click', function () {
    $('#md_modal').dialog({title: "{{Scan MiFlora}}"});
    $('#md_modal').load('index.php?v=d&plugin=MiFlora&modal=ScanMiflora').dialog('open');
});

$('#bt_healthMiFlora').on('click', function () {
    $('#md_modal').dialog({title: "{{Santé MiFlora}}"});
    $('#md_modal').load('index.php?v=d&plugin=MiFlora&modal=health').dialog('open');
});

$('#bt_remoteMiFlora').on('click', function () {
    $('#md_modal').dialog({title: "{{Gestion des antennes bluetooth}}"});
    $('#md_modal').load('index.php?v=d&plugin=MiFlora&modal=MiFlora.remote&id=MiFlora').dialog('open');
});

function getModelListParam(_conf,_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/MiFlora/core/ajax/blea.ajax.php", // url du fichier php
        data: {
            action: "getModelListParam",
            conf: _conf,
            id: _id,
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var options = '';
            for (var i in data.result[0]) {
                if (data.result[0][i]['selected'] == 1){
                    options += '<option value="'+i+'" selected>'+data.result[0][i]['value']+'</option>';
                } else {
                    options += '<option value="'+i+'">'+data.result[0][i]['value']+'</option>';
                }
            }
            if (data.result[1] == true){
                $(".refreshdelay").show();
            } else {
                $(".refreshdelay").hide();
            }
            if (data.result[2] != false){
                $(".globalRemark").show();
                $(".globalRemark").empty().append(data.result[2]);
            } else {
                $(".globalRemark").empty()
                $(".globalRemark").hide();
            }
            if (data.result[3] != false){
                $(".specificmodal").show();
                $(".specificmodal").attr('data-modal', data.result[3]);
            } else {
                $(".specificmodal").hide();
            }
            if (data.result[4] != false){
                $(".cancontrol").show();
            } else {
                $(".cancontrol").hide();
            }
            if (data.result[5] != false){
                $(".canbelocked").show();
            } else {
                $(".canbelocked").hide();
            }
            $(".modelList").show();
            $(".listModel").html(options);
            $icon = $('.eqLogicAttr[data-l1key=configuration][data-l2key=iconModel]').value();
            if($icon != '' && $icon != null){
                $('#img_device').attr("src", 'plugins/blea/core/config/devices/'+$icon+'.jpg');
            }
        }
    });
}

/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.MiFlora
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {
            configuration: {}
        };
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}


