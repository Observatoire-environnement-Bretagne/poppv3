
/* evolution_paysage */
DROP TABLE if exists evolution_paysage;
CREATE TABLE evolution_paysage (
	evolution_paysage_id serial PRIMARY KEY, 
	evolution_paysage_nom character varying(255),
	evolution_paysage_nom_lien character varying(255)
);

/* OPP */
DROP TABLE if exists opp;
Create table opp (
	opp_id serial PRIMARY KEY, 
	opp_nom character varying(255), 
	opp_desc text, 
	opp_technicite text, 
	opp_annee_creation integer, 
	opp_niv_territ character varying(255), 
	opp_valo character varying(255), 
	opp_poids integer,
	opp_porteur_opp_id integer,
	opp_participative boolean
);

/* axe_thematic */
DROP TABLE if exists axe_thematic;
Create table axe_thematic (
	axe_thematic_id serial PRIMARY KEY, 
	axe_thematic_nom character varying(255), 
	axe_thematic_desc text,
	axe_thematic_poids integer
);

/* typologie_paysage */
DROP TABLE if exists typologie_paysage;
Create table typologie_paysage (
	typologie_paysage_id serial PRIMARY KEY, 
	typologie_paysage_nom character varying(255), 
	typologie_paysage_desc text,
	typologie_paysage_poids integer
);


/* langue */
DROP TABLE if exists langue;
Create table langue (
	langue_id serial PRIMARY KEY, 
	langue_nom character varying(255), 
	langue_desc text,
	langue_poids integer
);


/* licence */
DROP TABLE if exists licence;
Create table licence (
	licence_id serial PRIMARY KEY, 
	licence_nom character varying(255), 
	licence_desc text,
	licence_poids integer
);

/* pays */
DROP TABLE if exists pays;
Create table pays (
	pays_id serial PRIMARY KEY, 
	pays_nom character varying(255), 
	pays_desc text,
	pays_poids integer
);

/* format */
DROP TABLE if exists format;
Create table format (
	format_id serial PRIMARY KEY, 
	format_nom character varying(255), 
	format_desc text,
	format_poids integer
);

/* region */
DROP TABLE if exists region;
Create table region (
	region_id serial PRIMARY KEY, 
	region_nom character varying(255), 
	region_desc text,
	region_poids integer
);

/* departement */
DROP TABLE if exists departement;
Create table departement (
	departement_id serial PRIMARY KEY, 
	departement_nom character varying(255), 
    departement_code character varying(255), 
	departement_desc text,
	departement_poids integer
);


/* commune */
DROP TABLE if exists commune;
Create table commune (
	commune_id serial PRIMARY KEY, 
	commune_nom character varying(255), 
	commune_insee character varying(255), 
	commune_desc text,
	commune_poids integer
);


/* ensemble_paysager */
DROP TABLE if exists ensemble_paysager;
Create table ensemble_paysager (
	ensemble_paysager_id serial PRIMARY KEY, 
	ensemble_paysager_nom character varying(255), 
	ensemble_paysager_desc text,
	ensemble_paysager_poids integer
);

/* unite_paysage */
DROP TABLE if exists unite_paysage;
Create table unite_paysage (
	unite_paysage_id serial PRIMARY KEY, 
	unite_paysage_nom character varying(255), 
	unite_paysage_desc text,
	unite_paysage_poids integer,
	unite_paysage_ensemble_id integer
);

/* unite_paysage_locale */
DROP TABLE if exists unite_paysage_locale;
Create table unite_paysage_locale (
	unite_paysage_locale_id serial PRIMARY KEY, 
	unite_paysage_locale_nom character varying(255), 
	unite_paysage_locale_desc text,
	unite_paysage_locale_poids integer
);


/* porteur_opp */
DROP TABLE if exists porteur_opp;
Create table porteur_opp (
	porteur_opp_id serial PRIMARY KEY, 
	porteur_opp_nom character varying(255), 
	porteur_opp_logo_id integer,
	porteur_opp_desc_courte text,
	porteur_opp_adresse text,
	porteur_opp_contact_ref text,
	porteur_opp_desc_tech text,
	porteur_opp_email character varying(255),
	porteur_opp_telephone character varying(255),
	porteur_opp_site_web character varying(255),
	porteur_opp_preocupation_paysagere text,
	porteur_opp_financeur boolean DEFAULT false
);

