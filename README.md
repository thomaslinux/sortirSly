# Projet sorties

Projets d'organisations de sorties pour les élèves de l'ENI. Ce projet est conçu pour fonctionner avec wamp. Vous pouvez télécharger wamp ici :

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
    - [ ] dateLimiteInscription > dateHeureDebut

## V2 - CSS
- [ ] Basculer entre un affichage en liste des sorties, et un affichage en grille (css on check)

## V3 - API recherche de sorties
- [ ] Cocher un critère de recherche de sortie recharge dynamiquement la liste des sorties via un appel API
