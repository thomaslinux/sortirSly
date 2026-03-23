# Projet sorties

Projets d'organisations de sorties pour les élèves de l'ENI. Ce projet est conçu pour fonctionner avec wamp. Vous pouvez télécharger wamp ici :

https://wampserver.aviatechno.net/

# Installation du projet

Dans le dossier d'installation de wamp, dans www :
```
git clone https://github.com/thomaslinux/sortirSly.git sortirSly
cd sortirSly
composer install
```

# TODO / Roadmap
## V0 Créer les entités
- [x] Participant en tant que User 
- [x] Sortie
    - [ ] dateHeureDebut >= dateLimiteInscription
    - [ ] dateLimiteInscription default = dateHeureDebut
    - [x] nbPlaces et nom sont non nullables
- [ ] Créer les relations
- [ ] Créer les fixtures

<img height="512px" alt="Diagramme de classe du projet" src="CONCEPTION/UML%20Class/sorties_diagramme_classe.png" />
## V1
- [ ] Créer les Controller


## V2 - CSS
- [ ] Basculer entre un affichage en liste des sorties, et un affichage en grille (css on check)

## V3 - API recherche de sorties
- [ ] Cocher un critère de recherche de sortie recharge dynamiquement la liste des sorties via un appel API
