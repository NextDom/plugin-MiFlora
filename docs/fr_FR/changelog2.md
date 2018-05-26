# Changelog

### Version 3.0.0 - 14 Mai 2018 Bêta
* Gestion d'antennes déportés
   - Possibilité de créer des antennes
   - Possibilité pour chaque objet de spécifier par quelle antenne il va être lu
   -  Attention:
        - le plugin a maintenant des dépendances à lancer, elles sont utile uniquement pour gèrer la migration à la version 3.0.0 pour les utilisateurs actuels s'il n'utilisent pas le Market pour la mise à jour. Pour tous les autres cas, les dependances ne sont pas utile.
        - il peut être nécessaire de le désactiver puis de l’activer pour bien mettre a jours les nouveaux champs, les données sont conservées lors de ce processus.
* Onglet santé permettant de voir de manière synthétique l'état des MiFlora.
* Ajout de valeurs par défaut pour l'alerte batterie faible.
* Ajout de la fonctionnalité refresh et passage du minimum de 5 à 15 minutes.
    - Cette fonctionnalité est utilisable depuis un scénario ou en cliquant sur le widget en mode desktop.
    - Attention de bien mettre une fréquence d'au moins 15 minutes pour vos objets existants.

* Gestion du Parrot flower.

