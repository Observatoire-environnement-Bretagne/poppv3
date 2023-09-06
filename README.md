# ![](doc/logo1.png?raw=true "Title") POPP - PLATEFORME DES OBSERVATOIRES PHOTOGRAPHIQUES DES PAYSAGES 


La Plateforme des Observatoires Photographiques du Paysage est un projet à l’initiative du Conseil Régional de Bretagne, de la DREAL Bretagne et de l’UMR CNRS 6590 ESO. 
Elle est conçue avec une cinquantaine d'acteurs du paysage de Bretagne et hors Bretagne à partir de 2011. Ce travail collaboratif a pour finalité d’analyser les dynamiques paysagères du Sud-Ouest de la France.

C’est un outil de suivi des évolutions du paysage et de mesure des impacts des politiques publiques. Il s’accompagne d’une dimension sensible et participative qui permet de co-construire avec les habitants une mémoire collective de chaque territoire.


![](doc/accueil.png?raw=true "Title")

## Évolution de paysage
Les paysages sont constamment en évolution

- La matérialité biophysique des paysages se modifie.
- Les représentations que nous avons des paysages évoluent au gré du temps et selon les groupes sociaux d’appartenance.

Ces modifications peuvent s’effectuer de manière lente et imperceptibles, ou a contrario, elles peuvent être brutales et rapides.

Les Observatoires photographiques du paysage, les OPP, ont pour but de reconstituer les dynamiques paysagères passées au moyen de la reconduction photographique. 

Il s’agit alors de re-photographier le même paysage dans des conditions similaires (cadrage, météo, hauteur de trépied, etc.) à des intervalles de temps réguliers (tous les ans, tous 5 ans, etc.). 

![](doc/photo2.jpg?raw=true)  <-- 2010 / 2018 -->  ![](doc/photo1.jpg?raw=true)


## La politique territoriale

L’observation des dynamiques paysages est un enjeu fort à toutes les échelles : **du local au régional**. 

Comprendre les évolutions paysagères permet d’adapter la politique paysagère en fonction des mécanismes naturels et des actions humaines à l’œuvre sur les territoires. Avec les incitations et prescriptions juridiques (loi « paysages » de 1993, convention européenne du Paysage de 2000, loi pour la reconquête de la biodiversité, de la nature et des paysages de 2016 dite loi « Biodiversité »), les collectivités territoriales et toute autorité compétente sont invitées :

- À développer une politique spécifique dédiée au paysage en matière d’aménagement, de gestion et de protection (art. 1, convention européenne du paysage) ;
- À intégrer de manière transversale le paysage dans les politiques sectorielles en termes de transport, d’urbanisme, etc. (art. 5, convention européenne du paysage) ;
- À définir des objectifs de qualité paysagère (Article 171, loi « Biodiversité ») ;
- À inciter les populations à participer aux décisions publiques (convention d'Aarhus, convention européenne du paysage et loi « Biodiversité »).


## Un logiciel facilitateur des OPP

Ce logiciel facilite l'exploitation des OPP par la gouvernance territoriale. La plateforme, en tant que telle, est un projet qui fédère les différents acteurs qui composent la gouvernance sur les territoires. Plus de trente acteurs de la connaissance paysagère en Bretagne se sont réunis au fur et à mesure du projet pour partager leurs compétences, leurs moyens et leurs expériences. 

Finalement, la plateforme impulse une dynamique régionale autour des questions paysagères avec 4 OPP en 2011 contre 27 en 2019.

Puisqu'elle est co-construite avec les porteurs OPP, la plateforme correspond aux besoins des territoires. En effet, elle donne accès aux séries OPP et facilite leurs analyses. L’interface permet :

- De rechercher des séries OPP par localisation ;
- De rechercher des séries OPP par contenu (haie, maison individuelle, cours d’eau, etc.) ;
- De rechercher des séries OPP par type de changement (apparition de haie, disparition de haie, etc.) ;
- D’exporter les séries OPP et leurs données associées.

![](doc/photo3.JPG?raw=true)![](doc/photo4.JPG?raw=true) ![](doc/photo5.JPG?raw=true)![](doc/photo6.JPG?raw=true) ![](doc/photo7.JPG?raw=true)![](doc/photo8.jpg?raw=true) ![](doc/photo9.jpg?raw=true)![](doc/photo10.JPG?raw=true) ![](doc/photo11.JPG?raw=true)![](doc/photo4.JPG?raw=true)

## Logiciels utilisés

- Serveur : ![](doc/apache.png?raw=true)
- BDD : ![](doc/postgres.png?raw=true)
- Language : ![](doc/php.png?raw=true)

## Installation

#### Ligne de commande

```shell
$ git clone https://username:git.geofit.fr/geofit/popp-docker.git
```

configurer le fichier apps/popp/.env

Installer et lancer les dockers
```shell
$ docker-compose build
```
```shell
$ docker-compose up -d
```

Se connecter sur le Docker php
```shell
$ docker exec -it popp_php bash
```

Installer les librairies
```shell
$ yarn install
```
```shell
$ composer install
ou
$ php composer.phar install
```

Les scripts de bases de données sont dans le dossier apps/popp/data
A lancer à la main pour le moment

Modifier les fichiers chosen-sprite.png et chosen-sprite@2x.png dans le dossier node_modules/chosen-js/ par les fichiers dans public/assets/images/

Pour lancer le serveur en dev
```shell
$ yarn encore dev -w
```
```shell
$ php bin/console server:run
```


Pour le passer en mode prod
Modifier le fichier .env
`APP_ENV=prod`

```shell
$ yarn encore production 
```
```shell
$ php bin/console c:c
```
```shell
$ chown -R www-data:www-data var/cache
```
#### Dans le code :

##### Mettre les données locales par défault sur Occitanie 
Aller dans apps/popp/config/packages/translation.yaml

- Modifier la ligne 3 : default_locale: 'occ'

[Screen](doc/screen2.png)

##### Modifier l'URL de la base de données dans le .env
Aller dans le .env dans apps/popp/.env

- Modifier la ligne 23 : DATABASE_URL="pgsql://postgres:password@db:5432/popp"

password : votre mot de passe de la base de données

db : le nom du serveur

5432 : c'est le port a modifier si c'est pas le même

[Screen](doc/screen.png)


##### Modifier Les fichiers paramètre pour obtenir le bon dictionnaire de mots
Aller dans apps/popp/src/assets/scripts/custom

Deux fichiers à modifier :

- parametre.js a remplacer par parametre_bzh.js

- parametre_occ.js a remplacer par parametre.js

[Screen](doc/screen1.png)

##### A compléter...





**Lancer l'application à l'adresse /public/popp**

## Application

Une fois l'application lancé, connectez-vous et aller dans la section **Gérer les paramètres** (à gauche du site)

##### Remplacez les informations suivantes par vos informations :

url de l'application : URL_POPP -> http://127.0.0.1:8000/

Chemin de stockage des fichiers : PATH_FOLDER_FILES	-> C:\Users\projet\popp\popp\apps\popp\public\files

url de récupération des fichiers : URL_FOLDER_FILES -> http://127.0.0.1:8000/files


**Application paramètrée avec succès !**