/* photo */
DROP TABLE if exists photo;
Create table photo (
	photo_id serial PRIMARY KEY, 
	photo_titre character varying(255), 
	photo_auteur character varying(255), 
	photo_desc_changement text,
	photo_date_desc timestamp without time zone,
	photo_date_prise timestamp without time zone,
	photo_format_id integer,
	photo_identifiant_int character varying(255),
	photo_licence_id integer,
	photo_licence_fiche_id integer,
	photo_file_id integer,
	photo_heure character varying(8),
	photo_type_appareil character varying(255),
	photo_focale real,
	photo_ouverture_dia character varying(255),
	photo_type_film character varying(255),
	photo_iso character varying(255),
	photo_poids_origine character varying(255),
	photo_inclinaison character varying(255),
	photo_hauteur real,
	photo_orientation real,
	photo_altitude integer,
	photo_coef_maree integer,
	photo_serie_id integer
);

/* thesaurus_tree */
DROP TABLE if exists thesaurus_tree;
CREATE TABLE thesaurus_tree (
	thesaurus_tree_id serial PRIMARY KEY, 
	thesaurus_tree_nom character varying(255), 
	thesaurus_tree_parent_id integer
);

/* l_photo_thesaurus */
DROP TABLE if exists l_photo_thesaurus;
CREATE TABLE l_photo_thesaurus (
	l_pt_id serial PRIMARY KEY, 
	l_pt_thesaurus_id integer,
	l_pt_photo_id integer
);


/* l_thesaurus_evolution */
DROP TABLE if exists l_thesaurus_evolution;
CREATE TABLE l_thesaurus_evolution (
	l_te_id serial PRIMARY KEY, 
	l_te_photo_thesaurus_id integer,
	l_te_evolution_id integer
);

/* document_ref */
DROP TABLE if exists document_ref;
Create table document_ref (
	document_ref_id serial PRIMARY KEY, 
	document_ref_identifiant_int character varying(255), 
	document_ref_auteur character varying(255), 
	document_ref_desc text,
	document_ref_date timestamp without time zone,
	document_ref_commentaire_date text,
	document_ref_type character varying(255),
	document_ref_format character varying(255),
	document_ref_source character varying(255),
	document_ref_langue_id integer,
	document_ref_site character varying(255),
	document_ref_licence_id integer,
	document_ref_nom character varying(255),
	document_ref_sous_titre character varying(255),
	document_ref_heure character varying(8),
	document_ref_periode character varying(255),
	document_ref_moment character varying(255),
	document_ref_lieu_conservation character varying(255),
	document_ref_orientation character varying(255),
	document_ref_altitude real,
	document_ref_coef_maree integer,
	document_ref_cote_doc character varying(255),
	document_ref_file_id integer
);

/* son */
DROP TABLE if exists son;
Create table son (
	son_id serial PRIMARY KEY, 
	son_titre character varying(255), 
	son_struct_resp_id integer, 
	son_auteur character varying(255),
	son_presentation text,
	son_lien_paysage text,
	son_date timestamp without time zone,
	son_type character varying(255),
	son_format character varying(255),
	son_heure character varying(8),
	son_type_mat character varying(255),
	son_traitement character varying(255),
	son_protocole character varying(255),
	son_contexte character varying(255),
	son_condition_meteo character varying(255),
	son_file_id integer,
	son_num_photo integer,
	son_langue_id integer,
	son_lieu text,
	son_licence_id integer,
	son_duree integer,
	son_serie_id integer
);

/* document */
DROP TABLE if exists document;
Create table document (
	document_id serial PRIMARY KEY, 
	document_titre character varying(255), 
	document_file_id integer, 
	document_legende text,
	document_serie_id integer
);

/* serie */
DROP TABLE if exists serie;
Create table serie (
	serie_id serial PRIMARY KEY, 
	serie_titre character varying(255), 
	serie_opp_id integer, 
	serie_typologie_id integer,
	serie_intention text,
	serie_desc text,
	serie_date timestamp without time zone,
	serie_porteur_opp_id integer,
	serie_identifiant_serie character varying(255),
	serie_identifiant_int character varying(255),
	serie_langue_id integer,
	serie_format_id integer,
	serie_pays_id integer,
	serie_region_id integer,
	serie_departement_id integer,
	serie_commune_id integer,
	serie_adresse character varying(255),
	serie_ensemble_paysage_id integer,
	serie_unite_paysagere_id integer,
	serie_freq_interval integer,
	serie_freq_period character varying(255),
	serie_geom_x real,
	serie_geom_y real,
	serie_obs_rephoto text,
	serie_croquis_id integer,
	serie_photo_trepied_id integer,
	serie_photo_ign_id integer,
	serie_photo_aerienne_id integer,
	serie_photo_context_id integer,
	serie_refdoc_id integer,
	serie_publie boolean DEFAULT true,
	serie_user_crea integer,
	serie_date_crea timestamp without time zone,
	serie_user_maj integer,
	serie_date_maj timestamp without time zone
);

