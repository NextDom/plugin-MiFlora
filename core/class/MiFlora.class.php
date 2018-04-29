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
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/MiFloraCmd.class.php';


class MiFlora extends eqLogic
{
    public static $_widgetPossibility = array('custom' => true);

    public static function getFrequenceItem($mi_flora)
    {
        if (log::getLogLevel('MiFlora') == 100){ // si debug -> chaque minutes
            $frequenceItem = 1/60;
        } else {
            $frequenceItem = $mi_flora->getConfiguration('frequence');
            if ($frequenceItem == 0){
                $frequenceItem=config::byKey('frequence', 'MiFlora');
                if ($frequenceItem == 0){
                    $frequenceItem=1; // Ne doit pas arriver, 1H par defaut si config a une mauvaise valeur
                }
            }
        }
        return $frequenceItem;
    }

    public static function isProcessMiFlora($frequenceMin , $minutesStartCron ){
        if ( ($minutesStartCron % (round($frequenceMin * 60))) == 0) {
            if ($frequenceMin > 1) {
                if ((date("H") % (round($frequenceMin))) == 0) {
                    $processMiFlora = 1;
                } else {
                    $processMiFlora = 0;
                }
            } else {
                $processMiFlora = 1;
            }
        } else {
            $processMiFlora = 0;
        }
        // log::add('MiFlora', 'info', 'frequence < 1 :' . $frequenceMin . ' round(frequenceMin*60):' . round($frequenceMin * 60) . ' $minutesStartCron:' . $minutesStartCron . ' $processMiFlora:' . $processMiFlora . ' $minutesStartCron % (round($frequence * 60))):' . $minutesStartCron % (round($frequenceMin * 60))));
        return $processMiFlora;
    }

    public static function isMiFloraToBeProcessed($minutesStartCron){
        $frequenceItemMin=1000;
        foreach (eqLogic::byType('MiFlora', true) as $mi_flora) {
            $frequenceItem = MiFlora::getFrequenceItem($mi_flora);
            $frequenceItemMin = min($frequenceItemMin,$frequenceItem);
            //log::add('MiFlora', 'info', '$frequenceItem: '.$frequenceItem);
            //log::add('MiFlora', 'info', '$frequenceItem*60.0: '.round($frequenceItem*60.0));
            //log::add('MiFlora', 'info', 'date: '.$minutesStartCron.' - modulo: '.$minutesStartCron%round($frequenceItem*60));
        }
        return MiFlora::isProcessMiFlora($frequenceItemMin , $minutesStartCron);

    }

/*
 * Fonction exécutée automatiquement toutes les minutes par Jeedom
  activer cette version pour tester toutes les minutes, garder ensuite la suivante: une mesure par heure me semble suffisante */

    public static function cron()
    {
        if (log::getLogLevel('MiFlora') == 100){ // si debug -> chaque minutes
            log::add('MiFlora','debug', 'lance debug toute les minutes ');
            self::cron15();
        }
    }




	public static function cron15()
    {
        $minutesStartCron = date("i") ;
        log::add('MiFlora', 'info', 'traitement pour :'.$minutesStartCron) ;
        $processMiFlora = self::isMiFloraToBeProcessed($minutesStartCron);
        //log::add('MiFlora', 'info', 'process MiFlora ... '.$processMiFlora);
        if ($processMiFlora == 1){
             log::add('MiFlora', 'info', 'start process MiFlora ...');
            self::ProcessMiFlora($minutesStartCron);
       } else{
            log::add('MiFlora', 'info', 'skip MiFlora ...');
        }
    }

    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom */

 /*   public static function cronHourly()
    {
        self::ProcessMiFlora();
    }*/

    public static function scanbluetooth()
    {
        log::remove('MiFlora_scanbluetooth');
        $port = config::byKey('adapter', 'MiFlora', 0);
        $cmd  = 'expect ' . dirname(__FILE__) . '/../../resources/bluetooth-scan.sh ' . $port;
        $cmd  .= ' >> ' . log::getPathToLog('MiFlora_scanbluetooth') . ' 2>&1 &';
        exec($cmd);
    }

