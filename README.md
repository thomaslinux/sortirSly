# Projet sorties

Projets d'organisations de sorties pour les élèves de l'ENI. Ce projet est conçu pour fonctionner avec wamp. Vous pouvez
télécharger wamp ici :

https://wampserver.aviatechno.net/

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
    - [ ] date en placeholder
    - [ ] trier les dates de sorties en affichant la dernière sortie créée
    - [ ] Création d'un nouveau lieu depuis le formulaire de création de sortie
- [ ] Afficher la barre de recherche multi critères dans la liste des sorties
    - [ ] Gérer un affichage par pagination (front ou back ?)

## V2 - API recherche de sorties

- [ ] selecteur de lieu en fonction de la ville dans le créateur de sorties

- [ ] Cocher un critère de recherche de sortie recharge dynamiquement la liste des sorties via un appel API
- [ ] Formulaire de création de sorties
    - [ ] Faire un menu déroulant 'calendrier' plus fonctionnel (menu creation / modification d'une sortie

## V3 - CSS et JS

- [ ] Basculer entre un affichage en liste des sorties, et un affichage en grille (css on check)
- [x] Modifier le thème pour éviter de perdre de l'espace
- [ ] Désactiver la grille au redimensionnement
- [ ] Trier les éléments de la liste de sorties dans le tableau de sorties (javascript)

# Modèle graphique html :

https://raw.githack.com/thomaslinux/sortirSly/refs/heads/master/assets/styles/template-list1.html

https://raw.githack.com/thomaslinux/sortirSly/refs/heads/master/assets/styles/template-list2.html