/* l_serie_axe_thematic */
DROP TABLE if exists l_serie_axe_thematic;
CREATE TABLE l_serie_axe_thematic (
	l_sat_id serial PRIMARY KEY, 
	l_sat_serie_id integer,
	l_sat_axe_thematic_id integer
);

/* l_serie_unite_paysagere_locale */
DROP TABLE if exists l_serie_unite_paysagere_locale;
CREATE TABLE l_serie_unite_paysagere_locale (
	l_supl_id serial PRIMARY KEY, 
	l_supl_serie_id integer,
	l_supl_unite_paysage_locale_id integer
);

/* file_manager */
DROP TABLE if exists file_manager;
Create table file_manager (
	file_manager_id serial PRIMARY KEY, 
	file_manager_nom character varying(255), 
	file_manager_uri character varying(255),
	file_manager_mime character varying(255),
	file_manager_statut integer,
	file_manager_size bigint,
	file_manager_date bigint
);

/* lien_externe */
DROP TABLE if exists lien_externe;
CREATE TABLE lien_externe (
	lien_externe_id serial PRIMARY KEY, 
	lien_externe_serie_id integer,
	lien_externe_value character varying(255)
);
/* users */
DROP TABLE if exists users;
CREATE TABLE public.users
(
  id serial PRIMARY KEY,
  email character varying(180) NOT NULL,
  roles json NOT NULL,
  password character varying(255) NOT NULL,
  nom character varying(255) NOT NULL,
  prenom character varying(255) NOT NULL,
  sexe character varying(2) DEFAULT NULL::character varying,
  datederncnx timestamp without time zone,
  adresse text,
  code_postal character varying(255),
  ville character varying(255),
  telephone character varying(255),
	datederpub timestamp without time zone;
);

/* l_gestionnaire_opp */
DROP TABLE if exists l_gestionnaire_opp;
CREATE TABLE l_gestionnaire_opp (
	l_go_id serial PRIMARY KEY, 
	l_go_users_id integer,
	l_go_opp_id integer
);


/* l_fournisseur_opp */
DROP TABLE if exists l_fournisseur_opp;
CREATE TABLE l_fournisseur_opp (
	l_fo_id serial PRIMARY KEY, 
	l_fo_users_id integer,
	l_fo_opp_id integer
);


DROP TABLE if exists faq;
CREATE TABLE faq (
    faq_id serial PRIMARY KEY,
	faq_titre character varying(255),
	faq_question text,
	faq_reponse text,
	faq_date timestamp without time zone,
	faq_header text,
	faq_doc_name text,
	faq_doc_url text,
	faq_num_ordre integer
);  


DROP TABLE if exists ressource;
CREATE TABLE public.ressource
(
  ressource_id serial PRIMARY KEY,
  ressource_titre character varying(255),
  ressource_question text,
  ressource_desc text,
  ressource_logo_id integer,
  ressource_num_ordre integer
);


DROP TABLE if exists thesaurus_tree_facultatif;
CREATE TABLE thesaurus_tree_facultatif (
	thesaurus_tree_facultatif_id serial PRIMARY KEY, 
	thesaurus_tree_facultatif_nom character varying(255), 
	thesaurus_tree_facultatif_parent_id integer
);



DROP TABLE if exists l_photo_thesaurus_facultatif;
CREATE TABLE l_photo_thesaurus_facultatif (
	l_ptf_id serial PRIMARY KEY, 
	l_ptf_thesaurus_id integer,
	l_ptf_photo_id integer
);


