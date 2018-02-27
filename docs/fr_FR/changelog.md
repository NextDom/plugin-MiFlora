Version 2.0.0
--
Integration dans Jeedom-Plugins-Extra 

Version 1.2.5: 31 Mai 2017
--
* Fix d'un bug sur la fertilité (recuperation des 2 bytes au lieu d'un seul)
* Changement de l'icone pour respecter la charte Jeedom

Version 1.2.4: 18 Mars 2017
--
* Inversions du change log pour plus de lisibilite
* Gestion du firmware 2.9.2 - inversion du test seul 2.6.2 est geré avec l'ancienne methode

Version 1.2.3: 16 Janvier en Beta
--
* Amelioration de la gestion des erreurs de lecture

Version 1.2.2:
--
* Fix bug script python

Version 1.2.1: 7 janvier 2017
--
* fix des températures négatives (pull request de frederic34)

Version 1.2: 5 janvier 2017
--
* multiple corrections orthographiques
* migration de la recuperation des données vers le script python
* amélioration de la FAQ

Version 1.1: 11 décembre 2016
--
* Utilisation du paramètre hci et sec-level pour le firmware 2.6.6 et 2.7.0. (modif du script python)

Version 1.0.1: 11 décembre 2016
--
* Fix bug pour les nouveaux firmware en mode local

Version 1.0 : 10 décembre 2016
--
* Support du firmware 2.6.6 et 2.7.0 en mode local et déporté
* Choix du no de hci dans la config, très utile si le hci0 est affecté à un plugin exclusif ou s'il ne gère pas le Bluetooth BLE
* Choix du niveau de sécurité Bluetooth afin de résoudre les problèmes de certains dongles qui ne gèrent pas le niveau high
* le hci et niveau de sécurité n'est pas pris en compte pour le firmaware 2.6.6 et 2.7.0. (modif du script python pas encore testées)

Version 0.8 : 9 décembre 2016
--
* Affichage des items de Configuration en champ lecture seul.
* Widget desktop et mobile dédié.
* ajout de la date de dernière collecte
* forcer la récupération du firmware s'il est vide
* Le mode debug provoque une collection de données toutes les minutes, attention à ne pas le laisser en permanence surtout si vous êtes sur une carte SD
* Définir les nouveaux équipements en visible et activés par défaut
* Ajout de l'unité pour 'fertility', ce champ mesure en fait la conductivité ce qui permet de déduire la fertilité du sol

Versions 0.1 ... 0.7: 24 Novembre 2016 - 2 Décembre 2016
--
* Version initiale du plugin.

Idées pour les versions suivantes :
--
* Ajouter le test des config : hci et sec-level doivent etre rempli
* Choix du log erreur ou info en cas de problème de connexion au bout de 4 essais
* Tester que la macadd est <> entre les Équipements (erreur de copie colle)
* Tester l'état du Bluetooth et le redémarrer en cas de problème (sudo hciconfig hci0 down,sudo hciconfig hci0 up) par exemple l'erreur connect error: Connection refused (111)
* Provoquer une récupération des données tout de suite après l'ajout du matériel ? ou une commande pour forcer l'update
* Bouton pour détecter un nouveau MiFlora (éviter de trouver l'adresse à la main)
--
Cette liste de questions provient essentiellement de vos questions sur le fil de discussion MiFlora du forum.
--
