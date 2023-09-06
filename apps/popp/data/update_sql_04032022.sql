ALTER TABLE serie ADD COLUMN serie_user_crea integer;
ALTER TABLE serie ADD COLUMN serie_date_crea timestamp without time zone;
ALTER TABLE serie ADD COLUMN serie_user_maj integer;
ALTER TABLE serie ADD COLUMN serie_date_maj timestamp without time zone;
	
ALTER TABLE serie
  ADD CONSTRAINT fk_serie_user_crea FOREIGN KEY (serie_user_crea) REFERENCES users (id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_user_crea
  ON serie(serie_user_crea);
	

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_user_maj FOREIGN KEY (serie_user_maj) REFERENCES users (id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_user_maj
  ON serie(serie_user_maj);

/*
php bin/console doctrine:mapping:import "App\Entity" annotation --path=src/Entity --filter="Serie"
php bin/console make:entity --regenerate "App\Entity\LSerieAxeThematic"
php bin/console make:entity --regenerate "App\Entity\LSerieUnitePaysagereLocale"
php bin/console make:entity --regenerate "App\Entity\Serie"
*/

  
ALTER TABLE users ADD COLUMN datederpub timestamp without time zone;

/*
php bin/console doctrine:mapping:import "App\Entity" annotation --path=src/Entity --filter="Users"
php bin/console make:entity --regenerate "App\Entity\Users"
*/

DROP TABLE if exists actualites;
CREATE TABLE actualites (
	actualite_id serial PRIMARY KEY, 
	actualite_editor text
);

DROP TABLE if exists carrousel_photo;
CREATE TABLE carrousel_photo (
	carrousel_photo_id serial PRIMARY KEY, 
	carrousel_actualite_id integer, 
	carrousel_photo_file_id integer,
    carrousel_photo_titre character varying,
	carrousel_is_creating boolean
);
ALTER TABLE carrousel_photo
    ADD CONSTRAINT fk_carrousel_file_id FOREIGN KEY (carrousel_photo_file_id) REFERENCES file_manager (file_manager_id)
ON UPDATE NO ACTION ON DELETE NO ACTION;

ALTER TABLE carrousel_photo
    ADD CONSTRAINT fk_carrousel_actualite_id FOREIGN KEY (carrousel_actualite_id) REFERENCES actualites (actualite_id)
ON UPDATE NO ACTION ON DELETE NO ACTION;

/*ALTER TABLE public.carrousel_photo
    ADD COLUMN carrousel_photo_titre character varying;*/

ALTER TABLE actualites
  ADD COLUMN actualite_ordre int;

ALTER TABLE carrousel_photo
  ADD COLUMN carrousel_photo_ordre int;
