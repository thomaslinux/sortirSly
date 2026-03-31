# Projet sorties

Projets d'organisations de sorties pour les élèves de l'ENI.

Ce projet est conçu pour fonctionner avec wamp. Vous pouvez
télécharger wamp ici :

https://wampserver.aviatechno.net/

Et télécharger composer ici :
https://getcomposer.org/download/

# Installation du projet

Dans le dossier d'installation de wamp, dans www :

```
git clone https://github.com/thomaslinux/sortirSly.git sortirSly
cd sortirSly
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

Faire un composer install car ajout du composer league/csv

# TODO / Roadmap

## V0 Créer les entités

- [x] Participant en tant que User
- [x] Sortie
    - [x] dateHeureDebut >= dateLimiteInscription
    - [x] nbPlaces et nom sont non nullables
- [x] Créer les relations
    - [x] Associer Users et Campus
    - [x] Associer Sortie et Campus
    - [x] Associer Sortie et Users
- [x] Créer les fixtures avec associations
- [x] Récupérer et afficher la liste des sorties

<img height="512px" alt="Diagramme de classe du projet" src="CONCEPTION/UML%20Class/sorties_diagramme_classe.png" />

## V1

- [ ] Formulaire de création de sorties
    - [x] dateLimiteInscription > dateHeureDebut (back)
    - [x] date par défaut est aujourd'hui, front date est aujourd'hui
    - [/] date en placeholder
    - [ ] trier les dates de sorties en affichant la dernière sortie créée
    - [ ] Création d'un nouveau lieu depuis le formulaire de création de sortie
    - [x] réduire le nombre de query sur la liste
- [x] Liste des sorties
    - [x] Afficher la barre de recherche multi critères dans la liste des sorties
        - [/] Gérer un affichage par pagination (front ou back ?)
    - [x] Afficher les dates au format (europe/paris) car en UTC dans la database

## V2 - API recherche de sorties

- [ ] selecteur de lieu en fonction de la ville dans le créateur de sorties
- [ ] Cocher un critère de recherche de sortie recharge dynamiquement la liste des sorties via un appel API
- [ ] Formulaire de création de sorties
    - [ ] Faire un menu déroulant 'calendrier' plus fonctionnel (menu creation / modification d'une sortie

## V3 - CSS et JS

- [x] Styliser la page de détails de la sortie
- [x] Styliser la page d'annulation de la sortie
- [ ] Styliser la page de réinitialisation de mot de passe
    - [ ] Tester si le mot de passe est bien reset
    - [ ] Tester le style sur la page reset depuis un lien par mail
- [x] Modifier le thème pour éviter de perdre de l'espace
- [ ] Désactiver la grille au redimensionnement
- [ ] Basculer entre un affichage en liste des sorties, et un affichage en grille (css on check)

  ~~Récupérer les états historisés uniquement pour l'admin~~
  (uniquement en BDD même pour les admins pas visibles dans la liste des sorties

## V4 - Bonus

- [x] Trier les éléments de la liste de sorties dans le tableau de sorties (javascript)
- [-] Masquer des sorties en fonction de leur Etat
- [x] Recherche Javascript
    - [x] Recherche javascript désactivable côté client
    - [x] Croix pour effacer le champ de recherche actuel
    - [ ] Recherche rapide utilisateurs inscrits
    - [ ] Recherche rapide organisateur

## V5 - Après test utilisateur
- [ ] Gestion des utilisateur
  - [ ] Chaque action dans sa colonne nommée plus que la colonne nommée action ?
  - [ ] Rendre inactif plus que désactiver comme texte on hover

# Modèle graphique html :

https://raw.githack.com/thomaslinux/sortirSly/refs/heads/master/CONCEPTION/template-list1.html

https://raw.githack.com/thomaslinux/sortirSly/refs/heads/master/CONCEPTION/template-list2.html
