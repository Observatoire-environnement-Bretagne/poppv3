# POPP Breizh


## Application

### Screenshots



## Developers

### Instalation

__Create application user and run HTTP server__
```shell
$ php bin/console doctrine:schema:create # Create database schema from entity model
$ php bin/console fos:user:create --super-admin # Create first application user with admin privileges
$ php bin/console server:run # Run http server
```

Pour installer les dépendances
```shell
$ yarn install
$ php composer.phar install
```

Pour lancer le serveur en dev
```shell
$ yarn encore dev -w
$ php bin/console server:run
```

Pour le passer en mode prod
Modifier le fichier .env
`APP_ENV=prod`

```shell
$ yarn encore production 
$ php bin/console c:c
$ chown -R www-data:www-data var/cache
```

puis lancer l'application à l'adresse /public/popp

