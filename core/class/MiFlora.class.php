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

class MiFlora extends eqLogic {
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
     activer cette version pour tester toutes les minutes, garder ensuite la suivante: une mesure par heure me semble suffisante
      public static function cron() {

            	foreach (eqLogic::byType('MiFlora', true) as $mi_flora) {
		  $macAdd = $mi_flora->getConfiguration('macAdd');
                  log::add('MiFlora', 'debug', 'mi flora mac add:'.$macAdd);
		  $mi_flora->getMesure($macAdd,$MiFloraData);
		  $mi_flora->traiteMesure($macAdd,$MiFloraData,$temperature,$moisture,$fertility,$lux);
		  $mi_flora->updateJeedom($temperature,$moisture,$fertility,$lux);
		}

      }
      /* */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom */
      public static function cronHourly() {
            	foreach (eqLogic::byType('MiFlora', true) as $mi_flora) {
		  $macAdd = $mi_flora->getConfiguration('macAdd');
                  log::add('MiFlora', 'debug', 'mi flora mac add:'.$macAdd);
		  $mi_flora->getMesure($macAdd,$MiFloraData);
		  $mi_flora->traiteMesure($macAdd,$MiFloraData,$temperature,$moisture,$fertility,$lux);
		  $mi_flora->updateJeedom($temperature,$moisture,$fertility,$lux);
		}

      }
      /* */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDayly() {

      }
     */



    /*     * *********************Méthodes d'instance************************* */

    /************************** Pile de mise à jour **************************/ 

     /* fonction permettant d'initialiser la pile 
     * plugin: le nom de votre plugin
     * action: l'action qui sera utilisé dans le fichier ajax du pulgin 
     * callback: fonction appelé coté client(JS) pour mettre à jour l'affichage 
     */ 
    public function initStackData() {
        nodejs::pushUpdate('MiFlora::initStackDataEqLogic', array('plugin' => 'MiFlora', 'action' => 'saveStack', 'callback' => 'displayEqLogic'));
    }

   /* fonnction permettant d'envoyer un nouvel équipement pour sauvegarde et affichage, 
     * les données sont envoyé au client(JS) pour être traité de manière asynchrone
     * Entrée: 
     *      - $params: variable contenant les paramètres eqLogic
     */
    public function stackData($params) {
        if(is_object($params)) {
            $paramsArray = utils::o2a($params);
        }
        nodejs::pushUpdate('MiFlora::stackDataEqLogic', $paramsArray);
    }
       /* Fonction appelé pour la sauvegarde asynchrone
     * Entrée: 
     *      - $params: variable contenant les paramètres eqLogic
     */
    public function saveStack($params) {
        // inserer ici le traitement pour sauvegarde de vos données en asynchrone
    }

    /* fonction appelé avant le début de la séquence de sauvegarde */
    public function preSave() {
    }

    /* fonction appelé pendant la séquence de sauvegarde avant l'insertion 
     * dans la base de données pour une mise à jour d'une entrée */
    public function preUpdate() {
    }

 
    public function preInsert() {
        
    }

    public function postInsert() {
        
    }


    public function postSave() {
        
    }


