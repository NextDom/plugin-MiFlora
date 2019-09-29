<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('MiFlora');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction logoPrimary" data-action="add">
                <i class="fas fa-plus-circle"></i>
                <br>
                <span>{{Ajouter}}</span>
            </div>

            <div class="cursor" id="bt_scanMiFlora" >
                <i class="fas fa-bullseye"></i>
                <br>
                <span>{{Lancer Scan}}</span>
            </div>

            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fas fa-wrench"></i>
                <br>
                <span>{{Configuration}}</span>
            </div>
            <div class="cursor" id="bt_remoteMiFlora">
                <i class="fab fa-bluetooth"></i>
                <br>
                <span>{{Antennes}}</span>
            </div>
            
            <div class="cursor" id="bt_healthMiFlora">
                <i class="fas fa-medkit"></i>
                <br>
                <span>{{Santé}}</span>
            </div>
        </div>

        <legend><i class="fas fa-table"></i> {{Mes MiFloras}}</legend>
        <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
        <div class="eqLogicThumbnailContainer">
            <?php
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
                echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
                echo '<br>';
                echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

<div class="col-xs-12 eqLogic" style="display: none;">
    <div class="input-group pull-right" style="display:inline-flex">
        <span class="input-group-btn">
            <a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a>
            <a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a>
            <a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a>
            <a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
        </span>
    </div>               

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
        <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
        <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
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
                <label class="col-sm-3 control-label">{{Catégorie}}</label>
                <div class="col-sm-9">
                 <?php
                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                    echo '<label class="checkbox-inline">';
                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                    echo '</label>';
                    }
                  ?>
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
				<a class="btn btn-default"  id="bt_searchMiFlora" title="Sélectionner la commande"><i class="fas fa-search"></i></a>
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
                <a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Commandes}}</a><br/><br/>
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
<!-- <?php include_file('core', 'plugin.ajax', 'js'); ?>  Removed in Jeedom4 template -->
<?php include_file('core', 'plugin.template', 'js');?>