DROP TABLE if exists l_thesaurus_facultatif_evolution;
CREATE TABLE l_thesaurus_facultatif_evolution (
	l_tfe_id serial PRIMARY KEY, 
	l_tfe_photo_thesaurus_id integer,
	l_tfe_evolution_id integer
);

  
DROP TABLE if exists l_ressource_file_manager;
CREATE TABLE l_ressource_file_manager (
    l_refm_id serial PRIMARY KEY, 
    l_refm_ressource_id integer,
    l_refm_file_manager_id integer,
	nom_fichier character varying(255)
);


DROP TABLE if exists document_annexe;
CREATE TABLE document_annexe (
	document_annexe_id serial PRIMARY KEY, 
	document_annexe_titre character varying(255),
	document_annexe_desc text,
	document_annexe_file_id integer,
	document_annexe_opp_id integer,
	document_annexe_all_opp BOOLEAN DEFAULT false
);

DROP TABLE if exists commentaire;
CREATE TABLE commentaire (
	commentaire_id serial PRIMARY KEY, 
	commentaire_text text,
	commentaire_etat integer,
	commentaire_date timestamp without time zone,
	commentaire_auteur_id integer,
	commentaire_photo_id integer
);

DROP TABLE if exists l_document_annexe_opp;
CREATE TABLE l_document_annexe_opp (
	l_dao_id serial PRIMARY KEY, 
	l_dao_opp_id integer,
	l_dao_document_annexe_id integer
);

DROP TABLE if exists public.apropos;
CREATE TABLE public.apropos
(
  apropos_id serial PRIMARY KEY,
  apropos_titre text,
  apropos_description text,
  apropos_doc_url text,
  apropos_doc_label text,
  apropos_num_ordre integer
);

DROP TABLE if exists public.l_faq_file_manager;
CREATE TABLE public.l_faq_file_manager
(
  l_fafm_id serial PRIMARY KEY,
  l_fafm_faq_id integer,
  l_fafm_file_manager_id integer,
  l_fafm_nom_fichier character varying
);

DROP TABLE if exists public.parametre;
CREATE TABLE public.parametre
(
  id serial PRIMARY KEY,
  prm_code character varying(255) NOT NULL,
  prm_valeur character varying(255) NOT NULL,
  prm_desc character varying(1024) DEFAULT NULL::character varying
);

DROP TABLE if exists actualites;
CREATE TABLE actualites (
	actualite_id serial PRIMARY KEY, 
	actualite_editor text,
	actualite_ordre int
);

DROP TABLE if exists carrousel_photo;
CREATE TABLE carrousel_photo (
	carrousel_photo_id serial PRIMARY KEY, 
	carrousel_actualite_id integer, 
	carrousel_photo_file_id integer,
  carrousel_photo_titre character varying,
	carrousel_is_creating boolean, 
	carrousel_photo_ordre integer
);
---------------------------------------