    public static function ProcessMiFlora($minutesStartCron)
    {
        $adapter = config::byKey('adapter', 'MiFlora');
        $seclvl = config::byKey('seclvl', 'MiFlora');
        foreach (eqLogic::byType('MiFlora', true) as $mi_flora) {
            log::add('MiFlora', 'info', 'enter item per item:'.$mi_flora->getHumanName(false, false));
            $frequenceItem = MiFlora::getFrequenceItem($mi_flora);
            if (MiFlora::isProcessMiFlora($frequenceItem,$minutesStartCron) == 0){
                log::add('MiFlora', 'info', $mi_flora->getHumanName(false, false).' frequence toutes les '.round($frequenceItem*60)." minutes, next");
                // Attn: min frequence = 12h a 0 et 12h --> ok pour batterie"
            } else {
                log::add('MiFlora', 'info', $mi_flora->getHumanName(false, false).' frequence toutes les '.round($frequenceItem*60).' minutes, go');
                //$mi_flora->refreshWidget();
                $macAdd = $mi_flora->getConfiguration('macAdd');
                log::add('MiFlora', 'info', 'mi flora mac add:' . $macAdd);
                $FirmwareVersion = $mi_flora->getConfiguration('firmware_version');
                // recupere le niveau de la batterie deux  fois par jour a 12 h
                // log::add('MiFlora', 'debug', 'date:'.date("h"));
                if (((date("h") == 12 && intval($minutesStartCron) < 5)) || $FirmwareVersion == '') {
                    $MiFloraBatteryAndFirmwareVersion = '';
                    $MiFloraNameString = '';
                    $MiFloraName = '';
                    $battery = -1;
                    $mi_flora->getMiFloraStaticData($macAdd, $MiFloraBatteryAndFirmwareVersion, $MiFloraNameString, $adapter, $seclvl);
                    $mi_flora->traiteMiFloraBatteryAndFirmwareVersion($macAdd, $MiFloraBatteryAndFirmwareVersion, $battery, $FirmwareVersion);
                    $mi_flora->traiteMiFloraName($macAdd, $MiFloraNameString, $MiFloraName);
                    $mi_flora->updateStaticData($macAdd, $battery, $FirmwareVersion, $MiFloraName);
                    if($battery<$mi_flora->getConfiguration('battery_danger_threshold')){
                        log::add('MiFlora', 'error', 'Error: Batterie faible - '.$battery);

                    } elseif ($battery<$mi_flora->getConfiguration('battery_warning_threshold')) {
                        log::add('MiFlora', 'error', 'Warning: Batterie faible - '.$battery);
                    }
                }
                $tryGetData = 0;
                $MiFloraData = '';
                $loopcondition = true;
                while ($loopcondition) {
                    if ($tryGetData > 3) { // stop after 4 try
                        break;
                    }
                    if ($tryGetData > 0) {
                        log::add('MiFlora', 'info', 'mi flora data for ' . $macAdd . ' is empty or null, trying again, nb retry:' . $tryGetData);
                    }

                    log::add('MiFlora', 'debug', 'mi flora FirmwareVersion:' . $FirmwareVersion);

                    $mi_flora->getMesure($macAdd, $MiFloraData, $FirmwareVersion, $adapter, $seclvl);
                    log::add('MiFlora', 'debug', 'mi flora data:' . $MiFloraData . ':');
                    $tryGetData++;
                    // TODO
                    // traiter ces reponses en erreur
                    // Characteristic value/descriptor: aa bb cc dd ee ff 99 88 77 66 00 00 00 00 00 00
                    // Characteristic value/descriptor: 00 00 00 00 00 00 00 00 00 00 00 00 00 00 00 00
                    $mi_flora->traiteMesure($macAdd, $MiFloraData, $temperature, $moisture, $fertility, $lux);
                    // log::add('MiFlora', 'debug', 'temperature:'.$temperature.':');
                    if ($MiFloraData == '' or ($temperature == 0 and $moisture == 0 and $fertility == 0 and $lux == 0)) {
                        // wait 5 s hopping it'll be better ...
                        log::add('MiFlora', 'debug', 'wait 5 s hopping it ll be better ...');
                        sleep(5);
                    } else {
                        $loopcondition = false;
                    }
                }
                if ($MiFloraData == '') {
                    log::add('MiFlora', 'warning', 'mi flora data is empty, retried ' . $tryGetData . ' times, stop pour '.$mi_flora->getHumanName(false, false));
                    message::add ('MiFlora', 'mi flora data is empty for '.$mi_flora->getHumanName(false, false) .' check module');
                } else {
                    $mi_flora->traiteMesure($macAdd, $MiFloraData, $temperature, $moisture, $fertility, $lux);
                    $mi_flora->updateJeedom($macAdd, $temperature, $moisture, $fertility, $lux);
                    $mi_flora->refreshWidget();
                }
            }
        }

    }

