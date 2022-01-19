<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('MiFlora');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
$eqlogicss = eqLogic::byType($plugin->getId());
?>
<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un MiFlora}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes MiFloras}}</legend>
        <legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
                <center>
                    <i class="fa fa-plus-circle" style="font-size : 6em;color:#94ca02;"></i>
                </center>
                <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>{{Ajouter}}</center></span>
            </div>

            <div class="cursor" id="bt_scanMiFlora" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
             <center class="includeicon">
             <i class="fa fa-bullseye" style="font-size : 6em;color:#94ca02;"></i>
             </center>
             <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>{{Lancer Scan}}</center></span>
             </div>


            <div class="cursor eqLogicAction" data-action="gotoPluginConf" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
                <center>
                    <i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
                </center>
                <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Configuration}}</center></span>
            </div>

            <div class="cursor" id="bt_remoteMiFlora" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
                <center>
                    <i class="fa fa-bluetooth" style="font-size : 6em;color:#767676;"></i>
                </center>
                <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Antennes}}</center></span>
            </div>

              <div class="cursor" id="bt_healthMiFlora" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
         <center>
            <i class="fa fa-medkit" style="font-size : 6em;color:#767676;"></i>
             </center>
            <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Santé}}</center></span>
             </div>
        </div>
        <legend><i class="fa fa-table"></i> {{Mes MiFloras}}</legend>
        <div class="eqLogicThumbnailContainer">
            <?php
            foreach ($eqLogics as $eqLogic) {
                echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
                echo "<center>";
                echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
                echo "</center>";
                echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
        <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
        <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Équipement}}</a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
        </ul>
        <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <br/>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Nom de l'équipement MiFlora}}</label>
                            <div class="col-sm-3">
                                <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement MiFlora}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                            <div class="col-sm-3">
                                <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
                                    foreach (jeeObject::all() as $object) {
                                        echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
                                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
                            </div>
                        </div>
                        <div class="form-group input-group-sm">
                            <label class="col-sm-3 control-label">{{MiFlora Bluetooth mac add}}</label>
                            <div class="col-sm-3">
                            	<div class="input-group input-group-sm">
                                <input type="text" style="text-transform: uppercase;" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="macAdd" placeholder="xx:xx:xx:xx"/>
                                <span class="input-group-btn">
									<a class="btn btn-default"  id="bt_searchMiFlora" title="Sélectionner la commande"><i class="fa fa-search"></i></a>
								</span>
                            </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Fréquence de récuperation des données}}</label>
                            <div class="col-sm-3">
                                <select id="frequence" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="frequence" placeholder="frequence de rafraichissement">
                                    <option selected value=0>{{default}}</option>
                                    <option value=0.25>{{15mn}}</option>
                                    <option value=0.5>{{30mn}}</option>
                                    <option value=1>{{1h}}</option>
                                    <option value=2>{{2h}}</option>
                                    <option value=3>{{3h}}</option>
                                    <option value=4>{{4h}}</option>
                                    <option value=5>{{5h}}</option>
                                    <option value=6>{{6h}}</option>
                                    <option value=7>{{7h}}</option>
                                    <option value=8>{{8h}}</option>
                                    <option value=9>{{9h}}</option>
                                    <option value=10>{{10h}}</option>
                                    <option value=11>{{11h}}</option>
                                    <option value=12>{{12h}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                                <label class="col-sm-3 control-label help" data-help="{{Antenne forcée)}}">{{Antenne}}</label>
                                <div class="col-sm-3">
                                    <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="antenna">
                                        <option value="local">{{Local}}</option>
                                        <option value="Auto">{{Auto}}</option>
                                        <?php
                                        try{
                                            $hasMiFlora = plugin::byId('MiFlora');
                                        } catch (Exception $e) {
                                        }
                                        if ($hasMiFlora != '' && $hasMiFlora->isActive()){
                                            $remotes = MiFlora_remote::all();
                                            foreach ($remotes as $remote) {
                                                echo '<option value="' . $remote->getId() . '">{{Remote : ' . $remote->getRemoteName() .'}}</option>';
                                            }
                                        }
                                        ?>

                                    </select>
                                </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label help" data-help="{{Humidité minimum pour la plante en cas de mini la commande hummin monte a 1 pour scenario}}">{{Humidité Minimum}}</label>
                            <div class="col-sm-3">
                                <input type="number" name="Humidité Minimum" min="5" max="90" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="HumMin" placeholder="Mum min"/>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Nom de la plante}}</label>
                            <span class="col-sm-3 eqLogicAttr" data-l1key="configuration" data-l2key="plant_name"></span>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Firmware}}</label>
                            <span class="col-sm-3 eqLogicAttr" data-l1key="configuration" data-l2key="firmware_version"></span>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Batterie}}</label>
                            <span class="col-sm-3 eqLogicAttr" data-l1key="configuration" data-l2key="batteryStatus"></span>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Dernière Collecte}}</label>
                            <span class="col-sm-3 eqLogicAttr" data-l1key="status" data-l2key="lastCommunication"></span>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Type d'objet}}</label>
                            <span class="col-sm-3 eqLogicAttr" data-l1key="configuration" data-l2key="devicetype"></span>
                        </div>
                     </fieldset>
                </form>
            </div>

            <div role="tabpanel" class="tab-pane" id="commandtab">
                <a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>
                <table id="table_cmd" class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>{{Nom}}</th><th>{{Type}}</th><th>{{Action}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'MiFlora', 'js', 'MiFlora');?>
<?php include_file('core', 'plugin.ajax', 'js'); ?>
<?php include_file('core', 'plugin.template', 'js');?>
