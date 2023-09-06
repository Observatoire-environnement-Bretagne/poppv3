
INSERT INTO pays(pays_nom) VALUES ('FRANCE');
INSERT INTO pays(pays_nom) VALUES ('ALLEMAGNE');
INSERT INTO pays(pays_nom) VALUES ('ITALIE');
INSERT INTO pays(pays_nom) VALUES ('MONACO');
INSERT INTO pays(pays_nom) VALUES ('ROYAUME-UNI');
INSERT INTO pays(pays_nom) VALUES ('BELGIQUE');
INSERT INTO pays(pays_nom) VALUES ('ESPAGNE');
INSERT INTO pays(pays_nom) VALUES ('ANDORRE');


DELETE FROM pays WHERE 
pays_nom not in ('FRANCE', 'ESPAGNE')