    /* */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDayly() {

      }
     */

    /* fonction permettant d'initialiser la pile
     * plugin: le nom de votre plugin
     * action: l'action qui sera utilisé dans le fichier ajax du pulgin
     * callback: fonction appelé coté client(JS) pour mettre à jour l'affichage
     */

    public function initStackData()
    {
        nodejs::pushUpdate('MiFlora::initStackDataEqLogic', array('plugin' => 'MiFlora', 'action' => 'saveStack', 'callback' => 'displayEqLogic'));
    }

    /* fonnction permettant d'envoyer un nouvel équipement pour sauvegarde et affichage,
     * les données sont envoyé au client(JS) pour être traité de manière asynchrone
     * Entrée:
     *      - $params: variable contenant les paramètres eqLogic
     */

    public function stackData($params)
    {
        if (is_object($params)) {
            $paramsArray = utils::o2a($params);
        }
        nodejs::pushUpdate('MiFlora::stackDataEqLogic', $paramsArray);
    }

    /* Fonction appelé pour la sauvegarde asynchrone
     * Entrée:
     *      - $params: variable contenant les paramètres eqLogic
     */

    /*public function saveStack($params)
    {
        // inserer ici le traitement pour sauvegarde de vos données en asynchrone
    }

    /* fonction appelé avant le début de la séquence de sauvegarde */

    /*public function preSave()
    {

    }*/

    /* fonction appelé pendant la séquence de sauvegarde avant l'insertion
     * dans la base de données pour une mise à jour d'une entrée */

    public function preUpdate()
    {
        if (empty($this->getConfiguration('macAdd'))) {
            throw new \Exception(__('L\'adresse Mac doit être spécifiée', __FILE__));
        }
    }

    public function preInsert()
    {
        $this->setIsEnable(1);
        $this->setIsVisible(1);
        $this->setConfiguration('battery_type', '1x3V CR2032');
        $this->setConfiguration('batteryStatus', '');
        $this->setConfiguration('firmware_version', '');
        $this->setConfiguration('plant_name', '');
        $this->setConfiguration('frequence', '0');
        $this->setConfiguration('battery_danger_threshold','10');
        $this->setConfiguration('battery_warning_threshold','15');
    }

    /* public function postInsert()
      {
      }

      public function postSave()
      {
      } */

    public function postUpdate()
    {
        $lastrefresh = $this->getCmd(null, 'lastrefresh');
        if (!is_object($lastrefresh)) {
            $lastrefresh = new MiFloraCmd();
            $lastrefresh->setLogicalId('lastrefresh');
            $lastrefresh->setIsVisible(1);
            $lastrefresh->setName(__('Dernier refresh', __FILE__));
        }
        $lastrefresh->setType('info');
        $lastrefresh->setSubType('string');
        $lastrefresh->setEventOnly(1);
        $lastrefresh->setEqLogic_id($this->getId());
        $lastrefresh->save();

        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = new MiFloraCmd();
            $refresh->setLogicalId('refresh');
            $refresh->setIsVisible(1);
            $refresh->setName(__('Rafraîchir', __FILE__));
        }
        $refresh->setType('action');
        $refresh->setSubType('other');
        $refresh->setEqLogic_id($this->getId());
        $refresh->save();

