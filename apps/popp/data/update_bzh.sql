
alter table unite_paysage add column unite_paysage_ensemble_id integer;

ALTER TABLE unite_paysage
  ADD CONSTRAINT fk_unite_paysage_ensemble_id FOREIGN KEY (unite_paysage_ensemble_id) REFERENCES ensemble_paysager (ensemble_paysager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_unite_paysage_ensemble_id
  ON unite_paysage(unite_paysage_ensemble_id);

INSERT INTO parametre(prm_code, prm_valeur, prm_desc) VALUES ('APP_NAME', 'POPP-Breizh', 'Nom de l''application');
INSERT INTO parametre(prm_code, prm_valeur, prm_desc) VALUES ('APP_DESC', 'Plateforme des Observatoires Photographiques du Paysage de Bretagne', 'Description de l''application');
INSERT INTO parametre(prm_code, prm_valeur, prm_desc) VALUES ('RESP_NOM', 'Caroline Guittet', 'Nom du responsable');
INSERT INTO parametre(prm_code, prm_valeur, prm_desc) VALUES ('RESP_TITRE', 'Pôle paysages / Observatoire de l’Environnement en Bretagne', 'Rôle du responsable');
INSERT INTO parametre(prm_code, prm_valeur, prm_desc) VALUES ('RESP_MAIL', 'caroline.guittet[@]bretagne-environnement.fr', 'Email du responsable');
INSERT INTO parametre(prm_code, prm_valeur, prm_desc) VALUES ('RESP_TEL', '02 99 35 84 86', 'Téléphone du responsable');
INSERT INTO parametre(prm_code, prm_valeur, prm_desc) VALUES ('RESP_ADRESSE', '6-A rue du Bignon – 35000 Rennes', 'Adresse de l''organisme responsable');
INSERT INTO parametre(prm_code, prm_valeur, prm_desc) VALUES ('RESP_STRUCTURE', 'Observatoire de l''environnement en Bretagne', 'Organisme responsable');
INSERT INTO parametre(prm_code, prm_valeur, prm_desc) VALUES ('RESP_CONTACT', 'le pôle paysages de l''OEB', 'Organisme responsable à contacter');
INSERT INTO parametre(prm_code, prm_valeur, prm_desc) VALUES ('SHOW_ACTUALITE', '0', 'Afficher l''onglet "Actualité" (1 pour afficher)');