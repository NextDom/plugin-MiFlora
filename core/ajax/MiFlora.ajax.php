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

try {
    require_once __DIR__ . '/../../../../core/php/core.inc.php';
    require_once __DIR__ . '/../../../../plugins/MiFlora/core/class/MiFlora.class.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new \Exception(__('401 - Accès non autorisé', __FILE__));
    }

    ajax::init();

     // action qui permet d'obtenir l'ensemble des eqLogic
    switch (init('action')) {
        case 'getAll':
            $eqLogics = eqLogic::byType('MiFlora');
            // la liste des équipements
            foreach ($eqLogics as $eqLogic) {
                $data['id'] = $eqLogic->getId();
                $data['humanSidebar'] = $eqLogic->getHumanName(true, false);
                $data['humanContainer'] = $eqLogic->getHumanName(true, true);
                $return[] = $data;
            }
            ajax::success($return);
            break;
        case 'scanbluetooth':
            MiFlora::scanbluetooth();
            ajax::success();
    }
    // action qui permet d'effectuer la sauvegarde des donéée en asynchrone
    if (init('action') == 'saveStack') {
        $params = init('params');
        ajax::success(MiFlora::saveStack($params));
    }


    if (init('action') == 'save_MiFloraRemote') {
        $MiFloraRemoteSave = jeedom::fromHumanReadable(json_decode(init('MiFlora_remote'), true));
        $MiFlora_remote = MiFlora_remote::byId($MiFloraRemoteSave['id']);
        if (!is_object($MiFlora_remote)) {
            $MiFlora_remote = new MiFlora_remote();
        }
        utils::a2o($MiFlora_remote, $MiFloraRemoteSave);
        $MiFlora_remote->save();
        ajax::success(utils::o2a($MiFlora_remote));
    }

    if (init('action') == 'get_MiFloraRemote') {
        $MiFlora_remote = MiFlora_remote::byId(init('id'));
        if (!is_object($MiFlora_remote)) {
            throw new Exception(__('Remote inconnu : ', __FILE__) . init('id'), 9999);
        }
        ajax::success(jeedom::toHumanReadable(utils::o2a($MiFlora_remote)));
    }

    if (init('action') == 'remove_MiFloraRemote') {
        $id = init('id') ;
        log::add('MiFlora','info', 'remote  id ' . $id ) ;
        $MiFlora_remote = MiFlora_remote::byId(init('id'));
        log::add('MiFlora','debug', 'remote  remove' ) ;
        if (!is_object($MiFlora_remote)) {
            throw new Exception(__('Remote inconnu : ', __FILE__) . init('id'), 9999);
        }
        $MiFlora_remote->remove();
        ajax::success();
    }

    if (init('action') == 'getRemoteLogDependancy') {
        ajax::success(MiFlora::getRemoteLog(init('remoteId'),'_dependancy'));
    }

    if (init('action') == 'dependancyRemote') {
        ajax::success(MiFlora::dependancyRemote(init('remoteId')));
    }



    throw new \Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
}catch
    (\Exception $e) {
        ajax::error(displayExeption($e), $e->getCode());
    }

