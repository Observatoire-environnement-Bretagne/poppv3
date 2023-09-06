INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) values ('Urbanisme', 0);
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) values ('Agriculture', 0);
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) values ('Infrastructure et réseau', 0);
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) values ('Milieu naturel', 0);
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) values ('Ressources', 0);
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) values ('Tourisme', 0);
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) values ('Patrimoine', 0);
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) values ('Evènement', 0);

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Étalement urbain', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Urbanisme';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Centres bourgs et espaces publics', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Urbanisme';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Zones d’activité', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Urbanisme';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Signalétique', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Urbanisme';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Equipement public', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Urbanisme';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Culture', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Agriculture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Elevage', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Agriculture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Bâti', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Agriculture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Friche agricole', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Agriculture';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Réseau', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Infrastructure et réseau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Déchet', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Infrastructure et réseau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Routière', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Infrastructure et réseau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Transport', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Infrastructure et réseau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Ouvrage hydraulique', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Infrastructure et réseau';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Végétal', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Milieu naturel';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Eau', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Milieu naturel';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Sol', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Milieu naturel';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Production d’énergie', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Ressources';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Sous sol', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Ressources';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Montagne', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Tourisme';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Littoral et fluvial', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Tourisme';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Nature', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Tourisme';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Accueil', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Tourisme';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Religieux', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Patrimoine';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Arboré', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Patrimoine';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Rural', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Patrimoine';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Hydraulique', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Patrimoine';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Historique', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Patrimoine';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Industriel', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Patrimoine';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Naturel', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Evènement';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Climatique', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Evènement';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Industriel (évènement)', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Evènement';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Humain', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Evènement';


INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Urbanisation linéaire', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Étalement urbain';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Coupure urbaine', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Étalement urbain';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Lotissement', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Étalement urbain';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Maison isolée', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Étalement urbain';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Espace piéton', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Arbres en ville', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Nature en ville', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Dent creuse', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Densification', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Stationnement', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Façade restaurée', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Petit commerce', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Ouvertures sur les cours d’eau', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Mobilier urbain', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Jardin', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Centres bourgs et espaces publics';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Friche d’activité', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Zones d’activité';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Réhabilitation', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Zones d’activité';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Bâtiment industriel', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Zones d’activité';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Bâtiment commerciaux', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Zones d’activité';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Enseigne', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Signalétique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Prés-enseigne', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Signalétique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Publicité', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Signalétique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Signalisation d’Information Locale', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Signalétique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Dispositif d’information', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Signalétique';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Sportif', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Equipement public';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Educatif', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Equipement public';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Culturel', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Equipement public';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Médical', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Equipement public';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Grande culture', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Vignoble', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Peupleraie', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Oliveraie', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Châtaigneraie', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Polyculture', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Bocage', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Agro-foresterie', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Maraîchère', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Verger', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Résineux', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Culture';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Pelouse sèche', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Elevage';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Pâturages', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Elevage';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Landes', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Elevage';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Mytiliculture', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Elevage';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Ostréiculture', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Elevage';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Corps de ferme', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Bâti';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Hangar', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Bâti';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Muret', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Bâti';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Silo', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Bâti';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Bâti d’élevage', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Bâti';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Épineux', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Friche agricole';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Près bois', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Friche agricole';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Taillis', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Friche agricole';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Pylône', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Réseau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Antenne', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Réseau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Transformateur électrique', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Réseau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Réseau filaire', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Réseau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Eclairage public', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Réseau';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Station d’épuration', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Déchet';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Lagune d’épuration', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Déchet';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Décharge sauvage', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Déchet';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Déchetterie', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Déchet';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Aménagement routier', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Routière';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Piste cyclable', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Routière';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Chemin piéton', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Routière';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Tram', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Transport';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Remonté mécanique', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Transport';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Réseau ferré', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Transport';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Digue', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Ouvrage hydraulique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Jetée', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Ouvrage hydraulique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Enrochement', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Ouvrage hydraulique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Barrage', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Ouvrage hydraulique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Retenue collinaire', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Ouvrage hydraulique';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Forêt', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Végétal';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Bosquet', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Végétal';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Arbre isolé', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Végétal';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Haie rurale', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Végétal';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Prairie', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Végétal';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Plante invasive', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Végétal';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Ripisylve', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Eau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Rivière', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Eau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Neige', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Eau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Bras mort', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Eau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Zone humide', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Eau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Glacier', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Eau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Lac', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Eau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Cascade', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Eau';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Source', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Eau';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Imperméabilisation', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Sol';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Érosion', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Sol';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Dune', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Sol';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Atterrissement', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Sol';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Falaise', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Sol';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Terrasse', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Sol';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Éolienne', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Production d’énergie';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Photovoltaïque', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Production d’énergie';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Hydraulique', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Production d’énergie';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Méthanisation', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Production d’énergie';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Carrière', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Sous sol';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Remblais', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Sous sol';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Déblais', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Sous sol';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Gravière', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Sous sol';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Unité Touristique Nouvelle', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Montagne';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Station de ski', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Montagne';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Plage', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Littoral et fluvial';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Location saisonnière', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Littoral et fluvial';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Port', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Littoral et fluvial';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Camping', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Nature';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Aire de camping car', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Nature';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Base de loisir', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Nature';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Panorama', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Nature';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Musées', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Accueil';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Installation saisonnière', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Accueil';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Lieu d’information', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Accueil';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Lieu de culte', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Religieux';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Cimetière', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Religieux';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Croix', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Religieux';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Mail planté', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Arboré';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Arbre remarquable', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Arboré';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Grange pastorale', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Rural';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Moulin', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Rural';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Abreuvoir', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Rural';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Mur de pierre sèche', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Rural';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Ecluse', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Hydraulique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Lavoir', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Hydraulique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Fontaine', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Hydraulique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Pont', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Hydraulique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Ponton', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Hydraulique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Cale', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Hydraulique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Canal', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Hydraulique';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Château', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Historique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Dolmen', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Historique';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Filature', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Industriel';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Site minier', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Industriel';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Bâtiment industriel du XIX au début XXème', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Industriel';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Inondation', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Naturel';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Avalanche', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Naturel';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Séisme', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Naturel';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Tempête', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Climatique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Sécheresse', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Climatique';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Incendie', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Climatique';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Pollution', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Industriel (évènement)';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Explosion', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Industriel (évènement)';

INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Fête', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Humain';
INSERT INTO thesaurus_tree(thesaurus_tree_nom, thesaurus_tree_parent_id) 
SELECT 'Manifestation culturelle', thesaurus_tree_id FROM thesaurus_tree WHERE thesaurus_tree_nom = 'Humain';
