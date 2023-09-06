
INSERT INTO region (region_nom) VALUES ('Guadeloupe');
INSERT INTO region (region_nom) VALUES ('Martinique');
INSERT INTO region (region_nom) VALUES ('Guyane');
INSERT INTO region (region_nom) VALUES ('La Réunion');
INSERT INTO region (region_nom) VALUES ('Mayotte');
INSERT INTO region (region_nom) VALUES ('Île-de-France');
INSERT INTO region (region_nom) VALUES ('Centre-Val de Loire');
INSERT INTO region (region_nom) VALUES ('Bourgogne-Franche-Comté');
INSERT INTO region (region_nom) VALUES ('Normandie');
INSERT INTO region (region_nom) VALUES ('Hauts-de-France');
INSERT INTO region (region_nom) VALUES ('Grand Est');
INSERT INTO region (region_nom) VALUES ('Pays de la Loire');
INSERT INTO region (region_nom) VALUES ('Bretagne');
INSERT INTO region (region_nom) VALUES ('Nouvelle-Aquitaine');
INSERT INTO region (region_nom) VALUES ('Occitanie');
INSERT INTO region (region_nom) VALUES ('Auvergne-Rhône-Alpes');
INSERT INTO region (region_nom) VALUES ('Provence-Alpes-Côte d''Azur');
INSERT INTO region (region_nom) VALUES ('Corse');

DELETE FROM region 
WHERE region_nom not in ('Occitanie', 'Nouvelle-Aquitaine')
