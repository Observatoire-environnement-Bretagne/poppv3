DROP TABLE if exists actualites;
CREATE TABLE actualites (
	actualite_id serial PRIMARY KEY, 
	actualite_editor text,
	actualite_ordre int
);