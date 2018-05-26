# FAQ

### Quelle est la différence entre MiFlora et BLEA
> MiFlora ne gère que les plantes, BLEA est un plugin pour tous les objets Bluetooth LE, il est donc beaucoup plus complexe, 
il nécéssite des dépendances, a un systeme de démon, il est adapté pour gérer une multitude de types d'objets Bluetooth LE 
mais nécéssite plus de suivi et de maintenance, principalement les démons et dépendances lors des mise à jour

### Est-ce que ce plugin s'appuie sur des API tiers ?

> Le plugin utilise le Bluetooth pour récupérer les informations du MiFlora.
Il faut installer le Bluetooth et s'assurer que `gatttool -b macAddMiFlora --char-read -a 0x35` fonctionne sur le device cible.

### Est ce que ce plugin monopolise le Bluetooth ?

> Non pas du tout, il a besoin du Bluetooth pour chaque relevé, cf question suivante pour plus de détails sur le nombre de relevés par jour.


### Combien de fois par jour les mesures sont-elles récupérées ?

> C'est défini dans la configuration globale du plugin, pour tous les objets : de toutes les 15 minutes à toutes les 12 heures.
Il est possible de configurer une fréquence differente par MiFlora, `defaut` permet d'utiliser la frequence globale.

>J'utilise le modulo de l'heure actuelle avec la fréquence saisie en paramètre. +
Attention: en mode debug, les données sont récupérés en permanence indépendamment de la configuration.

> Les informations statiques (batterie, nom de l appareil, version du firmware) sont récupérées toutes les 12 heures : à minuit et midi.


### Avec quelle version de firmware ce plugin est-il compatible ?

> Il est compatible avec toutes les versions connues à ce jour (2.9.2) depuis la version 1.0 du plugin.


### Je possède un RPI3, J'ai dû désactiver le Bluetooth interne pour ne pas avoir d'interférence avec le Zwave (razberry). Est-ce qu'il faut toujours garder le Bluetooth interne désactivé pour résoudre ce souci ? Sinon est-ce que n'importe quelle clef USB BT fait l'affaire pour être compatible avec les MiFlora et le RPI3 ?

> Dans ce cas il faut prendre un dongle BLE. Le problème avec le razberry c'est seulement si on utilise le contrôleur interne.


### Je souhaite contribuer à l'amélioration de ce plugin, est ce possible ?

> Bien sur, le code est sur GitHub : rjullien/plugin-MiFlora, vous pouvez soumettre des pull requests.

### gatttool est instable et se bloque sur RPI

> Il y a beaucoup de configuration qui peuvent générer ce problème. Avec Pixel il faut faire attention d'avoir un seul gestionnaire de bluetooth.
BlueZ est incompatible avec blueman (sudo apt-get remove blueman)

### Le plugin fonctionne bien: que puis je faire avec ?

> Les valeurs d'humidité, de fertilité, de luminosité et de températures sont accessible depuis des scénarios.

> Il est possible de lire ces valeurs, de les comparer à un seuil et d'alerter en cas de dépassement du seuil, par exemple pour arroser une plante.

> Les alertes peuvent être données par du 'text to speech' (plugin playTTS par exemple), par notification sur smartphone (plugin pushbullet), par SMS ...

> Les seuils peuvent être trouvés en utilisant la base de plantes de Xiaomi ou celle de Parrot à défaut un seuil entre 14 et 16 semble convenir à une majorité de plantes d'intérieur.

> Il est aussi possible de réguler un arrosage automatique, MiFlora semble bien résister aux intempéries