    public function postUpdate() {
      		$cmdlogic = MiFloraCmd::byEqLogicIdAndLogicalId($this->getId(), 'temperature');
		if (!is_object($cmdlogic)) {
			$MiFloraCmd = new MiFloraCmd();
			$MiFloraCmd->setName(__('Temperature', __FILE__));
			$MiFloraCmd->setEqLogic_id($this->id);
			$MiFloraCmd->setLogicalId('temperature');
			$MiFloraCmd->setConfiguration('data', 'temperature');
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
			$MiFloraCmd->setConfiguration('data', 'temperature');
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
			$MiFloraCmd->setType('info');
			$MiFloraCmd->setSubType('numeric');
			$MiFloraCmd->setUnite('lx');
			$MiFloraCmd->setIsHistorized(1);
			$MiFloraCmd->save();
		}
		

        
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*     * **********************Getteur Setteur*************************** */

    public function getMesure($macAdd,&$MiFloraData) {
	   log::add('MiFlora', 'debug', 'macAdd:'.$macAdd);
	   $MiFloraData='';
	   // $MiFloraData='Characteristic value/descriptor: e1 00 00 8b 00 00 00 10 5d 00 00 00 00 00 00 00 \n';
	   // $MiFloraData='Characteristic value/descriptor read failed: Internal application error: I/O';
	  //TODO get data
	   //TODO: tester chaine error et gerer erreur

	   /* Work in progress -- hardcoded values to be changed */
           $ip='10.182.207.58';
	   $port='22';
	   $user='pi';
	   $pass='YourPasswd';


	   //eqLogic::
	   //$id=$eqLogic->getId();
	   //	   $eqLogic = $this->getEqLogic();
	   //      $miflora = self::byId($eqLogic->getId(), 'MiFlora');
	   // $miflora = self::byId($eqLogic->getId(), 'MiFlora');
	   //$miflora = self::byId(242, 'MiFlora');
	   //log::add('MiFlora', 'debug', 'miflora-obj:'. $miflora);
	   //$ip=$miflora->getConfiguration('addressip');
	   //$port=$miflora->getConfiguration('portssh');
	   //$user=$miflora->getConfiguration('user');
	   //$pass=$miflora->getConfiguration('password');
	   log::add('MiFlora', 'debug', 'ip:'.$ip);
	   log::add('MiFlora', 'debug', 'port:'.$port);
	   log::add('MiFlora', 'debug', 'user:'.$user);
	   log::add('MiFlora', 'debug', 'pass:'.$pass);
	   
			
	   log::add('MiFlora', 'debug', 'connexion SSH ...');
	   if (!$connection = ssh2_connect($ip,$port)) {
	     log::add('MiFlora', 'error', 'connexion SSH KO');
	   }else{ 
	     if (!ssh2_auth_password($connection,$user,$pass)){
	       log::add('MiFlora', 'error', 'Authentification SSH KO');
	     }else{ 
	       log::add('MiFlora', 'debug', 'Commande par SSH'); 
	       $pico = ssh2_exec($connection,"gatttool -b ".$macAdd." --char-read -a 0x35");
	       stream_set_blocking($pico, true);
	       $result = stream_get_contents($pico);						
	       log::add('MiFlora', 'debug', 'SSH result:'.$result);
	       $MiFloraData=$result;
	       $closesession = ssh2_exec($connection, 'exit');
	       stream_set_blocking($closesession, true);
	       stream_get_contents($closesession);
	       }
	   }

	   
           log::add('MiFlora', 'debug', 'MiFloraData:'.$MiFloraData);
    }
    
    public function traiteMesure($macAdd,$MiFloraData,&$temperature,&$moisture,&$fertility,&$lux) {
         // process data
         // log::add('MiFlora', 'debug', 'MiFloraData:'.$MiFloraData);
	 $MiFloraData = explode(": ", $MiFloraData);
	 // log::add('MiFlora', 'debug', 'MiFloraDataExplode:'.$MiFloraData[1]);
	 $MiFloraData = explode(" ", $MiFloraData[1]);
	 $temperature=hexdec($MiFloraData[1].$MiFloraData[0])/10;
	 $moisture=hexdec($MiFloraData[7]);
	 $fertility=hexdec($MiFloraData[8]);
	 $lux=hexdec($MiFloraData[4].$MiFloraData[3]);
         log::add('MiFlora', 'debug', $macAdd.' Temperature:'.$temperature);
         log::add('MiFlora', 'debug', $macAdd.' Moisture:'.$moisture);
         log::add('MiFlora', 'debug', $macAdd.' Fertility:'.$fertility);
         log::add('MiFlora', 'debug', $macAdd.' Lux:'.$lux);
    }
    public function updateJeedom($temperature,$moisture,$fertility,$lux) {

	 // store into Jeedom DB
      if ($temperature==0 && $moisture==0 && $fertility==0 && $lux==0) {
	 log::add('MiFlora', 'error', 'Toutes les mesures a 0, erreur de connection Mi Flora');
      } else {
	 $cmd = $this->getCmd(null, 'temperature');
	 if (is_object($cmd)) {
	   // $cmd->setCollectDate($date);
	   $cmd->event($temperature);
	   log::add('MiFlora', 'debug', $macAdd.' Store Temperature:'.$temperature);
	 }
	 $cmd = $this->getCmd(null, 'moisture');
	 if (is_object($cmd)) {
	   $cmd->event($moisture);
	   log::add('MiFlora', 'debug', $macAdd.' Store Moisture:'.$moisture);
	 }
	 $cmd = $this->getCmd(null, 'fertility');
	 if (is_object($cmd)) {
	   $cmd->event($fertility);
	   log::add('MiFlora', 'debug', $macAdd.' Store Fertility:'.$fertility);
	 }
	 $cmd = $this->getCmd(null, 'lux');
	 if (is_object($cmd)) {
	   $cmd->event($lux);
	   log::add('MiFlora', 'debug', $macAdd.' Store Lux:'.$lux);
	 }
      }
	 
    }
    public function sendCommand( $id, $type, $option ) {
      log::add('MiFlora', 'debug', 'Lecture : '.$id. ' ' . $type . ' ' . $option);
      $playtts = self::byId($id, 'MiFlora');
      $ip=$playtts->getConfiguration('addressip');
      log::add('MiFlora', 'debug', 'Lecture : '.$ip);
    }

}

class MiFloraCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

  //    public function execute($_options = array()) {
	public function execute($_options = null) {
	  log::add('MiFlora', 'info', 'Commande recue : ' . $_options['message']);
	  $eqLogic = $this->getEqLogic();
	  MiFlora::sendCommand($eqLogic->getId(), $this->getLogicalId(), $_options['message']);
	  return true;
	}
      
        

    /*     * **********************Getteur Setteur*************************** */
}

?>