ALTER TABLE document
  ADD CONSTRAINT fk_document_file_id FOREIGN KEY (document_file_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_document_file_id
  ON document(document_file_id);

ALTER TABLE document_ref
  ADD CONSTRAINT fk_document_ref_langue_id FOREIGN KEY (document_ref_langue_id) REFERENCES langue (langue_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_document_ref_langue_id
  ON document_ref(document_ref_langue_id);

ALTER TABLE document_ref
  ADD CONSTRAINT fk_document_ref_licence_id FOREIGN KEY (document_ref_licence_id) REFERENCES licence (licence_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_document_ref_licence_id
  ON document_ref(document_ref_licence_id);

ALTER TABLE document_ref
  ADD CONSTRAINT fk_document_ref_file_id FOREIGN KEY (document_ref_file_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_document_ref_file_id
  ON document_ref(document_ref_file_id);

ALTER TABLE l_photo_thesaurus
  ADD CONSTRAINT fk_l_pt_thesaurus_id FOREIGN KEY (l_pt_thesaurus_id) REFERENCES thesaurus_tree (thesaurus_tree_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_pt_thesaurus_id
  ON l_photo_thesaurus(l_pt_thesaurus_id);

ALTER TABLE l_photo_thesaurus
  ADD CONSTRAINT fk_l_pt_photo_id FOREIGN KEY (l_pt_photo_id) REFERENCES photo (photo_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_pt_photo_id
  ON l_photo_thesaurus(l_pt_photo_id);

ALTER TABLE l_serie_axe_thematic
  ADD CONSTRAINT fk_l_sat_serie_id FOREIGN KEY (l_sat_serie_id) REFERENCES serie (serie_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_sat_serie_id
  ON l_serie_axe_thematic(l_sat_serie_id);

ALTER TABLE l_serie_axe_thematic
  ADD CONSTRAINT fk_l_sat_axe_thematic_id FOREIGN KEY (l_sat_axe_thematic_id) REFERENCES axe_thematic (axe_thematic_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_sat_axe_thematic_id
  ON l_serie_axe_thematic(l_sat_axe_thematic_id);
  

ALTER TABLE l_serie_unite_paysagere_locale
  ADD CONSTRAINT fk_l_supl_serie_id FOREIGN KEY (l_supl_serie_id) REFERENCES serie (serie_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_supl_serie_id
  ON l_serie_unite_paysagere_locale(l_supl_serie_id);

ALTER TABLE l_serie_unite_paysagere_locale
  ADD CONSTRAINT fk_l_supl_unite_paysage_locale_id FOREIGN KEY (l_supl_unite_paysage_locale_id) REFERENCES unite_paysage_locale (unite_paysage_locale_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_supl_unite_paysage_locale_id
  ON l_serie_unite_paysagere_locale(l_supl_unite_paysage_locale_id);

ALTER TABLE l_thesaurus_evolution
  ADD CONSTRAINT fk_l_te_photo_thesaurus_id FOREIGN KEY (l_te_photo_thesaurus_id) REFERENCES l_photo_thesaurus (l_pt_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_te_photo_thesaurus_id
  ON l_thesaurus_evolution(l_te_photo_thesaurus_id);

ALTER TABLE l_thesaurus_evolution
  ADD CONSTRAINT fk_l_te_evolution_id FOREIGN KEY (l_te_evolution_id) REFERENCES evolution_paysage (evolution_paysage_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_te_evolution_id
  ON l_thesaurus_evolution(l_te_evolution_id);

ALTER TABLE lien_externe
  ADD CONSTRAINT fk_lien_externe_serie_id FOREIGN KEY (lien_externe_serie_id) REFERENCES serie (serie_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_lien_externe_serie_id
  ON lien_externe(lien_externe_serie_id);


ALTER TABLE photo
  ADD CONSTRAINT fk_photo_format_id FOREIGN KEY (photo_format_id) REFERENCES format (format_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_photo_format_id
  ON photo(photo_format_id);

ALTER TABLE photo
  ADD CONSTRAINT fk_photo_licence_id FOREIGN KEY (photo_licence_id) REFERENCES licence (licence_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_photo_licence_id
  ON photo(photo_licence_id);


ALTER TABLE photo
  ADD CONSTRAINT fk_photo_file_id FOREIGN KEY (photo_file_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_photo_file_id
  ON photo(photo_file_id);
  
ALTER TABLE photo
  ADD CONSTRAINT fk_photo_serie_id FOREIGN KEY (photo_serie_id) REFERENCES serie (serie_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_file_id
  ON photo(photo_serie_id);

ALTER TABLE porteur_opp
  ADD CONSTRAINT fk_porteur_opp_logo_id FOREIGN KEY (porteur_opp_logo_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_porteur_opp_logo_id
  ON porteur_opp(porteur_opp_logo_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_opp_id FOREIGN KEY (serie_opp_id) REFERENCES opp (opp_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_opp_id
  ON serie(serie_opp_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_porteur_opp_id FOREIGN KEY (serie_porteur_opp_id) REFERENCES porteur_opp (porteur_opp_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_porteur_opp_id
  ON serie(serie_porteur_opp_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_langue_id FOREIGN KEY (serie_langue_id) REFERENCES langue (langue_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_langue_id
  ON serie(serie_langue_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_format_id FOREIGN KEY (serie_format_id) REFERENCES format (format_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_format_id
  ON serie(serie_format_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_pays_id FOREIGN KEY (serie_pays_id) REFERENCES pays (pays_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_pays_id
  ON serie(serie_pays_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_region_id FOREIGN KEY (serie_region_id) REFERENCES region (region_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_region_id
  ON serie(serie_region_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_departement_id FOREIGN KEY (serie_departement_id) REFERENCES departement (departement_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_departement_id
  ON serie(serie_departement_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_commune_id FOREIGN KEY (serie_commune_id) REFERENCES commune (commune_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_commune_id
  ON serie(serie_commune_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_ensemble_paysage_id FOREIGN KEY (serie_ensemble_paysage_id) REFERENCES ensemble_paysager (ensemble_paysager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_ensemble_paysage_id
  ON serie(serie_ensemble_paysage_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_unite_paysagere_id FOREIGN KEY (serie_unite_paysagere_id) REFERENCES unite_paysage (unite_paysage_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_unite_paysagere_id
  ON serie(serie_unite_paysagere_id);

ALTER TABLE serie
  ADD CONSTRAINT fk_serie_croquis_id FOREIGN KEY (serie_croquis_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_croquis_id
  ON serie(serie_croquis_id);
  
ALTER TABLE serie
  ADD CONSTRAINT fk_serie_photo_trepied_id FOREIGN KEY (serie_photo_trepied_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_photo_trepied_id
  ON serie(serie_photo_trepied_id);
  
ALTER TABLE serie
  ADD CONSTRAINT fk_serie_photo_ign_id FOREIGN KEY (serie_photo_ign_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_photo_ign_id
  ON serie(serie_photo_ign_id);
  
ALTER TABLE serie
  ADD CONSTRAINT fk_serie_photo_aerienne_id FOREIGN KEY (serie_photo_aerienne_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_photo_aerienne_id
  ON serie(serie_photo_aerienne_id);
  
ALTER TABLE serie
  ADD CONSTRAINT fk_serie_photo_context_id FOREIGN KEY (serie_photo_context_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_photo_context_id
  ON serie(serie_photo_context_id);
  
ALTER TABLE serie
  ADD CONSTRAINT fk_serie_refdoc_id FOREIGN KEY (serie_refdoc_id) REFERENCES document_ref (document_ref_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_serie_refdoc_id
  ON serie(serie_refdoc_id);
  
ALTER TABLE son
  ADD CONSTRAINT fk_son_struct_resp_id FOREIGN KEY (son_struct_resp_id) REFERENCES porteur_opp (porteur_opp_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_son_struct_resp_id
  ON son(son_struct_resp_id);
  
ALTER TABLE son
  ADD CONSTRAINT fk_son_file_id FOREIGN KEY (son_file_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_son_file_id
  ON son(son_file_id);
  
ALTER TABLE son
  ADD CONSTRAINT fk_son_langue_id FOREIGN KEY (son_langue_id) REFERENCES langue (langue_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_son_langue_id
  ON son(son_langue_id);
  
ALTER TABLE son
  ADD CONSTRAINT fk_son_licence_id FOREIGN KEY (son_licence_id) REFERENCES licence (licence_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_son_licence_id
  ON son(son_licence_id);
  
ALTER TABLE serie
ADD CONSTRAINT fk_serie_typologie_id FOREIGN KEY (serie_typologie_id) REFERENCES typologie_paysage (typologie_paysage_id)
ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fk_serie_typologie_id
ON serie(serie_typologie_id);


ALTER TABLE photo
  ADD CONSTRAINT fk_photo_licence_fiche_id FOREIGN KEY (photo_licence_fiche_id) REFERENCES licence (licence_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_photo_licence_fiche_id
  ON photo(photo_licence_fiche_id);
  

ALTER TABLE opp
  ADD CONSTRAINT fk_opp_porteur_opp_id FOREIGN KEY (opp_porteur_opp_id) REFERENCES porteur_opp (porteur_opp_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_opp_porteur_opp_id
  ON opp(opp_porteur_opp_id);


ALTER TABLE l_gestionnaire_opp
  ADD CONSTRAINT fk_l_go_users_id FOREIGN KEY (l_go_users_id) REFERENCES users (id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_go_users_id
  ON l_gestionnaire_opp(l_go_users_id);

ALTER TABLE l_gestionnaire_opp
  ADD CONSTRAINT fk_l_go_opp_id FOREIGN KEY (l_go_opp_id) REFERENCES opp (opp_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_go_opp_id
  ON l_gestionnaire_opp(l_go_opp_id);
  
ALTER TABLE l_fournisseur_opp
  ADD CONSTRAINT fk_l_fo_users_id FOREIGN KEY (l_fo_users_id) REFERENCES users (id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_fo_users_id
  ON l_fournisseur_opp(l_fo_users_id);

ALTER TABLE l_fournisseur_opp
  ADD CONSTRAINT fk_l_fo_opp_id FOREIGN KEY (l_fo_opp_id) REFERENCES opp (opp_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_fo_opp_id
  ON l_fournisseur_opp(l_fo_opp_id);
  
  

ALTER TABLE document
  ADD CONSTRAINT fk_document_serie_id_id FOREIGN KEY (document_serie_id) REFERENCES serie (serie_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_document_serie_id
  ON document(document_serie_id);
  
ALTER TABLE son
  ADD CONSTRAINT fk_son_serie_id_id FOREIGN KEY (son_serie_id) REFERENCES serie (serie_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_son_serie_id
  ON son(son_serie_id);
  
ALTER TABLE ONLY public.faq
    ADD CONSTRAINT fk_faq_file_id FOREIGN KEY (faq_id) REFERENCES public.file_manager(file_manager_id);
	
  
ALTER TABLE ONLY public.ressource
    ADD CONSTRAINT fk_ressource_logo_id FOREIGN KEY (ressource_logo_id) REFERENCES public.file_manager(file_manager_id);
	

ALTER TABLE l_photo_thesaurus_facultatif
  ADD CONSTRAINT fk_l_ptf_thesaurus_id FOREIGN KEY (l_ptf_thesaurus_id) REFERENCES thesaurus_tree_facultatif (thesaurus_tree_facultatif_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_ptf_thesaurus_id
  ON l_photo_thesaurus_facultatif(l_ptf_thesaurus_id);

ALTER TABLE l_photo_thesaurus_facultatif
  ADD CONSTRAINT fk_l_ptf_photo_id FOREIGN KEY (l_ptf_photo_id) REFERENCES photo (photo_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_ptf_photo_id
  ON l_photo_thesaurus_facultatif(l_ptf_photo_id);
 

ALTER TABLE l_thesaurus_facultatif_evolution
  ADD CONSTRAINT fk_l_tfe_photo_thesaurus_id FOREIGN KEY (l_tfe_photo_thesaurus_id) REFERENCES l_photo_thesaurus_facultatif (l_ptf_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_tfe_photo_thesaurus_id
  ON l_thesaurus_facultatif_evolution(l_tfe_photo_thesaurus_id);

ALTER TABLE l_thesaurus_facultatif_evolution
  ADD CONSTRAINT fk_l_tfe_evolution_id FOREIGN KEY (l_tfe_evolution_id) REFERENCES evolution_paysage (evolution_paysage_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_tfe_evolution_id
  ON l_thesaurus_facultatif_evolution(l_tfe_evolution_id);
  
  
CREATE INDEX fki_l_refm_file_manager_id ON l_ressource_file_manager USING btree (l_refm_file_manager_id);

CREATE INDEX fki_l_refm_ressource_id ON l_ressource_file_manager USING btree (l_refm_ressource_id);

ALTER TABLE ONLY l_ressource_file_manager
    ADD CONSTRAINT fk_l_refm_file_manager_id FOREIGN KEY (l_refm_file_manager_id) REFERENCES file_manager(file_manager_id);

ALTER TABLE ONLY l_ressource_file_manager
    ADD CONSTRAINT fk_l_refm_ressource_id FOREIGN KEY (l_refm_ressource_id) REFERENCES ressource(ressource_id);

ALTER TABLE document_annexe
  ADD CONSTRAINT fk_document_annexe_file_id FOREIGN KEY (document_annexe_file_id) REFERENCES file_manager(file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_document_annexe_file_id
  ON document_annexe(document_annexe_file_id);

ALTER TABLE document_annexe
  ADD CONSTRAINT fk_document_annexe_opp_id FOREIGN KEY (document_annexe_opp_id) REFERENCES opp (opp_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_document_annexe_opp_id
  ON document_annexe(document_annexe_opp_id);


ALTER TABLE commentaire
  ADD CONSTRAINT fk_commentaire_auteur_id FOREIGN KEY (commentaire_auteur_id) REFERENCES users(id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_commentaire_auteur_id
  ON commentaire(commentaire_auteur_id);

ALTER TABLE commentaire
  ADD CONSTRAINT fk_commentaire_photo_id FOREIGN KEY (commentaire_photo_id) REFERENCES photo (photo_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_commentaire_photo_id
  ON commentaire(commentaire_photo_id);
 

ALTER TABLE l_document_annexe_opp
  ADD CONSTRAINT fk_l_dao_opp_id FOREIGN KEY (l_dao_opp_id) REFERENCES opp (opp_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_dao_opp_id
  ON l_document_annexe_opp(l_dao_opp_id);

ALTER TABLE l_document_annexe_opp
  ADD CONSTRAINT fk_l_dao_document_annexe_id FOREIGN KEY (l_dao_document_annexe_id) REFERENCES document_annexe (document_annexe_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_l_dao_document_annexe_id
  ON l_document_annexe_opp(l_dao_document_annexe_id);

ALTER TABLE l_faq_file_manager
  ADD CONSTRAINT fk_l_fafm_file_manager_id FOREIGN KEY (l_fafm_file_manager_id) REFERENCES file_manager (file_manager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;


CREATE INDEX fki_l_fafm_faq_id ON l_faq_file_manager USING btree (l_fafm_faq_id);

ALTER TABLE ONLY l_faq_file_manager
    ADD CONSTRAINT fk_l_fafm_faq_id FOREIGN KEY (l_fafm_faq_id) REFERENCES faq(faq_id);

ALTER TABLE unite_paysage
  ADD CONSTRAINT fk_unite_paysage_ensemble_id FOREIGN KEY (unite_paysage_ensemble_id) REFERENCES ensemble_paysager (ensemble_paysager_id)
   ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE INDEX fki_unite_paysage_ensemble_id
  ON unite_paysage(unite_paysage_ensemble_id);


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


ALTER TABLE carrousel_photo
    ADD CONSTRAINT fk_carrousel_file_id FOREIGN KEY (carrousel_photo_file_id) REFERENCES file_manager (file_manager_id)
ON UPDATE NO ACTION ON DELETE NO ACTION;

ALTER TABLE carrousel_photo
    ADD CONSTRAINT fk_carrousel_actualite_id FOREIGN KEY (carrousel_actualite_id) REFERENCES actualites (actualite_id)
ON UPDATE NO ACTION ON DELETE NO ACTION;
-------------------------------------------------

INSERT INTO users(email, roles, password, nom, prenom, datederncnx)
VALUES ('admin@admin.fr', '	["ROLE_ADMIN"]', '$2y$13$Qw./ZzGOeWhUntV31S94puqXcZporI5BXOYcwunXYF05N4qEqOR6C', 'admin','admin', CURRENT_DATE);
--admin@admin.fr / metmet

INSERT INTO evolution_paysage(evolution_paysage_nom, evolution_paysage_nom_lien) VALUES ( 'Stabilité', 'stability');
INSERT INTO evolution_paysage(evolution_paysage_nom, evolution_paysage_nom_lien) VALUES ( 'Apparition', 'appeared');
INSERT INTO evolution_paysage(evolution_paysage_nom, evolution_paysage_nom_lien) VALUES ( 'Disparition', 'disappeared');
INSERT INTO evolution_paysage(evolution_paysage_nom, evolution_paysage_nom_lien) VALUES ( 'Augmentation', 'increase');
INSERT INTO evolution_paysage(evolution_paysage_nom, evolution_paysage_nom_lien) VALUES ( 'Diminution', 'decrease');
INSERT INTO evolution_paysage(evolution_paysage_nom, evolution_paysage_nom_lien) VALUES ( 'Changement d''apparence', 'appearance_change');


CREATE OR REPLACE FUNCTION public.get_next_identifiant_interne_serie(IN in_id_opp integer)
    RETURNS character varying
    LANGUAGE 'plpgsql'
    VOLATILE
    PARALLEL UNSAFE
    COST 100
    
AS $BODY$
DECLARE 
id_int VARCHAR = '001';
BEGIN
	SELECT INTO id_int LPAD((TO_NUMBER(serie_identifiant_int, '999') + 1)::VARCHAR, 3, '0') 
	FROM serie 
	where serie_opp_id = in_id_opp 
	AND LENGTH(serie_identifiant_int) = 3
	ORDER BY TO_NUMBER(serie_identifiant_int, '999') DESC
	limit 1;
--	RETURN id_int;
	RETURN coalesce(id_int, '001');
END;
$BODY$;

CREATE OR REPLACE FUNCTION public.get_next_identifiant_interne_photo(IN in_id_opp integer)
    RETURNS character varying
    LANGUAGE 'plpgsql'
    VOLATILE
    PARALLEL UNSAFE
    COST 100
    
AS $BODY$
DECLARE 
id_int VARCHAR = '001';
BEGIN
	SELECT INTO id_int LPAD((count(1) +1)::VARCHAR, 3, '0') 
	FROM photo 
	where photo_serie_id = in_id_opp ;
	RETURN  coalesce(id_int, '001');
END;
$BODY$;

