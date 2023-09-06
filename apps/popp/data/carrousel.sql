DROP TABLE if exists carrousel_photo;
CREATE TABLE carrousel_photo (
	carrousel_photo_id serial PRIMARY KEY, 
	carrousel_actualite_id integer, 
	carrousel_photo_file_id integer,
    carrousel_photo_titre character varying,
	carrousel_is_creating boolean, 
	carrousel_photo_ordre integer

);
ALTER TABLE carrousel_photo
    ADD CONSTRAINT fk_carrousel_file_id FOREIGN KEY (carrousel_photo_file_id) REFERENCES file_manager (file_manager_id)
ON UPDATE NO ACTION ON DELETE NO ACTION;

ALTER TABLE carrousel_photo
    ADD CONSTRAINT fk_carrousel_actualite_id FOREIGN KEY (carrousel_actualite_id) REFERENCES actualites (actualite_id)
ON UPDATE NO ACTION ON DELETE NO ACTION;

/*ALTER TABLE public.carrousel_photo
    ADD COLUMN carrousel_photo_titre character varying;*/