        $cmdlogic = MiFloraCmd::byEqLogicIdAndLogicalId($this->getId(), 'temperature');
        if (!is_object($cmdlogic)) {
            $MiFloraCmd = new MiFloraCmd();
            $MiFloraCmd->setName(__('Temperature', __FILE__));
            $MiFloraCmd->setEqLogic_id($this->id);
            $MiFloraCmd->setLogicalId('temperature');
            $MiFloraCmd->setConfiguration('data', 'temperature');
            $MiFloraCmd->setEqType('miflora');
            $MiFloraCmd->setType('info');
            $MiFloraCmd->setSubType('numeric');
            $MiFloraCmd->setUnite('°C');
            $MiFloraCmd->setIsHistorized(1);
            $MiFloraCmd->save();
        }
        $cmdlogic = MiFloraCmd::byEqLogicIdAndLogicalId($this->getId(), 'moisture');
        if (!is_object($cmdlogic)) {
            $MiFloraCmd = new MiFloraCmd();
            $MiFloraCmd->setName(__('Moisture', __FILE__));
            $MiFloraCmd->setEqLogic_id($this->id);
            $MiFloraCmd->setLogicalId('moisture');
            $MiFloraCmd->setConfiguration('data', 'moisture');
            $MiFloraCmd->setEqType('miflora');
            $MiFloraCmd->setType('info');
            $MiFloraCmd->setSubType('numeric');
            $MiFloraCmd->setUnite('%');
            $MiFloraCmd->setIsHistorized(1);
            $MiFloraCmd->save();
        }
        $cmdlogic = MiFloraCmd::byEqLogicIdAndLogicalId($this->getId(), 'fertility');
        if (!is_object($cmdlogic)) {
            $MiFloraCmd = new MiFloraCmd();
            $MiFloraCmd->setName(__('Fertility', __FILE__));
            $MiFloraCmd->setEqLogic_id($this->id);
            $MiFloraCmd->setLogicalId('fertility');
            $MiFloraCmd->setConfiguration('data', 'fertility');
            $MiFloraCmd->setEqType('miflora');
            $MiFloraCmd->setType('info');
            $MiFloraCmd->setSubType('numeric');
            $MiFloraCmd->setUnite('');
            $MiFloraCmd->setIsHistorized(1);
            $MiFloraCmd->save();
        }
        $cmdlogic = MiFloraCmd::byEqLogicIdAndLogicalId($this->getId(), 'lux');
        if (!is_object($cmdlogic)) {
            $MiFloraCmd = new MiFloraCmd();
            $MiFloraCmd->setName(__('Lux', __FILE__));
            $MiFloraCmd->setEqLogic_id($this->id);
            $MiFloraCmd->setLogicalId('lux');
            $MiFloraCmd->setConfiguration('data', 'lux');
            $MiFloraCmd->setEqType('miflora');
            $MiFloraCmd->setType('info');
            $MiFloraCmd->setSubType('numeric');
            $MiFloraCmd->setUnite('lx');
            $MiFloraCmd->setIsHistorized(1);
            $MiFloraCmd->save();
        }
    }

    /* public function preRemove()
      {

      }

      public function postRemove()
      {

      } */

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*     * **********************Getteur Setteur*************************** */

    public function getMesure($macAdd, &$MiFloraData, $FirmwareVersion, $adapter, $seclvl)
    {
        log::add('MiFlora', 'debug', 'macAdd:' . $macAdd);
        $MiFloraData = '';
        // $MiFloraData='Characteristic value/descriptor: e1 00 00 8b 00 00 00 10 5d 00 00 00 00 00 00 00 \n';
        // $MiFloraData='Characteristic value/descriptor read failed: Internal application error: I/O';
        //TODO: tester chaine error et gerer erreur

        $is_deporte = config::byKey('maitreesclave', 'MiFlora');
        log::add('MiFlora', 'debug', 'is_deporte:' . $is_deporte);
        if ($is_deporte == "deporte") {
            $ip   = config::byKey('addressip', 'MiFlora');
            $port = config::byKey('portssh', 'MiFlora');
            $user = config::byKey('user', 'MiFlora');
            $pass = config::byKey('password', 'MiFlora');

            log::add('MiFlora', 'debug', 'ip:' . $ip);
            log::add('MiFlora', 'debug', 'port:' . $port);
            log::add('MiFlora', 'debug', 'user:' . $user);
            log::add('MiFlora', 'debug', 'pass:' . $pass);

            if ($FirmwareVersion == "2.6.2") {
                $commande = "gatttool --adapter=" . $adapter . " -b " . $macAdd . " --char-read -a 0x35 --sec-level=" . $seclvl;
                # $commande="/usr/bin/python /tmp/getMiFloraData.py ".$macAdd." ".$FirmwareVersion." 0 ".$adapter." ".$seclvl;
            } else {
                $commande = "/usr/bin/python /tmp/GetMiFloraData.py " . $macAdd . " " . $FirmwareVersion . " 0 " . $adapter . " " . $seclvl;
            }

            log::add('MiFlora', 'debug', 'connexion SSH ...' . $commande);
            if (!$connection = ssh2_connect($ip, $port)) {
                log::add('MiFlora', 'error', 'connexion SSH KO');
            } else {
                if (!ssh2_auth_password($connection, $user, $pass)) {
                    log::add('MiFlora', 'error', 'Authentification SSH KO');
                } else {
                    log::add('MiFlora', 'debug', 'Commande par SSH');
                    ssh2_scp_send($connection, realpath(dirname(__FILE__)) . '/../../resources/GetMiFloraData.py', '/tmp/GetMiFloraData.py', 0755);

                    $gattresult  = ssh2_exec($connection, $commande);
                    stream_set_blocking($gattresult, true);
                    $MiFloraData = stream_get_contents($gattresult);
                    log::add('MiFlora', 'debug', 'SSH result:' . $MiFloraData);

                    $closesession = ssh2_exec($connection, 'exit');
                    stream_set_blocking($closesession, true);
                    stream_get_contents($closesession);
                }
            }
        } else {
            //$MiFloraData='Characteristic value/descriptor: e1 00 00 8b 00 00 00 10 5d 00 00 00 00 00 00 00 \n';
            log::add('MiFlora', 'debug', 'local call');
            #  $command = 'gatttool -b ' . $macAdd . '  --char-read -a 0x35 --sec-level=high  2>&1 ';
            if ($FirmwareVersion == "2.6.2") {
                $command = "gatttool --adapter=" . $adapter . " -b " . $macAdd . '  --char-read -a 0x35 --sec-level=' . $seclvl . ' 2>&1 ';
                # $command="/usr/bin/python ".dirname(__FILE__) . "/../../resources/GetMiFloraData.py ".$macAdd." ".$FirmwareVersion." 0 ".$adapter." ".$seclvl;
            } else {
                $command = "/usr/bin/python " . dirname(__FILE__) . "/../../resources/GetMiFloraData.py " . $macAdd . " " . $FirmwareVersion . " 0 " . $adapter . " " . $seclvl;
            }
            log::add('MiFlora', 'debug', 'command: ' . $command);
            $MiFloraData = exec($command);
            log::add('MiFlora', 'debug', 'MiFloraData: ' . $MiFloraData);
            if (strpos($MiFloraData, 'read failed') !== false or strpos($MiFloraData, 'connect') !== false) {
                log::add('MiFlora', 'error', 'erreur: gatttool ne fonctionne pas - ' . $MiFloraData);
                $MiFloraData = '';
            }
        }
    }

    public function getMiFloraStaticData($macAdd, &$MiFloraBatteryAndFirmwareVersion, &$MiFloraName, $adapter, $seclvl)
    {
        log::add('MiFlora', 'debug', 'macAdd:' . $macAdd);
        $MiFloraBatteryAndFirmwareVersion = '';
        $MiFloraName                      = '';
        // $MiFloraData='Characteristic value/descriptor: e1 00 00 8b 00 00 00 10 5d 00 00 00 00 00 00 00 \n';
        // $MiFloraData='Characteristic value/descriptor read failed: Internal application error: I/O';
        //TODO: tester chaine error et gerer erreur

        $is_deporte = config::byKey('maitreesclave', 'MiFlora');
        log::add('MiFlora', 'debug', 'is_deporte:' . $is_deporte);
        if ($is_deporte == "deporte") {

            $ip   = config::byKey('addressip', 'MiFlora');
            $port = config::byKey('portssh', 'MiFlora');
            $user = config::byKey('user', 'MiFlora');
            $pass = config::byKey('password', 'MiFlora');

            log::add('MiFlora', 'debug', 'ip:' . $ip);
            log::add('MiFlora', 'debug', 'port:' . $port);
            log::add('MiFlora', 'debug', 'user:' . $user);
            log::add('MiFlora', 'debug', 'pass:' . $pass);

            log::add('MiFlora', 'debug', 'connexion SSH ...');
            if (!$connection = ssh2_connect($ip, $port)) {
                log::add('MiFlora', 'error', 'connexion SSH KO');
            } else {
                if (!ssh2_auth_password($connection, $user, $pass)) {
                    log::add('MiFlora', 'error', 'Authentification SSH KO');
                } else {
                    log::add('MiFlora', 'debug', 'Commande par SSH');
                    // get MiFlora Battery And Firmware Version
                    //gatttool -b C4:7C:8D:61:BB:9A --char-read -a 0x038
                    //Characteristic value/descriptor: 64 10 32 2e 36 2e 32
                    //battery:64 version 2.6.2
                    $gattresult                       = ssh2_exec($connection, "gatttool --adapter=" . $adapter . " -b " . $macAdd . " --char-read -a 0x038 --sec-level=" . $seclvl);
                    stream_set_blocking($gattresult, true);
                    $MiFloraBatteryAndFirmwareVersion = stream_get_contents($gattresult);
                    log::add('MiFlora', 'debug', 'MiFloraBatteryAndFirmwareVersion:' . $MiFloraBatteryAndFirmwareVersion);

                    // get MiFlora Name
                    //gatttool -b C4:7C:8D:61:BB:9A --char-read -a 0x03
                    // Characteristic value/descriptor: 46 6c 6f 77 65 72 20 6d 61 74 65 (Flower mate)
                    $gattresult  = ssh2_exec($connection, "gatttool --adapter=" . $adapter . " -b " . $macAdd . " --char-read -a 0x03 --sec-level=" . $seclvl);
                    stream_set_blocking($gattresult, true);
                    $MiFloraName = stream_get_contents($gattresult);
                    log::add('MiFlora', 'debug', 'MiFloraName:' . $MiFloraName);

                    $closesession = ssh2_exec($connection, 'exit');
                    stream_set_blocking($closesession, true);
                    stream_get_contents($closesession);
                }
            }
        } else {
            // $MiFloraBatteryAndFirmwareVersion ='Characteristic value/descriptor: 64 10 32 2e 36 2e 32 ';
            // $MiFloraName='Characteristic value/descriptor: 46 6c 6f 77 65 72 20 6d 61 74 65 \n';
            // connect error: Connection timed out
            // connect: Device or resource busy
            log::add('MiFlora', 'debug', 'local call static data');
            $command                          = 'gatttool --adapter=' . $adapter . ' -b ' . $macAdd . '  --char-read -a 0x38 --sec-level=' . $seclvl . ' 2>&1 ';
            $MiFloraBatteryAndFirmwareVersion = exec($command);
            log::add('MiFlora', 'debug', 'MiFloraBatteryAndFirmwareVersion: ' . $MiFloraBatteryAndFirmwareVersion);
            if (strpos($MiFloraBatteryAndFirmwareVersion, 'read failed') !== false or strpos($MiFloraBatteryAndFirmwareVersion, 'connect') !== false) {
                log::add('MiFlora', 'error', 'erreur: gatttool ne fonctionne pas - ' . $MiFloraBatteryAndFirmwareVersion);
                $MiFloraBatteryAndFirmwareVersion = '';
            }
            $command     = 'gatttool --adapter=' . $adapter . ' -b ' . $macAdd . '  --char-read -a 0x03 --sec-level=' . $seclvl . '  2>&1 ';
            $MiFloraName = exec($command);
            log::add('MiFlora', 'debug', 'MiFloraName: ' . $MiFloraName);
            if (strpos($MiFloraName, 'read failed') !== false or strpos($MiFloraName, 'connect') !== false) {
                log::add('MiFlora', 'error', 'erreur: gatttool ne fonctionne pas - ' . $MiFloraName);
                $MiFloraName = '';
            }
        }
    }

    public function hex2bin($h)
    {
        if (!is_string($h))
            return null;
        $r = '';
        for ($a = 0; $a < strlen($h); $a += 2) {
            $r .= chr(hexdec($h{$a} . $h{($a + 1)}));
        }
        return $r;
    }

    public function traiteMiFloraBatteryAndFirmwareVersion($macAdd, $MiFloraData, &$battery, &$FirmwareVersion)
    {
        //Characteristic value/descriptor: 64 10 32 2e 36 2e 32
        $MiFloraData     = explode(": ", $MiFloraData);
        $MiFloraData     = explode(" ", $MiFloraData[1]);
        $battery         = hexdec($MiFloraData[0]);
        $FirmwareVersion = $MiFloraData[2] . $MiFloraData[3] . $MiFloraData[4] . $MiFloraData[5] . $MiFloraData[6];
        $FirmwareVersion = hex2bin($FirmwareVersion);
        log::add('MiFlora', 'debug', $macAdd . ' MiFloraData[0]:' . $MiFloraData[0]);
        log::add('MiFlora', 'debug', $macAdd . ' battery:' . $battery);
        log::add('MiFlora', 'debug', $macAdd . ' FirmwareVersion:' . $FirmwareVersion);
    }

    public function traiteMiFloraName($macAdd, $MiFloraData, &$miFloraName)
    {
        //Characteristic value/descriptor: 64 10 32 2e 36 2e 32
        $MiFloraData = explode(": ", $MiFloraData);
        $MiFloraData = explode(" ", $MiFloraData[1]);
        $miFloraName = $MiFloraData[0] . $MiFloraData[1] . $MiFloraData[2] . $MiFloraData[3] . $MiFloraData[4] . $MiFloraData[5] . $MiFloraData[6] . $MiFloraData[7] . $MiFloraData[8] . $MiFloraData[9] . $MiFloraData[10];
        $miFloraName = hex2bin($miFloraName);
        log::add('MiFlora', 'debug', $macAdd . ' miFloraName:' . $miFloraName);
    }

    public function traiteMesure($macAdd, $MiFloraData, &$temperature, &$moisture, &$fertility, &$lux)
    {
        // process data
        // log::add('MiFlora', 'debug', 'MiFloraData:'.$MiFloraData);
        $MiFloraData = explode(": ", $MiFloraData);
        // log::add('MiFlora', 'debug', 'MiFloraDataExplode:'.$MiFloraData[1]);
        $MiFloraData = explode(" ", $MiFloraData[1]);
        if (hexdec($MiFloraData[1]) > 128) {
            $temperature = -((65536 - hexdec($MiFloraData[1] . $MiFloraData[0])) / 10);
        } else {
            $temperature = hexdec($MiFloraData[1] . $MiFloraData[0]) / 10;
        }
        // traite cette erreur:
        // Characteristic value/descriptor: aa bb cc dd ee ff 99 88 77 66 00 00 00 00 00 00
        if ($temperature == -1749.4) {
            log::add('MiFlora', 'info', $macAdd . 'Temperature:' . $temperature . ' Lu: aa bb cc dd ... Mise de toutes les valeurs a 0 pour forcer un retry');
            $temperature = 0;
            $moisture    = 0;
            $fertility   = 0;
            $lux         = 0;
        } else {
            $moisture  = hexdec($MiFloraData[7]);
            $fertility = hexdec($MiFloraData[9] . $MiFloraData[8]);
            $lux       = hexdec($MiFloraData[4] . $MiFloraData[3]);
            log::add('MiFlora', 'debug', $macAdd . ' Temperature:' . $temperature);
            log::add('MiFlora', 'debug', $macAdd . ' Moisture:' . $moisture);
            log::add('MiFlora', 'debug', $macAdd . ' Fertility:' . $fertility);
            log::add('MiFlora', 'debug', $macAdd . ' Lux:' . $lux);
        }
    }

    public function updateJeedom($macAdd, $temperature, $moisture, $fertility, $lux)
    {
        // store into Jeedom DB
        if ($temperature == 0 && $moisture == 0 && $fertility == 0 && $lux == 0) {
            log::add('MiFlora', 'error', 'Toutes les mesures a 0 pour ' . $macAdd . ', erreur de connection Mi Flora');
        } else {
            if ($temperature > 100 || $temperature < -50) {
                log::add('MiFlora', 'error', 'Temperature hors plage (' . $temperature . ') pour ' . $macAdd . ', erreur de connection Bluetooth');
            } else {
                $cmd = $this->getCmd(null, 'temperature');
                if (is_object($cmd)) {
                    // $cmd->setCollectDate($date);
                    $cmd->event($temperature);
                    log::add('MiFlora', 'info', $macAdd . ' Store Temperature:' . $temperature);
                }
                $cmd = $this->getCmd(null, 'moisture');
                if (is_object($cmd)) {
                    $cmd->event($moisture);
                    log::add('MiFlora', 'info', $macAdd . ' Store Moisture:' . $moisture);
                }
                $cmd = $this->getCmd(null, 'fertility');
                if (is_object($cmd)) {
                    $cmd->event($fertility);
                    log::add('MiFlora', 'info', $macAdd . ' Store Fertility:' . $fertility);
                }
                $cmd = $this->getCmd(null, 'lux');
                if (is_object($cmd)) {
                    $cmd->event($lux);
                    log::add('MiFlora', 'info', $macAdd . ' Store Lux:' . $lux);

                }
                $cmd = $this->getCmd(null, 'lastrefresh');
                if (is_object($cmd)) {
                    $lastrefresh=(date('H:i'));
                    $cmd->event($lastrefresh);
                    log::add('MiFlora', 'info', $macAdd . ' Store LastRefresh:' . $lastrefresh);
                }
            }
        }
    }

    public function updateStaticData($macAdd, $battery, $FirmwareVersion, $MiFloraName)
    {
        log::add('MiFlora', 'debug', 'Update Static Data');
        // store into Jeedom DB
        if ($battery == 0) {
            // pas de retry pour ce type d info, on peut perdre une ou deux mesures
            log::add('MiFlora', 'info', 'Battery=0, pour ' . $macAdd . ' ,erreur probable de connection Mi Flora');
        } else {
            $this->batteryStatus($battery);
            if ($battery != $this->getConfiguration('batteryStatus')) {
                log::add('MiFlora', 'debug', $macAdd . ' Store battery:' . $battery);
                $this->setConfiguration('batteryStatus', $battery);
                $this->save();
            }
            if ($FirmwareVersion != $this->getConfiguration('firmware_version')) {
                log::add('MiFlora', 'info', $macAdd . ' Store firmware version:' . $FirmwareVersion);
                $this->setConfiguration('firmware_version', $FirmwareVersion);
                $this->save();
            }
        }
        if ($MiFloraName == '') {
            log::add('MiFlora', 'info', 'MiFloraName vide pour ' . $macAdd . ', erreur probable de connection Mi Flora');
        } else {
            if ($MiFloraName != $this->getConfiguration('plant_name')) {
                log::add('MiFlora', 'info', $macAdd . ' Store MiFloraName:' . $MiFloraName);
                $this->setConfiguration('plant_name', $MiFloraName);
                $this->save();
            }
        }
    }

    public function toHtml($_version = 'dashboard')
    {
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
        $version = jeedom::versionAlias($_version);
        if ($this->getDisplay('hideOn' . $version) == 1) {
            return '';
        }
        foreach ($this->getCmd('info') as $cmd) {
            $replace['#' . $cmd->getLogicalId() . '_id#']      = $cmd->getId();
            $replace['#' . $cmd->getLogicalId() . '#']         = $cmd->execCmd();
            $replace['#' . $cmd->getLogicalId() . '_collect#'] = $cmd->getCollectDate();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
            }
        }

        log::add('MiFlora', 'debug', $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'miflora', 'miflora'))));

        $refresh = $this->getCmd(null, 'refresh');
        $replace['#refresh_id#'] = $refresh->getId();

        return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'miflora', 'MiFlora')));
    }

    public function sendCommand($id, $type, $option)
    {
        log::add('MiFlora', 'debug', 'Lecture : ' . $id . ' ' . $type . ' ' . $option);
        $command = self::byId($id, 'MiFlora');
        $ip      = $command->getConfiguration('addressip');
        log::add('MiFlora', 'debug', 'Lecture : ' . $ip);
    }

}

