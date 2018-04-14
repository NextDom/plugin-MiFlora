# Configuration

La partie configuration du plugin permet :

* de choisir la fréquence de relevés des informations du MiFlora, de toutes les 5, 10, 15, 30 minutes, toutes les heures, jusqu'à toutes les 12 heures,
* de choisir le dongle/port Bluetooth (**_hci0_** en général, `hciconfig` permet de lister ceux disponibles sur votre système),
* de choisir le niveau de sécurité de la communication Bluetooth (`high` sauf si ce n'est pas supporté par votre système),
* de choisir entre un mode local et un mode déporté.

Pour chaque équipement, il faut rentrer l'adresse Bluetooth de l'équipement.

Il est possible de trouver celle-ci en utilisant les commandes :

```
bluetoothctl
scan on
```

Les adresses des MiFlora apparaissent comme ci-dessous:
```
[NEW] Device C4:7C:8D:xx:xx:xx Flower mate
[NEW] Device C4:7C:8D:xx:xx:xx Flower care
scan off
quit
```
La fréquence de lecture des données est par défaut celle de la configuration globale. Elle peut être modifié pour chaque équipement.

### Utilisation en déporté

Il est possible que Jeedom pilote un appareil déporté qui se chargera de la communication Bluetooth avec les MiFlora.

Dans ce cas, il faut renseigner les paramètres de connexion SSH entre Jeedom et cet appareil déporté :

Le choix déporté active la partie configuration qui permet de saisir une adresse IP, un port, un nom d'utilisateur ainsi que le mot de passe associé pour l'appareil distant.
Le plugin va alors se connecter en SSH à l'IP saisie précédemment et récupérer les informations du MiFlora en Bluetooth grâce à la commande `gatttools`

Aucun Jeedom n'est nécessaire sur l'équipement distant.

### Prérequis

Il faut installer le Bluetooth et s'assurer que `gatttool --device=hci0 -b _macAddMiFlora_ --char-read -a 0x35 --sec-level=high` fonctionne sur l'appareil cible (selon le choix local ou déporté).

### Configuration avancée

**_hci :_** permet de choisir le dongle Bluetooth pour ceux qui en ont plusieurs.

**_niveau de sécurité :_** permet de choisir le niveau de sécurité Bluetooth, `high` semble bien dans la majorité des cas, cependant changer le niveau de sécurité semble résoudre certains problèmes de connexions.

### Mode debug

Le mode debug permet de lancer en permanence (toutes les minutes), la récupération des données MiFlora. Il convient de limiter son utilisation au debug.
Laisser le mode debug en permanence va affecter la durée de vie du support de stockage, spécialement les cartes SD et vider plus rapidement la pile de l'appareil.
