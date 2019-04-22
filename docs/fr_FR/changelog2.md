# Changelog
### Version 3.0.3 - 22 Avril 2019 Stable
* Modification de la doc

### Version 3.0.2 - 9 Mars 2019 Stable
* Corrige un bug: le parcours des objets s'arrete si un objet ne reponds pas

### Version 3.0.1 - 10 Fevrier 2019 Stable
* Ajoute un nouveau range de MacAdd pour les Parrots
* clarification les erreurs/warnings
* ajout de questions a la FAQ
* Correction de l'erreur de mesure Lux Parrot, suite à un commentaire sur le forum

### Version 3.0.0 - 14 Mai 2018 Bêta - 30 Juin Stable
* Gestion d'antennes déportés
   - Possibilité de créer des antennes
   - Possibilité pour chaque objet de spécifier par quelle antenne il va être lu
   -  Attention:
        - le plugin a maintenant des dépendances à lancer, elles permettent de s'assurer que les packages nécessaires sont bien présents. Il n'est pas indispensable de les lancer si tous les packages sont présents.
        - il peut être nécessaire de le désactiver puis de l’activer pour bien mettre à jours les nouveaux champs, les données sont conservées lors de ce processus.
        - le mode déporté est supprimé au profit des antennes. J'ai prévu la création d'une antenne nommée **deporte** pour gérer la migration.
* Onglet **santé** permettant de voir de manière synthétique l'état des MiFlora.
* Onglet **scan** qui permet de trouver la liste des objets pas encore déclarés dans MiFlora. Le résultat du scan n'est pas fiable à 100%, si une antenne est occupée tous les objets vus seulement par cette antenne ne seront pas détectés
* Ajout d'un helper dans le menu **add** permettant de lancer le scan et d'ajouter un objet en cliquant sur la liste des MAC adresses plutôt que de les saisir à la main. 
* Ajout de valeurs par défaut pour l'alerte batterie faible pour chaque objet si rien n'est défini au niveau global Jeedom
* Vérification des batteries par rapport au seuil du plugin et aux seuils globaux Jeedom si rien n'est défini dans le plugin.
* Ajout de la fonctionnalité refresh et passage du minimum de 5 à 15 minutes.
    - Cette fonctionnalité est utilisable depuis un scénario ou en cliquant sur le widget en mode desktop.
    - Attention de bien mettre une fréquence d'au moins 15 minutes pour vos objets existants.

* Gestion du Parrot flower de la même manière que les MiFlora
