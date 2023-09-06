<?php

namespace App\Controller;

use ZipArchive;
use Dompdf\Dompdf;
use App\Entity\Opp;
use App\Entity\Son;
use App\Repository;
use Dompdf\Options;
use App\Entity\Pays;
use App\Entity\Photo;
use App\Entity\Serie;
use App\Entity\Users;
use App\Entity\Format;
use App\Entity\Langue;
use App\Entity\Region;
use App\Entity\Commune;
use App\Entity\Licence;
use App\Entity\Document;
use App\Entity\PorteurOpp;
use App\Entity\AxeThematic;
use App\Entity\Commentaire;
use App\Entity\Departement;
use App\Entity\DocumentRef;

use App\Entity\FileManager;
use App\Entity\LienExterne;
use App\Entity\UnitePaysage;
use App\Entity\ThesaurusTree;
use App\Entity\LFournisseurOpp;
use App\Entity\LPhotoThesaurus;
use App\Entity\EnsemblePaysager;
use App\Entity\EvolutionPaysage;
use App\Entity\LGestionnaireOpp;
use App\Entity\TypologiePaysage;
use App\Entity\LSerieAxeThematic;
use App\Entity\UnitePaysageLocale;
use App\Entity\LThesaurusEvolution;
use App\Entity\ThesaurusTreeFacultatif;

use Doctrine\ORM\Query\ResultSetMapping;

use App\Entity\LPhotoThesaurusFacultatif;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Entity\LSerieUnitePaysagereLocale;
use App\Entity\LThesaurusFacultatifEvolution;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

// Include Dompdf required namespaces
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SerieController extends Controller
{
    const FOLDER_FICHE_SERIE = '/serie_pdf/';

    const FOLDER_EXPORT_SERIE = '/export_serie/';    
    const FOLDER_UPLOAD_FILE = 'upload';
    const FOLDER_DOC_SERIE = 'document_serie';
    const FOLDER_PHOTO = 'photos';
    const FOLDER_MINIATURE = 'miniature';
    const FOLDER_REF_DOC = 'document_ref';
    const FOLDER_SON = 'sons';
    const FOLDER_DOCUMENT = 'documents';

    
    /**
     * @Route("gestion/get/series", name="getSeries")
     * @return Response
     */
    public function getSeries()
    {
        $em = $this->getDoctrine()->getManager();
        if($this->isGranted('ROLE_ADMIN')){
            $series = $em->getRepository(Serie::class)->findAll();
        }else if ($this->isGranted('ROLE_FOURNISSEUR')){
            $series = [];
            $user = $this->getUser();
            $OppsByFournisseur = $em->getRepository(LFournisseurOpp::class)->findBy(array('lFoUsers' => $user));
            foreach($OppsByFournisseur as $OppByFournisseur){
                $opp = $OppByFournisseur->getLFoOpp();
                $oppSeries = $em->getRepository(Serie::class)->findBy(array('serieOpp' => $opp));
                foreach($oppSeries as $serie){
                    $series[$serie->getSerieId()] = $serie;
                }
            }
            $OppsByGestionnaire = $em->getRepository(LGestionnaireOpp::class)->findBy(array('lGoUsers' => $user));
            foreach($OppsByGestionnaire as $OppByGestionnaire){
                $opp = $OppByGestionnaire->getLGoOpp();
                $oppSeries = $em->getRepository(Serie::class)->findBy(array('serieOpp' => $opp));
                foreach($oppSeries as $serie){
                    $series[$serie->getSerieId()] = $serie;
                }
            }
        }

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        return $this->render("serie/series.html.twig", [
            'series' => $series,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]); 
    }
       
    /**
     * @Route("/public/popp", name="public_page")
     * @return Response
     */
    public function viewSeries()
    {
        //Chargement des variables globales
        // Chargement des services
        $ParametreDAO = $this->get('parametre.dao');
        //A déplacer au lancement de l'application
        $ParametreDAO->setGlobalParamaters();
        
        $em = $this->getDoctrine()->getManager();
        $Series = $em->getRepository(Serie::class)->findBy(array(), array('serieIdentifiantSerie' => 'ASC'));
        $Photos = $em->getRepository(Photo::class)->findBy(array(), array('photoDatePrise' => 'ASC'));
        $structuresOpp = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));

        //On ne récupère que les communes qui contiennent des séries
        $tabCommunes = [];
        $tabUnitePaysage = [];
        $tabRegions = [];
        $tabDepartements = [];
        $tabTypologiePaysages = [];
        $tabEnsemblePaysagers = [];
        $tabOPPs = [];
        $tabPays = [];
        $tabStucturesOpp = [];
        $tabThemeOrder = [];
        $tabYearPhoto = [];

        foreach($Photos as $photo){
            $datePhoto = $photo->getphotoDatePrise();
            $yearPhoto = $datePhoto->format('Y');
            $tabYearPhoto[$yearPhoto] = $yearPhoto;
        }
        
        foreach($Series as $serie){
            if($serie->getSerieCommune()){
                $tabCommunes[$serie->getSerieCommune()->getCommuneInsee()] =  $serie->getSerieCommune();
            }

            //On ne récupère que les unités paysagères qui contiennent des séries
            /*if($serie->getSerieUnitePaysagere()){
                $tabUnitePaysage[$serie->getSerieUnitePaysagere()->getUnitePaysageId()] = $serie->getSerieUnitePaysagere();
            }*/
            //On ne récupère que les unités paysagères qui contiennent des séries
            if($serie->getSerieUnitePaysagere()){
                $tabUnitePaysage[$serie->getSerieUnitePaysagere()->getUnitePaysageId()] = $serie->getSerieUnitePaysagere()->getUnitePaysageNom();
            }

            //On ne récupère que les Typologies qui contiennent des séries
            if($serie->getSerieTypologie()){
                $tabTypologiePaysages[$serie->getSerieTypologie()->getTypologiePaysageId()] = $serie->getSerieTypologie()->getTypologiePaysageNom();
            }

            //On ne récupère que les départements qui contiennent des séries
            if($serie->getSerieDepartement()){
                $tabDepartements[$serie->getSerieDepartement()->getDepartementId()] = array("departementNom" => $serie->getSerieDepartement()->getDepartementNom(), "departementCode" => $serie->getSerieDepartement()->getDepartementCode());
            }

            //On ne récupère que les ensembles paysagés qui contiennent des séries
            if($serie->getSerieEnsemblePaysage()){
                $tabEnsemblePaysagers[$serie->getSerieEnsemblePaysage()->getEnsemblePaysagerId()] = $serie->getSerieEnsemblePaysage()->getEnsemblePaysagerNom();
            }

            //On ne récupère que les OPP qui contiennent des séries
            if($serie->getSerieOpp()){
                $tabOPPs[$serie->getSerieOpp()->getOppId()] = $serie->getSerieOpp()->getOppNom();
            }

            //On ne récupère que les Pays qui contiennent des séries
            if($serie->getSeriePays()){
                $tabPays[$serie->getSeriePays()->getPaysId()] = $serie->getSeriePays()->getPaysNom();
            }

            //On ne récupère que les Structures qui contiennent des séries
            if($serie->getSeriePorteurOpp()){
                $tabStucturesOpp[$serie->getSeriePorteurOpp()->getPorteurOppId()] = $serie->getSeriePorteurOpp()->getPorteurOppNom();
            }

            //On ne récupère que les régions qui contiennent des séries
            if($serie->getSerieRegion()){
                $tabRegions[$serie->getSerieRegion()->getRegionId()] = $serie->getSerieRegion()->getRegionNom();
            }
        }
        
        $lAxeThematics = $em->getRepository(LSerieAxeThematic::class)->findAll();
        $tabAxeThematics = [];
        foreach($lAxeThematics as $lSerieAxeThematic){
            //$themeSansAccent = strtr($lSerieAxeThematic->getLSatAxeThematic()->getAxeThematicNom(), "ÀÁÂàÄÅàáâàäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
            $str = htmlentities($lSerieAxeThematic->getLSatAxeThematic()->getAxeThematicNom(), ENT_NOQUOTES, 'utf-8');
            $themeSansAccent = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
            $tabThemeOrder[$themeSansAccent] = $lSerieAxeThematic->getLSatAxeThematic()->getAxeThematicId();
            $tabAxeThematics[$lSerieAxeThematic->getLSatAxeThematic()->getAxeThematicId()] = $lSerieAxeThematic->getLSatAxeThematic()->getAxeThematicNom();
        }

        ksort($tabThemeOrder);
        //print_r($tabThemeOrder);
        $tabAxeThematicsOrder = [];
        foreach($tabThemeOrder as $axeThematicId){
            $tabAxeThematicsOrder[$axeThematicId] = $tabAxeThematics[$axeThematicId];
        }
        //$this->get('session')->remove('critere_search_series');
        //initialisation des series
        $searchSeries = $this->getSearchSeries();
        //$critereSearchSeries = $this->get('session')->get('critere_search_series');
        //$critereSearchSeries['series'] = $searchSeries;
        //$this->get('session')->set('critere_search_series', $critereSearchSeries);
        ksort($tabCommunes);
        asort($tabOPPs);
        asort($tabDepartements);
        asort($tabEnsemblePaysagers);
        asort($tabPays);
        asort($tabRegions);
        asort($tabUnitePaysage);
        asort($tabTypologiePaysages);
        asort($tabStucturesOpp);
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        return $this->render("serie/public.html.twig",[
            'AxeThematics' => $tabAxeThematicsOrder,
            'Communes' => $tabCommunes,
            'Departements' => $tabDepartements,
            'EnsemblePaysagers' => $tabEnsemblePaysagers,
            'Opps' => $tabOPPs,
            'Pays' => $tabPays,
            'Regions' => $tabRegions,
            'UnitePaysages' => $tabUnitePaysage,
            'TypologiePaysages' => $tabTypologiePaysages,
            'Series' => $Series,
            'tabYearPhoto' => $tabYearPhoto,
            'structuresOpp' => $tabStucturesOpp,
            'structures' => $structuresOpp,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
     
    
    /**
     * @Route("gestion/create/serie", name="createSerie")
     * @return Response
     */
    public function createSerie(TranslatorInterface $translator)
    {
        //Récupération du FileManagerDAO
        $fileManagerDAO = $this->get('filemanager.dao');
        //paramètres golobaux
        $parameters = $this->get('session')->get('parameters');

        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('langueNom' => 'ASC'));
        $axeThematics = $em->getRepository(AxeThematic::class)->findBy(array(), array('axeThematicNom' => 'ASC'));
        $communes = $em->getRepository(Commune::class)->findBy(array(), array('communeNom' => 'ASC'));
        $departements = $em->getRepository(Departement::class)->findBy(array(), array('departementNom' => 'ASC'));
        $ensemblesPaysagers = $em->getRepository(EnsemblePaysager::class)->findBy(array(), array('ensemblePaysagerNom' => 'ASC'));
        
        $opps = $em->getRepository(Opp::class)->findBy(array(), array('oppNom' => 'ASC'));
        if ($this->isGranted('ROLE_FOURNISSEUR') && !$this->isGranted('ROLE_ADMIN')){
            $opps = [];
            $user = $this->getUser();
            $OppsByFournisseur = $em->getRepository(LFournisseurOpp::class)->findBy(array('lFoUsers' => $user));
            foreach($OppsByFournisseur as $OppByFournisseur){
                $opps[] = $OppByFournisseur->getLFoOpp();
            }
            $OppsByGestionnaire = $em->getRepository(LGestionnaireOpp::class)->findBy(array('lGoUsers' => $user));
            foreach($OppsByGestionnaire as $OppByGestionnaire){
                $opps[] = $OppByGestionnaire->getLGoOpp();
            }
        }

        $pays = $em->getRepository(Pays::class)->findBy(array(), array('paysNom' => 'ASC'));
        $regions = $em->getRepository(Region::class)->findBy(array(), array('regionNom' => 'ASC'));
        $unitesPaysage = $em->getRepository(UnitePaysage::class)->findBy(array(), array('unitePaysageNom' => 'ASC'));
        $typologiesPaysage = $em->getRepository(TypologiePaysage::class)->findBy(array(), array('typologiePaysageNom' => 'ASC'));
        $structuresOpp = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'ASC'));
        
        $unitesPaysageLocale = $em->getRepository(UnitePaysageLocale::class)->findBy(array(), array('unitePaysageLocaleNom' => 'ASC'));
        $formats = $em->getRepository(Format::class)->findBy(array(), array('formatNom' => 'ASC'));
        $licences = $em->getRepository(Licence::class)->findBy(array(), array('licenceNom' => 'ASC'));
        
        $serie = new Serie();
        $photos = [];
        $lstUnitePaysageLocal = [];
        $sons = [];
        $documents = [];
        $liensExt = [];

        $defaultValue = [
            'langue' => '',
            'evolution_delete' => '',
            'pays' => '',
            'region' => '',
            'frequence' => ''
        ];
        $regionDefault = $em->getRepository(Region::class)->findOneBy(array('regionNom' => $translator->trans('default_value.region')));
        if ($regionDefault){
            $defaultValue['region'] = $regionDefault->getRegionId();
        }
        $paysFr = $em->getRepository(Pays::class)->findOneBy(array('paysNom' => $translator->trans('default_value.pays')));
        if ($paysFr){
            $defaultValue['pays'] = $paysFr->getPaysId();
        }
        $langueFr = $em->getRepository(Langue::class)->findOneBy(array('langueNom' => $translator->trans('default_value.langue')));
        if ($langueFr){
            $defaultValue['langue'] = $langueFr->getLangueId();
        }
        $defaultValue['frequence'] = $translator->trans('default_value.frequence');
        $evolutionDelete = $em->getRepository(EvolutionPaysage::class)->findOneBy(array('evolutionPaysageNom' => $translator->trans('default_value.disparition')));
        if ($evolutionDelete){
            $defaultValue['evolution_delete'] = $evolutionDelete->getEvolutionPaysageId();
        }
        
        $tabAxeThematics = [];
        foreach($axeThematics as $axeThematic){
            //$themeSansAccent = strtr($lSerieAxeThematic->getLSatAxeThematic()->getAxeThematicNom(), "ÀÁÂàÄÅàáâàäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
            $str = htmlentities($axeThematic->getAxeThematicNom(), ENT_NOQUOTES, 'utf-8');
            $themeSansAccent = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
            $tabThemeOrder[$themeSansAccent] = $axeThematic->getAxeThematicId();
            $tabAxeThematics[$axeThematic->getAxeThematicId()] = $axeThematic->getAxeThematicNom();
        }

        ksort($tabThemeOrder);
        $tabAxeThematicsOrder = [];
        foreach($tabThemeOrder as $axeThematicId){
            $tabAxeThematicsOrder[$axeThematicId] = $tabAxeThematics[$axeThematicId];
        }
        
        //Tri par ordre alphabetique avec les accents
        $tabCommunes = [];
        foreach($communes as $commune){
            $str = htmlentities($commune->getCommuneNom(), ENT_NOQUOTES, 'utf-8');
            $communeSansAccent = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
            $tabCommuneOrder[$communeSansAccent] = $commune->getCommuneId();
            $tabCommunes[$commune->getCommuneId()] = $commune->getCommuneInsee() . ' ' . $commune->getCommuneNom();
        }

        ksort($tabCommuneOrder);
        $tabCommunesOrder = [];
        foreach($tabCommuneOrder as $communeId){
            $tabCommunesOrder[$communeId] = $tabCommunes[$communeId];
        }

        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("serie/create_serie.html.twig", [
            'langues' => $langues,
            'departements' => $departements,
            'regions' => $regions,
            'pays' => $pays,
            'opps' => $opps,
            'typologiesPaysage' => $typologiesPaysage,
            'axeThematics' => $tabAxeThematicsOrder,
            'structuresOpp' => $structuresOpp,
            'ensemblesPaysagers' => $ensemblesPaysagers,
            'unitesPaysageLocale' => $unitesPaysageLocale,
            'formats' => $formats,
            'licences' => $licences,
            'unitesPaysage' => $unitesPaysage,
            'communes' => $tabCommunesOrder,
            'action' => 'new',
            'serie' => $serie,
            'photos' => $photos,
            'lstAxeThematic' => '',
            'lstUnitePaysageLocal' => $lstUnitePaysageLocal,
            'sons' => $sons,
            'documents' => $documents,
            'structures' => $structuresOpp,
            'liensExt' => $liensExt,
            'defaultValue' => $defaultValue,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }

    /**
     * @Route("gestion/serie/insertDb", name="insertSerieDb")
     * @return Response
     */
    
    public function insertSerieDb(Request $request)
    { 
        $em = $this->getDoctrine()->getManager(); //on appelle Doctrine
        //Récupération du FileManagerDAO
        $fileManagerDAO = $this->get('filemanager.dao');
        //paramètres golobaux
        $parameters = $this->get('session')->get('parameters');
        //Date du jour
        //$datetime =  date("U"); //\DateTime::createFromFormat("U", date("Y-m-d H:i:s"));
        $today = new \DateTime();

        $serieId = $request->request->get('serie_id');
        $user = $this->getUser();
        $user->setDatederpub($today);
        //Insertion dans la base
        if ($serieId == 'new'){
            $serie = new Serie();
            if( $request->request->get('serie_id_serie') != "" ){
                $identifiant = $request->request->get('serie_id_serie');
            }else{
                $identifiant = date('mdy His');
            }
            $serie->setSerieIdentifiantSerie($identifiant);
            $serie->setSerieUserCrea($user);
            $serie->setSerieDateCrea($today);
        }else{
            $serie = $em->getRepository(Serie::class)->find($serieId);

            //ajout des données d'audit
            if(!$serie->getSerieUserCrea() && $request->request->get('serie_user_crea') != ""){
                $userCrea = $em->getRepository(Users::class)->find($request->request->get('serie_user_crea'));
                $serie->setSerieUserCrea($userCrea);
                if($request->request->get('serie_date_crea') != ""){
                    $serieDateCrea = \DateTime::createFromFormat("Y-m-d", $request->request->get('serie_date_crea'));
                    $serie->setSerieDateCrea($serieDateCrea);
                }
            }
            
            $serie->setSerieIdentifiantSerie($request->request->get('serie_id_serie'));
            //On supprime avant de créer
            $repoLSerieAxeThematic = $em->getRepository(LSerieAxeThematic::class);
            
            $lSatSeries = $repoLSerieAxeThematic->findBy(array('lSatSerie' => $serie));
            foreach($lSatSeries as $lSatSerie){
                $em->remove($lSatSerie);
            }
            $repoLSerieUnitePaysagereLocale = $em->getRepository(LSerieUnitePaysagereLocale::class);
            
            $lSuplSeries = $repoLSerieUnitePaysagereLocale->findBy(array('lSuplSerie' => $serie));
            foreach($lSuplSeries as $lSuplSerie){
                $em->remove($lSuplSerie);
            }
            $em->flush();
            
            $serie->setSerieUserMaj($user);
            $serie->setSerieDateMaj($today);
        }

        if(!isSet($serie)){
            return new JsonResponse(['status' => 'error', 'message' => 'La série à modifier n\'existe pas']);
        }
        
        $serie->setSerieTitre($request->request->get('serie_objet_titre'));
        $serie->setSerieIntention($request->request->get('serie_desc_intention'));
        $serie->setSerieDesc($request->request->get('serie_desc_fine_edit'));
        
        //$serieDate = \DateTime::createFromFormat("j/m/Y", $request->request->get('serie_datepicker'));
        $serieDate = \DateTime::createFromFormat("Y-m-d", $request->request->get('serie_datepicker'));
        $serie->setSerieDate($serieDate);
        //???
        $request->request->get('serie_id_serie');
        
        $serie->setSerieIdentifiantInt($request->request->get('serie_id_inter_opp'));
        $serie->setSerieAdresse($request->request->get('serie_coverage_adrs'));
        $serie->setSerieFreqInterval($request->request->get('serie_coverage_frequ_reconduct'));
        $serie->setSerieFreqPeriod($request->request->get('serie_coverage_freq_reconduct_unit'));
        $serie->setSerieGeomX($request->request->get('serie_emplacement_longitude'));
        $serie->setSerieGeomY($request->request->get('serie_emplacement_latitude'));
        $serie->setSerieObsRephoto($request->request->get('serie_fiche_obs'));

        $repoCommune = $em->getRepository(Commune::class);
        $commune = $repoCommune->find($request->request->get('serie_coverage_com'));
        $serie->setSerieCommune($commune);

        $repoDepartepement = $em->getRepository(Departement::class);
        $departement = $repoDepartepement->find($request->request->get('serie_coverage_dep'));
        $serie->setSerieDepartement($departement);

        $repoEnsPaysager = $em->getRepository(EnsemblePaysager::class);
        if($request->request->get('serie_coverage_ens_paysage') != ""){
            $ensPaysager = $repoEnsPaysager->find($request->request->get('serie_coverage_ens_paysage'));
            $serie->setSerieEnsemblePaysage($ensPaysager);
        }

        $repoFormat = $em->getRepository(Format::class);
        $format = $repoFormat->find($request->request->get('serie_format'));
        $serie->setSerieFormat($format);

        $repoLangue = $em->getRepository(Langue::class);
        $langue = $repoLangue->find($request->request->get('serie_langue'));
        $serie->setSerieLangue($langue);

        $repoTypologiePaysage = $em->getRepository(TypologiePaysage::class);
        $typologiePaysage = $repoTypologiePaysage->find($request->request->get('serie_objet_typologie'));
        $serie->setSerieTypologie($typologiePaysage);

        $repoOpp = $em->getRepository(Opp::class);
        $opp = $repoOpp->find($request->request->get('serie_objet_opp'));
        $serie->setSerieOpp($opp);

        $repoPays = $em->getRepository(Pays::class);
        $pays = $repoPays->find($request->request->get('serie_coverage_pays'));
        $serie->setSeriePays($pays);

        $repoPorteurOpp = $em->getRepository(PorteurOpp::class);
        $structureOpp = $repoPorteurOpp->find($request->request->get('serie_structure_opp'));
        $serie->setSeriePorteurOpp($structureOpp);
            
        $repoRegion = $em->getRepository(Region::class);
        $regionInst = $repoRegion->find($request->request->get('serie_coverage_region'));
        $serie->setSerieRegion($regionInst);
            
        if($request->request->get('seriePublie') == 'false'){
            $serie->setSeriePublie(false);
        }else{
            $serie->setSeriePublie(true);
        }

        $unitePaysage = $request->request->get('serie_coverage_unite_paysage');
        if($unitePaysage != ""){
            $repoUnitePaysage = $em->getRepository(UnitePaysage::class);
            $unitPaysage = $repoUnitePaysage->find($unitePaysage);
            $serie->setSerieUnitePaysagere($unitPaysage);
        }

        //gestion des documents
        $tabDoc = [];
        $tabDoc['croquis'] = $request->request->get('Croquis');
        $tabDoc['aerienne'] = $request->request->get('Aerienne');
        $tabDoc['context'] = $request->request->get('Context');
        $tabDoc['IGN'] = $request->request->get('IGN');
        $tabDoc['trepied'] = $request->request->get('Trepied');
        foreach($tabDoc as $typeDoc => $infoDoc){
            $newDoc = null;
            if($infoDoc['fileId'] == 'new'){
                //création du fichier
                $nomFile = $infoDoc['fileTitre'];

                $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_DOC_SERIE . "/";
                $pathUpload = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_UPLOAD_FILE . "/";
                if (!file_exists($pathFile)) {
                    mkdir($pathFile, 0777, true);
                }
                //On renomme si le fichier existe
                $fname = $nomFile;
                $rawBaseName = pathinfo($pathUpload . $nomFile, PATHINFO_FILENAME );
                $extension_upload = pathinfo($pathUpload . $nomFile, PATHINFO_EXTENSION  );
                $counter = 0;
                
                while(file_exists($pathFile . $nomFile)) {
                    $nomFile = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                    $counter++;
                };

                // Valider le fichier et le stocker définitivement
                if(!rename($pathUpload . $fname, $pathFile . $nomFile)){
                    return new JsonResponse(array(
                        'status' => "erreur",
                        'message' => "Erreur lors de la copie du fichier"
                    ));
                }
                $newDoc = new FileManager();
                
                $newDoc->setFileManagerNom($nomFile);
                $newDoc->setFileManagerUri("/" . self::FOLDER_DOC_SERIE . "/" . $nomFile);
                //$newLogo->setFileManagerMime($request->request->get('structureLogoTitre'));
                //$newLogo->setFileManagerStatut($request->request->get('structureLogoTitre'));
                $newDoc->setFileManagerSize($infoDoc['filePoids']);
                $newDoc->setFileManagerDate(date("U"));

                $em->persist($newDoc);
                $em->flush();
            }
            
            //On supprime le fichier existant si il y en a un
            if(($infoDoc['fileId'] == 'delete' || $infoDoc['fileId'] == 'new')){
                switch ($typeDoc){
                    case 'croquis' :
                        $doc = $serie->getSerieCroquis();   
                        $serie->setSerieCroquis($newDoc);
                    break;
                    case 'aerienne' :
                        $doc = $serie->getSeriePhotoAerienne();
                        $serie->setSeriePhotoAerienne($newDoc);
                    break;
                    case 'context' :
                        $doc = $serie->getSeriePhotoContext();
                        $serie->setSeriePhotoContext($newDoc);
                    break;
                    case 'IGN' :
                        $doc = $serie->getSeriePhotoIgn();
                        $serie->setSeriePhotoIgn($newDoc);
                    break;
                    case 'trepied' :
                        $doc = $serie->getSeriePhotoTrepied();
                        $serie->setSeriePhotoTrepied($newDoc);
                    break;
                    if(isSet($doc)){
                        $fileManagerDAO->removeFile($doc);
                    }
                }
            }
        } 
        $em->persist($serie);
        //gestion des photos
        
        $repoThesaurusTree = $em->getRepository(ThesaurusTree::class);
        $repoThesaurusTreeFacultatif = $em->getRepository(ThesaurusTreeFacultatif::class);
        $repoEvolutionPaysage = $em->getRepository(EvolutionPaysage::class);
        $repoPhoto = $em->getRepository(Photo::class);
        $repoLPhotoThesaurus = $em->getRepository(LPhotoThesaurus::class);
        $repoLThesaurusEvolution = $em->getRepository(LThesaurusEvolution::class);
        $repoLPhotoThesaurusFacultatif = $em->getRepository(LPhotoThesaurusFacultatif::class);
        $repoLThesaurusFacultatifEvolution = $em->getRepository(LThesaurusFacultatifEvolution::class);
        $repoLicence = $em->getRepository(Licence::class);
        foreach($request->request->get('photos') as $photoInfo){
            switch ($photoInfo['photo_action']) {
                case 'delete':
                    //Si on est en création puis suppression, on touche à rien
                    if(!str_contains($photoInfo['photo_id'], 'new')){
                        $photo = $repoPhoto->find($photoInfo['photo_id']);
                        if($photo){
                            $photoFile = $photo->getPhotoFile();
                            //Thésaurus
                            $LPhotoThesaurus = $repoLPhotoThesaurus->findBy(array('lPtPhoto' => $photo));
                            foreach($LPhotoThesaurus as $photoThesaurus){
                                $LThesaurusEvolutions = $repoLThesaurusEvolution->findBy(array('lTePhotoThesaurus' => $photoThesaurus));
                                foreach($LThesaurusEvolutions as $thesaurusEvolution){
                                    $em->remove($thesaurusEvolution);
                                }
                                $em->remove($photoThesaurus);
                            }
                            
                            //Thésaurus Facultatif
                            $LPhotoThesaurusFacultatif = $repoLPhotoThesaurusFacultatif->findBy(array('lPtfPhoto' => $photo));
                            foreach($LPhotoThesaurusFacultatif as $photoThesaurusFacultatif){
                                $LThesaurusFacultatifEvolutions = $repoLThesaurusFacultatifEvolution->findBy(array('lTfePhotoThesaurus' => $photoThesaurusFacultatif));
                                foreach($LThesaurusFacultatifEvolutions as $thesaurusFacultatifEvolution){
                                    $em->remove($thesaurusFacultatifEvolution);
                                }
                                $em->remove($photoThesaurusFacultatif);
                            }
                            
                            $em->remove($photo);
                            $em->flush();
                            $fileManagerDAO->removeFile($photoFile);
                        }
                    }
                break;
                case 'add':
                case 'updated':
                    $photo = null;
                    if ($photoInfo['photo_action'] == 'updated'){
                        $photo = $repoPhoto->find($photoInfo['photo_id']);
                    }
                    //dans le cas d'une création (ou mise à jour de création)
                    //On crée une photo
                    if(!isSet($photo)){
                        $photo = new Photo();
                    }
                    $photo->setPhotoTitre($photoInfo['photo_titre']);
                    $photo->setPhotoAuteur($photoInfo['photo_auteur']);
                    $photo->setPhotoDescChangement($photoInfo['photo_desc']);
                    if ($photoInfo['photo_date_desc'] != ""){
                        //$photoDateDesc = \DateTime::createFromFormat("j/m/Y", $photoInfo['photo_date_desc']);
                        $photoDateDesc = \DateTime::createFromFormat("Y-m-d", $photoInfo['photo_date_desc']);
                        $photo->setPhotoDateDesc($photoDateDesc);
                    }
                    $photoDatePrise = \DateTime::createFromFormat("Y-m-d", $photoInfo['photo_date_prise']);
                    $photo->setPhotoDatePrise($photoDatePrise);
                    if($photoInfo['photo_iden'] != ""){
                        $photo->setPhotoIdentifiantInt($photoInfo['photo_iden']);
                    }
                    if($photoInfo['photo_heure'] != ""){
                        $photo->setPhotoHeure($photoInfo['photo_heure']);
                    }
                    $photo->setPhotoTypeAppareil($photoInfo['photo_type_appareil']);
                    if($photoInfo['photo_focale'] != ""){
                        $photo->setPhotoFocale($photoInfo['photo_focale']);
                    }
                    $photo->setPhotoOuvertureDia($photoInfo['photo_ouverture']);
                    $photo->setPhotoTypeFilm($photoInfo['photo_type_film']);
                    $photo->setPhotoIso($photoInfo['photo_iso']);
                    $photo->setPhotoPoidsOrigine($photoInfo['photo_poids_ori']);
                    if($photoInfo['photo_hauteur'] != ""){
                        $photo->setPhotoInclinaison($photoInfo['photo_hauteur']);
                    }
                    if($photoInfo['photo_hauteur'] != ""){
                        $photo->setPhotoHauteur($photoInfo['photo_hauteur']);
                    }
                    if($photoInfo['photo_orientation'] != ""){
                        $photo->setPhotoOrientation($photoInfo['photo_orientation']);
                    }
                    if($photoInfo['photo_altitude'] != ""){
                        $photo->setPhotoAltitude($photoInfo['photo_altitude']);
                    }
                    if($photoInfo['photo_coef_mer'] != ""){
                        $photo->setPhotoCoefMaree($photoInfo['photo_coef_mer']);
                    }

                    $format = $repoFormat->find($photoInfo['photo_format']);
                    $photo->setPhotoFormat($format);
                    
                    $licencePhoto = $repoLicence->find($photoInfo['photo_licence']);
                    $photo->setPhotoLicence($licencePhoto);
                    
                    if($photoInfo['photo_fiche_licence'] != ""){
                        $licenceFichePhoto = $repoLicence->find($photoInfo['photo_fiche_licence']);
                        $photo->setPhotoLicenceFiche($licenceFichePhoto);
                    }

                    //Gestion des fichiers photo
                    switch($photoInfo['photo_file_action']){
                        case 'new' :
                            //création du fichier
                            $nomFile = $photoInfo['photo_file_name'];

                            $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_PHOTO . "/";
                            $pathUpload = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_UPLOAD_FILE . "/";
                            if (!file_exists($pathFile)) {
                                mkdir($pathFile, 0777, true);
                            }
                            //On renomme si le fichier existe
                            $fname = $nomFile;
                            $rawBaseName = pathinfo($pathUpload . $nomFile, PATHINFO_FILENAME );
                            $extension_upload = pathinfo($pathUpload . $nomFile, PATHINFO_EXTENSION  );
                            $counter = 0;
                            
                            while(file_exists($pathFile . $nomFile)) {
                                $nomFile = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                                $counter++;
                            };
                            // Valider le fichier et le stocker définitivement
                            if(!rename($pathUpload . $fname, $pathFile . $nomFile)){
                                return new JsonResponse(array(
                                    'status' => "erreur",
                                    'message' => "Erreur lors de la copie du fichier"
                                ));
                            }
                            $newPhoto = new FileManager();
                            
                            $newPhoto->setFileManagerNom($nomFile);
                            $newPhoto->setFileManagerUri("/" . self::FOLDER_PHOTO . "/" . $nomFile);
                            //$newLogo->setFileManagerMime($request->request->get('structureLogoTitre'));
                            //$newLogo->setFileManagerStatut($request->request->get('structureLogoTitre'));
                            $newPhoto->setFileManagerSize($photoInfo['photo_file_size']);
                            $newPhoto->setFileManagerDate(date("U"));

                            $em->persist($newPhoto);
                            $em->flush();

                            $oldPhoto = $photo->getPhotoFile();
                            
                            $photo->setPhotoFile($newPhoto);

                            //si une photo existe, on la supprime 
                            if($oldPhoto){
                                $fileManagerDAO->removeFile($oldPhoto);
                            }

                            //TODO GERER LES MINIATURES
                            list($width, $height, $type, $attr) = getimagesize($parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_PHOTO . '/' . $nomFile);
                            $heightMiniature = 85;
                            $coefMiniature = 85/$height;
                            $widthMiniature = $coefMiniature*$width;
                            
                            if (!file_exists($parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_MINIATURE . '/' . self::FOLDER_PHOTO . '/')) {
                                mkdir($parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_MINIATURE . '/' . self::FOLDER_PHOTO . '/', 0777, true);
                            }
                            $this->resize($widthMiniature, $parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_MINIATURE . '/' . self::FOLDER_PHOTO . '/'  . $nomFile, $pathFile . $nomFile);
                        break;
                    }
                    $photo->setPhotoSerie($serie);
                    $em->persist($photo);
                    
                    //Gestion du thésaurus
                    //delete avant d'insérer
                    $LPhotoThesaurus = $repoLPhotoThesaurus->findBy(array('lPtPhoto' => $photo));
                    foreach($LPhotoThesaurus as $photoThesaurus){
                        $LThesaurusEvolutions = $repoLThesaurusEvolution->findBy(array('lTePhotoThesaurus' => $photoThesaurus));
                        foreach($LThesaurusEvolutions as $thesaurusEvolution){
                            $em->remove($thesaurusEvolution);
                        }
                        $em->remove($photoThesaurus);
                    }
                    
                    $listPhotoThesaurus = $photoInfo['photo_thesaurus'];
                    $tabPhotoThesaurus = explode(',', $listPhotoThesaurus);
                    if($listPhotoThesaurus != ''){
                        foreach($tabPhotoThesaurus as $codeEvolutionThesaurus){
                            list($thesaurusId, $evolutionId) = explode('_', $codeEvolutionThesaurus);
                            $thesaurus = $repoThesaurusTree->find($thesaurusId);
                            $evolution = $repoEvolutionPaysage->find($evolutionId);

                            $photoThesaurusNew = new LPhotoThesaurus();
                            $photoThesaurusNew->setLPtPhoto($photo);
                            $photoThesaurusNew->setLPtThesaurus($thesaurus);
                            $em->persist($photoThesaurusNew);
                            
                            $ThesaurusEvolutionNew = new LThesaurusEvolution();
                            $ThesaurusEvolutionNew->setLTeEvolution($evolution);
                            $ThesaurusEvolutionNew->setLTePhotoThesaurus($photoThesaurusNew);
                            $em->persist($ThesaurusEvolutionNew);
                        }
                    }
                    
                    //Gestion du thésaurus facultatif
                    //delete avant d'insérer
                    $LPhotoThesaurusFacultatif = $repoLPhotoThesaurusFacultatif->findBy(array('lPtfPhoto' => $photo));
                    foreach($LPhotoThesaurusFacultatif as $photoThesaurusFacultatif){
                        $LThesaurusFacultatifEvolutions = $repoLThesaurusFacultatifEvolution->findBy(array('lTfePhotoThesaurus' => $photoThesaurusFacultatif));
                        foreach($LThesaurusFacultatifEvolutions as $thesaurusFacultatifEvolution){
                            $em->remove($thesaurusFacultatifEvolution);
                        }
                        $em->remove($photoThesaurusFacultatif);
                    }
                    
                    $listPhotoThesaurusFacultatif = $photoInfo['photo_thesaurus_facult'];
                    $tabPhotoThesaurusFacultatif = explode(',', $listPhotoThesaurusFacultatif);
                    if($listPhotoThesaurusFacultatif != ''){
                        foreach($tabPhotoThesaurusFacultatif as $codeEvolutionThesaurusFacultatif){
                            list($thesaurusFacultatifId, $evolutionId) = explode('_', $codeEvolutionThesaurusFacultatif);
                            $thesaurusFacultatif = $repoThesaurusTreeFacultatif->find($thesaurusFacultatifId);
                            $evolution = $repoEvolutionPaysage->find($evolutionId);
    
                            $photoThesaurusFacultatifNew = new LPhotoThesaurusFacultatif();
                            $photoThesaurusFacultatifNew->setLPtfPhoto($photo);
                            $photoThesaurusFacultatifNew->setLPtfThesaurus($thesaurusFacultatif);
                            $em->persist($photoThesaurusFacultatifNew);
                            
                            $ThesaurusFacultatifEvolutionNew = new LThesaurusFacultatifEvolution();
                            $ThesaurusFacultatifEvolutionNew->setLTfeEvolution($evolution);
                            $ThesaurusFacultatifEvolutionNew->setLTfePhotoThesaurus($photoThesaurusFacultatifNew);
                            $em->persist($ThesaurusFacultatifEvolutionNew);
                        }
                    }

                break;
            }
        }

        //Gestion des documents de référence
        $docRefAction = $request->request->get('serie_document_ref_action');
        if($docRefAction == 'new' || $docRefAction == 'update' || $docRefAction == 'delete'){
            //on supprime le document de référence s'il est existant
            $refDocFile = false;
            $docRef = $serie->getSerieRefdoc();
            if($docRef){
                $serie->setSerieRefdoc(null);
                $refDocFile = $docRef->getDocumentRefFile();
                $em->remove($docRef);
                if($refDocFile && $request->request->get('serie_document_ref_file_action') != 'loaded'){
                    $fileManagerDAO->removeFile($refDocFile);
                }
            }
            if($docRefAction == 'new' || $docRefAction == 'update'){
                $docRefNew = new DocumentRef();
                $docRefNew->setDocumentRefIdentifiantInt($request->request->get('serie_document_ref_identifiant_int'));
                $docRefNew->setDocumentRefAuteur($request->request->get('serie_document_ref_auteur'));
                $docRefNew->setDocumentRefDesc($request->request->get('serie_document_ref_desc'));
                if($request->request->get('serie_document_ref_date') != ""){
                    $docRefDate = \DateTime::createFromFormat("Y-m-d", $request->request->get('serie_document_ref_date'));
                    //$docRefDate = \DateTime::createFromFormat("j/m/Y", $request->request->get('serie_document_ref_date'));
                    $docRefNew->setDocumentRefDate($docRefDate);
                }
                $docRefNew->setDocumentRefCommentaireDate($request->request->get('serie_document_ref_commentaire_date'));
                $docRefNew->setDocumentRefType($request->request->get('serie_document_ref_type'));
                $docRefNew->setDocumentRefFormat($request->request->get('serie_document_ref_format'));
                $docRefNew->setDocumentRefSource($request->request->get('serie_document_ref_source'));
                $docRefNew->setDocumentRefSite($request->request->get('serie_document_ref_site'));
                //$docRefNew->setDocumentRefNom($request->request->get('serie_document_ref_nom'));
                $docRefNew->setDocumentRefSousTitre($request->request->get('serie_document_ref_sous_titre'));
                if($request->request->get('serie_document_ref_heure') != ""){
                    $docRefNew->setDocumentRefHeure($request->request->get('serie_document_ref_heure'));
                }
                $docRefNew->setDocumentRefPeriode($request->request->get('serie_document_ref_periode'));
                $docRefNew->setDocumentRefMoment($request->request->get('serie_document_ref_moment'));
                $docRefNew->setDocumentRefLieuConservation($request->request->get('serie_document_ref_lieu_conservation'));
                if($request->request->get('serie_document_ref_orientation') != ""){
                    $docRefNew->setDocumentRefOrientation($request->request->get('serie_document_ref_orientation'));
                }
                if($request->request->get('serie_document_ref_altitude') != ""){
                    $docRefNew->setDocumentRefAltitude($request->request->get('serie_document_ref_altitude'));
                }
                if($request->request->get('serie_document_ref_coef_maree') != ""){
                    $docRefNew->setDocumentRefCoefMaree($request->request->get('serie_document_ref_coef_maree'));
                }
                $docRefNew->setDocumentRefCoteDoc($request->request->get('serie_document_ref_cote_doc'));
                if($request->request->get('serie_document_ref_langue_id') != ""){
                    $docRefLangue = $repoLangue->find($request->request->get('serie_document_ref_langue_id'));
                    $docRefNew->setDocumentRefLangue($docRefLangue);
                }
                if($request->request->get('serie_document_ref_licence_id') != ""){
                    $docRefLicence = $repoLicence->find($request->request->get('serie_document_ref_licence_id'));
                    $docRefNew->setDocumentRefLicence($docRefLicence);
                }
                if($request->request->get('serie_document_ref_file_action') != ""){
                    //Si le fichier n'a pas été modifié, on le réinjecte
                    if($refDocFile && $request->request->get('serie_document_ref_file_action') == 'loaded'){
                        $docRefNew->setDocumentRefFile($refDocFile);
                    }else{
                        $nomFile = $request->request->get('serie_document_ref_file_titre');

                        $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_REF_DOC . "/";
                        $pathUpload = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_UPLOAD_FILE . "/";
                        if (!file_exists($pathFile)) {
                            mkdir($pathFile, 0777, true);
                        }
                        //On renomme si le fichier existe
                        $fname = $nomFile;
                        $rawBaseName = pathinfo($pathUpload . $nomFile, PATHINFO_FILENAME );
                        $extension_upload = pathinfo($pathUpload . $nomFile, PATHINFO_EXTENSION  );
                        $counter = 0;
                        
                        while(file_exists($pathFile . $nomFile)) {
                            $nomFile = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                            $counter++;
                        };
                        // Valider le fichier et le stocker définitivement
                        if(!rename($pathUpload . $fname, $pathFile . $nomFile)){
                            return new JsonResponse(array(
                                'status' => "erreur",
                                'message' => "Erreur lors de la copie du fichier"
                            ));
                        }
                        //on créer le nouveau document
                        $docRefFileNew = new FileManager();
                        
                        $docRefFileNew->setFileManagerNom($nomFile);
                        $docRefFileNew->setFileManagerUri("/" . self::FOLDER_REF_DOC . "/" . $nomFile);
                        $docRefFileNew->setFileManagerSize($request->request->get('serie_document_ref_file_poids'));
                        $docRefFileNew->setFileManagerDate(date("U"));

                        $em->persist($docRefFileNew);
                        $docRefNew->setDocumentRefFile($docRefFileNew);
                    }
                }
                $em->persist($docRefNew);
                $serie->setSerieRefdoc($docRefNew);
                
            }
        }
        
        //gestion des sons
        
        $repoSon = $em->getRepository(Son::class);
        $sons = $request->request->get('sons') ;
        if(isSet($sons)){
            foreach($request->request->get('sons') as $sonInfo){
                switch ($sonInfo['son_action']) {
                    case 'delete':
                        $son = $repoSon->find($sonInfo['son_id']);
                        if($son){
                            $sonFile = $son->getSonFile();

                            $em->remove($son);
                            $em->flush();
                            $fileManagerDAO->removeFile($sonFile);
                        }
                    break;
                    case 'add':
                    case 'updated':
                        $son = null;
                        if ($sonInfo['son_action'] == 'updated'){
                            $son = $repoSon->find($sonInfo['son_id']);
                        }
                        //dans le cas d'une création (ou mise à jour de création)
                        //On crée un son
                        if(!isSet($son)){
                            $son = new Son();
                        }
                        $son->setSonTitre($sonInfo['son_titre']);
                        $son->setSonAuteur($sonInfo['son_auteur']);
                        $son->setSonPresentation($sonInfo['son_presentation']);
                        $son->setSonLienPaysage($sonInfo['son_lien_paysage']);
                        if ($sonInfo['son_date'] != ""){
                            $sonDate = \DateTime::createFromFormat("Y-m-d", $sonInfo['son_date']);
                            //$sonDate = \DateTime::createFromFormat("j/m/Y", $sonInfo['son_date']);
                            $son->setSonDate($sonDate);
                        }
                        $son->setSonType($sonInfo['son_type']);
                        $son->setSonFormat($sonInfo['son_format']);
                        if($sonInfo['son_heure'] != ""){
                            $son->setSonHeure($sonInfo['son_heure']);
                        }
                        $son->setSonHeure($sonInfo['son_heure']);
                        $son->setSonTypeMat($sonInfo['son_type_mat']);
                        $son->setSonTraitement($sonInfo['son_traitement']);
                        $son->setSonProtocole($sonInfo['son_protocole']);
                        $son->setSonContexte($sonInfo['son_contexte']);
                        $son->setSonConditionMeteo($sonInfo['son_condition_meteo']);
                        $son->setSonNumPhoto($sonInfo['son_num_photo']);
                        $son->setSonLieu($sonInfo['son_lieu']);
                        $son->setSonDuree($sonInfo['son_duree']);

                        $langueSon = $repoLangue->find($sonInfo['son_langue_id']);
                        $son->setSonLangue($langueSon);
                        
                        $licenceSon = $repoLicence->find($sonInfo['son_licence_id']);
                        $son->setSonLicence($licenceSon);
                        
                        $structureOppSon = $repoPorteurOpp->find($sonInfo['son_struct_resp_id']);
                        $son->setSonStructResp($structureOppSon);

                        //Gestion des fichiers son
                        switch($sonInfo['son_file_action']){
                            case 'new' :
                                //création du fichier
                                $nomFile = $sonInfo['son_file_name'];

                                $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_SON . "/";
                                $pathUpload = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_UPLOAD_FILE . "/";
                                if (!file_exists($pathFile)) {
                                    mkdir($pathFile, 0777, true);
                                }

                                //On renomme si le fichier existe
                                $fname = $nomFile;
                                $rawBaseName = pathinfo($pathUpload . $nomFile, PATHINFO_FILENAME );
                                $extension_upload = pathinfo($pathUpload . $nomFile, PATHINFO_EXTENSION  );
                                $counter = 0;
                                
                                while(file_exists($pathFile . $nomFile)) {
                                    $nomFile = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                                    $counter++;
                                };

                                // Valider le fichier et le stocker définitivement
                                if(!rename($pathUpload . $fname, $pathFile . $nomFile)){
                                    return new JsonResponse(array(
                                        'status' => "erreur",
                                        'message' => "Erreur lors de la copie du son"
                                    ));
                                }
                                $newSon = new FileManager();
                                
                                $newSon->setFileManagerNom($nomFile);
                                $newSon->setFileManagerUri("/" . self::FOLDER_SON . "/" . $nomFile);
                                //$newLogo->setFileManagerMime($request->request->get('structureLogoTitre'));
                                //$newLogo->setFileManagerStatut($request->request->get('structureLogoTitre'));
                                $newSon->setFileManagerSize($sonInfo['son_file_size']);
                                $newSon->setFileManagerDate(date("U"));

                                $em->persist($newSon);
                                $em->flush();

                                $oldSon = $son->getSonFile();
                                
                                $son->setSonFile($newSon);

                                //si un son existe, on la supprime 
                                if($oldSon){
                                    $fileManagerDAO->removeFile($oldSon);
                                }

                            break;
                        }
                        $son->setSonSerie($serie);
                        $em->persist($son);
                        
                    break;
                }
            } 
        }

        //gestion des documents
        
        $repoDocument = $em->getRepository(Document::class);
        $documents = $request->request->get('documents') ;
        if(isSet($documents)){
            foreach($documents as $documentInfo){
                switch ($documentInfo['document_action']) {
                    case 'delete':
                        $document = $repoDocument->find($documentInfo['document_id']);
                        if($document){
                            $documentFile = $document->getDocumentFile();

                            $em->remove($document);
                            $em->flush();
                            $fileManagerDAO->removeFile($documentFile);
                        }
                    break;
                    case 'add':
                    case 'updated':
                        $document = null;
                        if ($documentInfo['document_action'] == 'updated'){
                            $document = $repoDocument->find($documentInfo['document_id']);
                        }
                        //dans le cas d'une création (ou mise à jour de création)
                        //On crée un document
                        if(!isSet($document)){
                            $document = new Document();
                        }
                        $document->setDocumentTitre($documentInfo['document_titre']);
                        $document->setDocumentLegende($documentInfo['document_legende']);

                        //Gestion des fichiers document
                        switch($documentInfo['document_file_action']){
                            case 'new' :
                                //création du fichier
                                $nomFile = $documentInfo['document_file_name'];

                                $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_DOCUMENT . "/";
                                $pathUpload = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_UPLOAD_FILE . "/";
                                if (!file_exists($pathFile)) {
                                    mkdir($pathFile, 0777, true);
                                }
                                
                                //On renomme si le fichier existe
                                $fname = $nomFile;
                                $rawBaseName = pathinfo($pathUpload . $nomFile, PATHINFO_FILENAME );
                                $extension_upload = pathinfo($pathUpload . $nomFile, PATHINFO_EXTENSION  );
                                $counter = 0;
                                
                                while(file_exists($pathFile . $nomFile)) {
                                    $nomFile = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                                    $counter++;
                                };
                                
                                // Valider le fichier et le stocker définitivement
                                if(!rename($pathUpload . $fname, $pathFile . $nomFile)){
                                    return new JsonResponse(array(
                                        'status' => "erreur",
                                        'message' => "Erreur lors de la copie du document"
                                    ));
                                }
                                $newDocument = new FileManager();
                                
                                $newDocument->setFileManagerNom($nomFile);
                                $newDocument->setFileManagerUri("/" . self::FOLDER_DOCUMENT . "/" . $nomFile);
                                //$newLogo->setFileManagerMime($request->request->get('structureLogoTitre'));
                                //$newLogo->setFileManagerStatut($request->request->get('structureLogoTitre'));
                                $newDocument->setFileManagerSize($documentInfo['document_file_size']);
                                $newDocument->setFileManagerDate(date("U"));

                                $em->persist($newDocument);
                                $em->flush();

                                $oldDocument = $document->getDocumentFile();
                                
                                $document->setDocumentFile($newDocument);

                                //si un document existe, on la supprime 
                                if($oldDocument){
                                    $fileManagerDAO->removeFile($oldDocument);
                                }

                            break;
                        }
                        $document->setDocumentSerie($serie);
                        $em->persist($document);
                        
                    break;
                }
            } 
        }

        $em->persist($serie);
        $em->flush();
            
        $repoAxeThematic = $em->getRepository(AxeThematic::class);

        $tabThematics = $request->request->get('serie_objet_thematique');
        if (isSet($tabThematics)){
            foreach($tabThematics as $thematicId){
                $thematic = $repoAxeThematic->find($thematicId);
    
                $lSerieAxeThematicNew = new LSerieAxeThematic();
                $lSerieAxeThematicNew->setLSatAxeThematic($thematic);
                $lSerieAxeThematicNew->setLSatSerie($serie);
                
                $em->persist($lSerieAxeThematicNew);
            }
        }
            
        $repoUnitePaysageLocale = $em->getRepository(UnitePaysageLocale::class);
        $tabUnitPaysageLocales = $request->request->get('serie_coverage_unit_paysage_local');
        if(isSet($tabUnitPaysageLocales)){
            foreach($tabUnitPaysageLocales as $unitPaysageLocaleId){
                $unitPaysageLocale = $repoUnitePaysageLocale->find($unitPaysageLocaleId);
    
                $LSerieUnitePaysagereLocaleNew = new LSerieUnitePaysagereLocale();
                $LSerieUnitePaysagereLocaleNew->setLSuplUnitePaysageLocale($unitPaysageLocale);
                $LSerieUnitePaysagereLocaleNew->setLSuplSerie($serie);
    
                $em->persist($LSerieUnitePaysagereLocaleNew);
            }
        }
            
        $repoLienExt = $em->getRepository(LienExterne::class);
        $liensExtSerie = $repoLienExt->findBy(array('lienExterneSerie' => $serie));
        foreach($liensExtSerie as $lienExtSerie){
            $em->remove($lienExtSerie);
        }

        $tabLiensExt = $request->request->get('liensExt');
        if(isSet($tabLiensExt)){
            foreach($tabLiensExt as $lienExt){
                $newLienExt = new LienExterne();
                $newLienExt->setLienExterneValue($lienExt['lienext_value']);
                $newLienExt->setLienExterneSerie($serie);
    
                $em->persist($newLienExt);
            }
        }

        $em->flush();
        return new JsonResponse(['status' => 'ok', 'serieId' => $serie->getSerieId()]);
    }

    function resize($newWidth, $targetFile, $originalFile) {

        $info = getimagesize($originalFile);
        $mime = $info['mime'];
    
        switch ($mime) {
                case 'image/jpeg':
                        $image_create_func = 'imagecreatefromjpeg';
                        $image_save_func = 'imagejpeg';
                        $new_image_ext = 'jpg';
                        break;
    
                case 'image/png':
                        $image_create_func = 'imagecreatefrompng';
                        $image_save_func = 'imagepng';
                        $new_image_ext = 'png';
                        break;
    
                case 'image/gif':
                        $image_create_func = 'imagecreatefromgif';
                        $image_save_func = 'imagegif';
                        $new_image_ext = 'gif';
                        break;
    
                default: 
                        throw new \Exception('Unknown image type.');
        }
    
        $img = $image_create_func($originalFile);
        list($width, $height) = getimagesize($originalFile);
    
        $newHeight = ($height / $width) * $newWidth;
        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
        if (file_exists($targetFile)) {
                unlink($targetFile);
        }
        //$image_save_func($tmp, "$targetFile.$new_image_ext");
        $image_save_func($tmp, $targetFile);
    }

    /**
     * @Route("/set/critereSearch/series", name="set_critetesearch_series")
     * @return JsonResponse
     */
    public function setCritereSearchSeries(Request $request)
    {
        $parameters = $this->get('session')->get('parameters');
        $action = $request->get('action');
        $em = $this->getDoctrine()->getManager();
        $this->get('session')->remove('critere_search_series');
        if ($action == "save") {
            $typoId = $request->request->get('typoId');
            $ensPayId = $request->request->get('ensPayId');
            $uniPayId = $request->request->get('uniPayId');
            $payId = $request->request->get('payId');
            $regId = $request->request->get('regId');
            $depId = $request->request->get('depId');
            $comId = $request->request->get('comId');
            $axeTheId = $request->request->get('axeTheId');
            $serId = $request->request->get('serId');
            $oppId = $request->request->get('oppId');
            $ann = $request->request->get('ann');
            $dataSearchThesaurus = $request->request->get('thesaurus');
            $porteurOppId = $request->request->get('porteurOppId');
            
            
            $critereSearchSeries = [
                'typoId'    => $typoId,
                'ensPayId'  => $ensPayId,
                'uniPayId'  => $uniPayId,
                'payId'     => $payId,
                'regId'     => $regId,
                'depId'     => $depId,
                'comId'     => $comId,
                'axeTheId'  => $axeTheId,
                'serId'     => $serId,
                'oppId'     => $oppId,
                'ann'       => $ann,
                'dataSearchThesaurus'       => $dataSearchThesaurus,
                'porteurOppId'       => $porteurOppId
            ];
            
            $this->get('session')->set('critere_search_series', $critereSearchSeries);
            /*$searchSeries = $this->getSearchSeries();
            //$critereSearchSeries['series'] = $searchSeries;
            $this->get('session')->set('critere_search_series', $critereSearchSeries);*/
        }else if ($action == "remove") {
            //TODO
            //$searchSeries = $this->getSearchSeries();
        }
        $searchSeries = $this->getSearchSeries();
        //$critereSearchSeries['series'] = $searchSeries;
        //$this->get('session')->set('critere_search_series', $critereSearchSeries);

        $tabSeries = [];
        foreach($searchSeries as $serie){
            $tabSerie['date'] = $serie->getSerieDate()->format('d/m/Y');
            $tabSerie['commune'] = $serie->getSerieCommune()->getCommuneNom();
            //Par défaut OPP local
            $tabSerie['opp'] = "local";
            if ($serie->getSerieOpp()){
                if ($serie->getSerieOpp()->getOppParticipative() == false){
                    $tabSerie['opp'] = "local";
                }else{
                    $tabSerie['opp'] = "participatif";
                }
            }
            $tabSerie['titre'] = $serie->getSerieTitre();
            $tabSerie['id'] = $serie->getSerieId();
            $photosSerie = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie), array('photoDatePrise' => 'ASC' ));
            $tabPhotosSeries = [];
            $limit = 10;
            $i = 0;
            foreach($photosSerie as $photoSerie){
                $i++;
                if($photoSerie->getPhotoFile()){
                    $tabPhotoSerie['url'] = $parameters['URL_POPP'] . '/files/miniature' . $photoSerie->getPhotoFile()->getFileManagerUri();
                }else{
                    $tabPhotoSerie['url'] = '';
                }
                $tabPhotoSerie['id'] = $photoSerie->getPhotoId();
                $tabPhotoSerie['date'] = $photoSerie->getPhotoDatePrise()->format('d/m/Y');
                $tabPhotoSerie['titre'] = $photoSerie->getPhotoTitre();
                $tabPhotosSeries[] = $tabPhotoSerie;
                if ($i >= $limit){
                    break;
                }
            }
            $tabSerie['photos'] = $tabPhotosSeries;
            $tabSeries[] = $tabSerie;
        }

        return new JsonResponse(array(
            'status' => 'ok', 
            'nbseries' => count($searchSeries),
            'tabSeries' => $tabSeries,
            'action' => $action
        ));
    }

    public function getSearchSeries(){
        $em = $this->getDoctrine()->getManager();
        $critereSearchSeries = $this->get('session')->get('critere_search_series');

        if(empty($critereSearchSeries)){
            $critereSearchSeries = [];
        }
        $critereSearchSeries['typoId'] = (!isSet($critereSearchSeries['typoId'])) ? "all" : $critereSearchSeries['typoId'];
        $critereSearchSeries['ensPayId'] = (!isSet($critereSearchSeries['ensPayId'])) ? "all" : $critereSearchSeries['ensPayId'];
        $critereSearchSeries['uniPayId'] = (!isSet($critereSearchSeries['uniPayId'])) ? "all" : $critereSearchSeries['uniPayId'];
        $critereSearchSeries['payId'] = (!isSet($critereSearchSeries['payId'])) ? "all" : $critereSearchSeries['payId'];
        $critereSearchSeries['regId'] = (!isSet($critereSearchSeries['regId'])) ? "all" : $critereSearchSeries['regId'];
        $critereSearchSeries['depId'] = (!isSet($critereSearchSeries['depId'])) ? "all" : $critereSearchSeries['depId'];
        $critereSearchSeries['comId'] = (!isSet($critereSearchSeries['comId'])) ? "all" : $critereSearchSeries['comId'];
        $critereSearchSeries['axeTheId'] = (!isSet($critereSearchSeries['axeTheId'])) ? "all" : $critereSearchSeries['axeTheId'];
        $critereSearchSeries['serId'] = (!isSet($critereSearchSeries['serId'])) ? "all" : $critereSearchSeries['serId'];
        $critereSearchSeries['oppId'] = (!isSet($critereSearchSeries['oppId'])) ? "all" : $critereSearchSeries['oppId'];
        $critereSearchSeries['ann'] = (!isSet($critereSearchSeries['ann'])) ? "all" : $critereSearchSeries['ann'];
        $critereSearchSeries['dataSearchThesaurus'] = (!isSet($critereSearchSeries['dataSearchThesaurus'])) ? "" : $critereSearchSeries['dataSearchThesaurus'];
        $critereSearchSeries['series'] = (!isSet($critereSearchSeries['series'])) ? [] : $critereSearchSeries['series'];
        $critereSearchSeries['porteurOppId'] = (!isSet($critereSearchSeries['porteurOppId'])) ? "all" : $critereSearchSeries['porteurOppId'];
        //print_r($critereSearchSeries);
        /*if(empty($critereSearchSeries)){
            $critereSearchSeries['typoId']    = "all";
            $critereSearchSeries['ensPayId']  = "all";
            $critereSearchSeries['uniPayId']  = "all";
            //Pays France par défaut
            $critereSearchSeries['payId']     = "all";
            //$critereSearchSeries['payId']     = "1";
            //Région Bretagne par défaut
            $critereSearchSeries['regId']     = "all";
            //$critereSearchSeries['regId']     = "15";
            $critereSearchSeries['depId']     = "all";
            $critereSearchSeries['comId']     = "all";
            $critereSearchSeries['axeTheId']  = "all";
            $critereSearchSeries['serId']     = "all";
            $critereSearchSeries['oppId']     = "all";
            $critereSearchSeries['ann']       = "all";
            $critereSearchSeries['dataSearchThesaurus']       = "";
            $critereSearchSeries['series']       = [];
            $critereSearchSeries['porteurOppId']       = 'all';
        }*/
        $tabSearch = [];
        $tabSearch['seriePublie'] = true;
        //Tri par TypologiePaysage
        if($critereSearchSeries['typoId'] != 'all'){
            $typologiePaysage = $em->getRepository(TypologiePaysage::class)->find($critereSearchSeries['typoId']); 
            $tabSearch['serieTypologie'] = $typologiePaysage;
            //$tabSearch['serieTypologie'] = $critereSearchSeries['typoId'];
        }
        //Tri par EnsemblePaysager
        if($critereSearchSeries['ensPayId'] != 'all'){
            $ensemblePaysager = $em->getRepository(EnsemblePaysager::class)->find($critereSearchSeries['ensPayId']); 
            $tabSearch['serieEnsemblePaysage'] = $ensemblePaysager;
        }
        //Tri par UnitePaysageLocale
        if($critereSearchSeries['uniPayId'] != 'all'){
            $UnitePaysage = $em->getRepository(UnitePaysage::class)->find($critereSearchSeries['uniPayId']); 
            $tabSearch['serieUnitePaysagere'] = $UnitePaysage;
        }
        //Tri par Pays
        if($critereSearchSeries['payId'] != 'all'){
            $Pays = $em->getRepository(Pays::class)->find($critereSearchSeries['payId']); 
            $tabSearch['seriePays'] = $Pays;
        }
        //Tri par Region
        if($critereSearchSeries['regId'] != 'all'){
            $region = $em->getRepository(Region::class)->find($critereSearchSeries['regId']); 
            $tabSearch['serieRegion'] = $region;
        }
        //Tri par Departement
        if($critereSearchSeries['depId'] != 'all'){
            $Departement = $em->getRepository(Departement::class)->find($critereSearchSeries['depId']); 
            $tabSearch['serieDepartement'] = $Departement;
        }
        //Tri par Commune
        if($critereSearchSeries['comId'] != 'all'){
            $Commune = $em->getRepository(Commune::class)->find($critereSearchSeries['comId']); 
            $tabSearch['serieCommune'] = $Commune;
        }
        //Tri par serieIdentifiantSerie
        if($critereSearchSeries['serId'] != 'all'){ 
            $tabSearch['serieId'] = $critereSearchSeries['serId'];
        }
        //Tri par Opp
        if($critereSearchSeries['oppId'] != 'all'){
            $Opp = $em->getRepository(Opp::class)->find($critereSearchSeries['oppId']); 
            $tabSearch['serieOpp'] = $Opp;
        }
        //Tri par Structure Opp
        if($critereSearchSeries['porteurOppId'] != 'all'){
            $porteurOpp = $em->getRepository(PorteurOpp::class)->find($critereSearchSeries['porteurOppId']); 
            $tabSearch['seriePorteurOpp'] = $porteurOpp;
        }

        $tabSerie = [];
        $repoPhoto = $em->getRepository(Photo::class);
        $series = $em->getRepository(Serie::class)->findBy($tabSearch);
        $repoLPhotoThesaurus = $em->getRepository(LPhotoThesaurus::class);
        $repoLThesaurusEvolution = $em->getRepository(LThesaurusEvolution::class);
        $repoThesaurus = $em->getRepository(ThesaurusTree::class);
        $repoThesaurusFacultatif = $em->getRepository(ThesaurusTreeFacultatif::class);
        $repoLPhotoThesaurusFacultatif = $em->getRepository(LPhotoThesaurusFacultatif::class);
        $repoLThesaurusFacultatifEvolution = $em->getRepository(LThesaurusFacultatifEvolution::class);
        $repoEvolutionPaysage = $em->getRepository(EvolutionPaysage::class);
        foreach($series as $serie){
            $validSerie = true;
            //Tri par axe thématique
            if($critereSearchSeries['axeTheId'] != 'all' && $validSerie){
                $AxeThematic = $em->getRepository(AxeThematic::class)->find($critereSearchSeries['axeTheId']); 
                $lSerieAxeThe = $em->getRepository(LSerieAxeThematic::class)
                    ->findBy(array(
                        'lSatSerie' => $serie, 
                        'lSatAxeThematic' => $AxeThematic
                    ));
                if (!$lSerieAxeThe){
                    $validSerie = false;
                }
            }
            //Tri par année
            if($critereSearchSeries['ann'] != 'all' && $validSerie){
                $validAnnPhoto = false;
                $photos = $repoPhoto->findBy(array('photoSerie' => $serie));
                foreach($photos as $photo){
                    $anneePhoto = $photo->getPhotoDatePrise()->format('Y');
                    if ($anneePhoto == $critereSearchSeries['ann']){
                        $validAnnPhoto = true;
                    }
                }
                if (!$validAnnPhoto){
                    $validSerie = false;
                }
            }

            //Tri par thésaurus
            if($critereSearchSeries['dataSearchThesaurus'] != '' && $validSerie){
                $validThesaurusPhoto = false;
                $tabSearchThesaurus = explode(',', $critereSearchSeries['dataSearchThesaurus']);
                foreach($tabSearchThesaurus as $searchThesaurus){
                    if($validThesaurusPhoto){
                        break;
                    }
                    list($idSearchThesaurus, $idSearchEvolution) = explode('_', $searchThesaurus);
                    $photos = $repoPhoto->findBy(array('photoSerie' => $serie));
                    foreach($photos as $photo){
                        //Thesaurus
                        if(strpos($idSearchThesaurus, 'f-') === false){
                            $thesaurus = $repoThesaurus->find($idSearchThesaurus);
                            $lPhotosThesaurus = $repoLPhotoThesaurus->findBy(array('lPtPhoto' => $photo, 'lPtThesaurus' => $thesaurus));
                            foreach($lPhotosThesaurus as $lPhotoThesaurus){
                                $evolution = $repoEvolutionPaysage->find($idSearchEvolution);
                                $lThesaurusEvolutions = $repoLThesaurusEvolution->findOneBy(array('lTePhotoThesaurus' => $lPhotoThesaurus, 'lTeEvolution' => $evolution));
                                if(isSet($lThesaurusEvolutions)){
                                    $validThesaurusPhoto = true;
                                }
                            }
                        }else{
                            //Thésaurus Facultatif
                            $idSearchThesaurusFacultatif = str_replace('f-', '', $idSearchThesaurus);
                            $thesaurusFacultatif = $repoThesaurusFacultatif->find($idSearchThesaurusFacultatif);
                            $lPhotosThesaurusFacultatif = $repoLPhotoThesaurusFacultatif->findBy(array('lPtfPhoto' => $photo, 'lPtfThesaurus' => $thesaurusFacultatif));
                            foreach($lPhotosThesaurusFacultatif as $lPhotoThesaurusFacultatif){
                                $evolution = $repoEvolutionPaysage->find($idSearchEvolution);
                                $lThesaurusFacultatifEvolutions = $repoLThesaurusFacultatifEvolution->findOneBy(array('lTfePhotoThesaurus' => $lPhotoThesaurusFacultatif, 'lTfeEvolution' => $evolution));
                                if(isSet($lThesaurusFacultatifEvolutions)){
                                    $validThesaurusPhoto = true;
                                }
                            }
                        }
                    }
                }
                if (!$validThesaurusPhoto){
                    $validSerie = false;
                }
            }

            if($validSerie){
                $tabSerie[] = $serie;
                //$tabSeriesReturn[] = $tabSerie;
            }
        }
        $critereSearchSeries['series']       = $tabSerie;
        $this->get('session')->set('critere_search_series', $critereSearchSeries);
        return $tabSerie;
    }

    /**
     * @Route("/get/series/geojson", name="get_series_geojson")
     * @return JsonResponse
     */
    public function getSeriesGeojson()
    {
        $em = $this->getDoctrine()->getManager();
        $critereSearchSeries =  $this->get('session')->get('critere_search_series');
        $series = $critereSearchSeries['series'];
        
        //print_r($critereSearchSeries);
        $features = [];
        foreach($series as $serie){
            $typeOpp = "local";
            //Récupération du type de porteurOpp
            //$porteurOpp = $em->getRepository(PorteurOpp::class)->findAll();
            //$seriePorteur = $serie->getSeriePorteurOpp($porteurOpp);
            
            if ($serie->getSerieOpp()){
                if ($serie->getSerieOpp()->getOppParticipative() == false){
                    $typeOpp = "local";
                }else{
                    $typeOpp = "participatif";
                }
            }

            $photos = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie), array('photoDatePrise' => 'ASC' ));
            $photoSerie = [];

            $limit=10;
            $i=0;
            foreach($photos as $photo){
                $i++;
                if($photo->getPhotoFile()){
                    $fileUri = $photo->getPhotoFile()->getFileManagerUri();
                }else{
                    $fileUri = "";
                }
                $idPhoto = $photo->getPhotoId();
                $anneePhoto = $photo->getPhotoDatePrise()->format('d/m/Y');
                array_push($photoSerie, ['id' => $idPhoto, 'date' => $anneePhoto, 'url' => $fileUri]);
                if ($i >= $limit){
                    break;
                }
            };    
            $feature = array(
                "type"       => "Feature", 
                "geometry"   => array(
                    "type"        => "Point",
                    "coordinates" => [$serie->getSerieGeomX(), $serie->getSerieGeomY()],
                ),
                "properties" => array(
                    "id"          => $serie->getSerieId(),
                    "title"       => $serie->getSerieTitre(),
                    "photosSerie" => $photoSerie,
                    "type"        => $typeOpp,
                    "commune"     => $serie->getSerieCommune()->getCommuneInsee() . " " . $serie->getSerieCommune()->getCommuneNom(),
                )
            );
            $features[] = $feature;
        }
        $geoJson["type"] = "FeatureCollection";
        $geoJson["test"] = "test";
        $geoJson["features"] = $features;
        return new JsonResponse($geoJson);
    }
    
    /**
     * @Route("get/init/param", name="getInitParam")
     * @return JsonResponse
     */
    public function getInitParam()
    {
        $critereSearchSeries =  $this->get('session')->get('critere_search_series');
        return new JsonResponse($critereSearchSeries);
    }

    /**
     * @Route("public/get/serie/{id}", name="get_serie")
     * @param string $id id
     * @return Response
     */

    public function getViewSerie($id)
    {
        //Chargement des repos
        $em = $this->getDoctrine()->getManager();
        $repoLPhotoThesaurus = $em->getRepository(LPhotoThesaurus::class);
        $repoLThesaurusEvolution = $em->getRepository(LThesaurusEvolution::class);
        $tabEvolutionPaysage = $em->getRepository(EvolutionPaysage::class)->findAll();
        $evolutionPresence = $em->getRepository(EvolutionPaysage::class)->findBy(array('evolutionPaysageNom' => 'Stabilité'));
        
        $repoLPhotoThesaurusFacultatif = $em->getRepository(LPhotoThesaurusFacultatif::class);
        $repoLThesaurusFacultatifEvolution = $em->getRepository(LThesaurusFacultatifEvolution::class);

        //Récupération des Entities concernées
        $serie = $em->getRepository(Serie::class)->find($id);
        $photos = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie), array('photoDatePrise' => 'ASC'));

        //Récupération des Axes thématiques pour la série
        $AxeTheBySerie = $em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatSerie' => $serie));
        $tabAxeTheme = [];
        foreach($AxeTheBySerie as $axeThe){
            $tabAxeTheme[] = $axeThe->getLSatAxeThematic();
        }

        //Récupération des séries pour les axes thémariques
        //$seriesByAxeThe = $em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatAxeThematic' => $tabAxeTheme),  array('lSatSerie' => 'ASC'));

        //On boucle sur les photos pour récupérer les licences
        $tabLicencesPhotos = [];
        //On boucle sur les photos pour récupérer les évolutions 
        $tabEvolutionsThesaurus = [];
        $indexPhoto = 1;
        $tabCommentaires = [];
        $firstPhoto = true;
        foreach($photos as $photo){
            if ($photo->getPhotoLicence()){
                $tabLicencesPhotos[$photo->getPhotoLicence()->getLicenceId()] = $photo->getPhotoLicence();
            }
            //thésaurus
            $photoThesaurus =  $repoLPhotoThesaurus->findBy(array('lPtPhoto' => $photo));
            $tabEvolution = [];
            //On boucle sur les thésaurus associés à la photo 
            foreach($photoThesaurus as $thesaurus){
                $thesaurusNom = $thesaurus->getLPtThesaurus()->getThesaurusTreeNom();
                $thesaurusId = $thesaurus->getLPtThesaurus()->getThesaurusTreeId();
                if($firstPhoto){
                    $thesaurusEvolution = $repoLThesaurusEvolution->findBy(array('lTePhotoThesaurus' => $thesaurus, 'lTeEvolution' => $evolutionPresence));
                    if($thesaurusEvolution){
                        $tabEvolution[0]['nom'] = "Présence";
                        $tabEvolution[0]['id'] = 0;
                        $tabEvolution[0]['nb'] = 1;
                        $tabEvolution[0]['listePhoto'] = $indexPhoto;
                        $tabEvolution[0]['libelleListePhoto'] = 'Photo concernée';
                    }
                }else{
                    $thesaurusEvolution = $repoLThesaurusEvolution->findBy(array('lTePhotoThesaurus' => $thesaurus));
                    if (isSet( $tabEvolutionsThesaurus[$thesaurusId])){
                        $tabEvolution = $tabEvolutionsThesaurus[$thesaurusId]['evolution'];
                    }else{
                        $tabEvolution = [];
                    }
                    //On boucle sur les évolutions associées au thésaurus de la photo
                    foreach($thesaurusEvolution as $evolution){
                        $evolutionNom = $evolution->getLTeEvolution()->getEvolutionPaysageNom();
                        $evolutionId = $evolution->getLTeEvolution()->getEvolutionPaysageId();
                        $tabEvolution[$evolutionId]['nom'] = $evolutionNom;
                        $tabEvolution[$evolutionId]['id'] = $evolutionId;
                        if(isSet($tabEvolution[$evolutionId]['nb'])){
                            $tabEvolution[$evolutionId]['nb']++;
                            $tabEvolution[$evolutionId]['listePhoto'] .= ', ' . $indexPhoto;
                            $tabEvolution[$evolutionId]['libelleListePhoto'] = 'Photos concernées';
                        }else{
                            $tabEvolution[$evolutionId]['nb'] = 1;
                            $tabEvolution[$evolutionId]['listePhoto'] = $indexPhoto;
                            $tabEvolution[$evolutionId]['libelleListePhoto'] = 'Photo concernée';
                        }
                    }
                }

                $tabEvolutionsThesaurus[$thesaurusId]['nom'] = $thesaurusNom;
                $tabEvolutionsThesaurus[$thesaurusId]['evolution'] = $tabEvolution;
            }
            
            //thésaurus Facultatif
            $photoThesaurusFacultatif =  $repoLPhotoThesaurusFacultatif->findBy(array('lPtfPhoto' => $photo));
            $tabEvolution = [];
            //On boucle sur les thésaurus facultatifs associés à la photo 
            foreach($photoThesaurusFacultatif as $thesaurusFacultatif){
                $thesaurusNom = $thesaurusFacultatif->getLPtfThesaurus()->getThesaurusTreeFacultatifNom();
                $thesaurusId = $thesaurusFacultatif->getLPtfThesaurus()->getThesaurusTreeFacultatifId();
                if($firstPhoto){
                    $thesaurusEvolution = $repoLThesaurusFacultatifEvolution->findBy(array('lTfePhotoThesaurus' => $thesaurusFacultatif, 'lTfeEvolution' => $evolutionPresence));
                    if($thesaurusEvolution){
                        $tabEvolution[0]['nom'] = "Présence";
                        $tabEvolution[0]['id'] = 0;
                        $tabEvolution[0]['nb'] = 1;
                        $tabEvolution[0]['listePhoto'] = $indexPhoto;
                        $tabEvolution[0]['libelleListePhoto'] = 'Photo concernée';
                    }
                }else{
                    $thesaurusEvolution = $repoLThesaurusFacultatifEvolution->findBy(array('lTfePhotoThesaurus' => $thesaurusFacultatif));
                    //TODO
                    if (isSet( $tabEvolutionsThesaurus['f_'.$thesaurusId])){
                        $tabEvolution = $tabEvolutionsThesaurus['f_'.$thesaurusId]['evolution'];
                    }else{
                        $tabEvolution = [];
                    }
                    //On boucle sur les évolutions associées au thésaurus de la photo
                    foreach($thesaurusEvolution as $evolution){
                        $evolutionNom = $evolution->getLTfeEvolution()->getEvolutionPaysageNom();
                        $evolutionId = $evolution->getLTfeEvolution()->getEvolutionPaysageId();
                        $tabEvolution[$evolutionId]['nom'] = $evolutionNom;
                        $tabEvolution[$evolutionId]['id'] = $evolutionId;
                        if(isSet($tabEvolution[$evolutionId]['nb'])){
                            $tabEvolution[$evolutionId]['nb']++;
                            $tabEvolution[$evolutionId]['listePhoto'] .= ', ' . $indexPhoto;
                            $tabEvolution[$evolutionId]['libelleListePhoto'] = 'Photos concernées';
                        }else{
                            $tabEvolution[$evolutionId]['nb'] = 1;
                            $tabEvolution[$evolutionId]['listePhoto'] = $indexPhoto;
                            $tabEvolution[$evolutionId]['libelleListePhoto'] = 'Photo concernée';
                        }
                    }
                }

                $tabEvolutionsThesaurus['f_'.$thesaurusId]['nom'] = $thesaurusNom;
                $tabEvolutionsThesaurus['f_'.$thesaurusId]['evolution'] = $tabEvolution;
            }

            $indexPhoto++;
            $commentaires = $em->getRepository(Commentaire::class)->findBy(array('commentairePhoto' => $photo, 'commentaireEtat' => 1), array('commentaireDate' => 'ASC'));
            foreach($commentaires as $commentaire){
                $tabCommentaires[] = $commentaire;
            }
            $firstPhoto = false;
        }
        

        $liensExterneSerie = $em->getRepository(LienExterne::class)->findBy(array('lienExterneSerie' => $serie));

        $sons = $em->getRepository(Son::class)->findBy(array('sonSerie' => $serie));
        $docs = $em->getRepository(Document::class)->findBy(array('documentSerie' => $serie));
        
        //print_r($tabEvolutionsThesaurus);
        $isModifiable = false;
        if ($this->isGranted('ROLE_FOURNISSEUR')){
            $user = $this->getUser();
            $OppsByFournisseur = $em->getRepository(LFournisseurOpp::class)->findBy(array('lFoUsers' => $user, 'lFoOpp' => $serie->getSerieOpp()));
            if($OppsByFournisseur){
                $isModifiable = true;
            }
        }
        if ($this->isGranted('ROLE_GESTIONNAIRE')){
            $user = $this->getUser();
            $OppsByGestionnaire = $em->getRepository(LGestionnaireOpp::class)->findBy(array('lGoUsers' => $user, 'lGoOpp' => $serie->getSerieOpp()));
            if($OppsByGestionnaire){
                $isModifiable = true;
            }
        }

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        return $this->render("serie/get_serie.html.twig",
            array(
                'serie' => $serie,
                'photos' => $photos,
                'AxeTheBySerie' => $AxeTheBySerie,
                //'seriesByAxeThe' => $seriesByAxeThe,
                'tabLicencesPhotos' => $tabLicencesPhotos,
                'tabEvolutionsThesaurus' => $tabEvolutionsThesaurus,
                'tabEvolutionPaysage' => $tabEvolutionPaysage,
                'liensExterneSerie' => $liensExterneSerie,
                'sons' => $sons,
                'docs' => $docs,
                'commentaires' => $tabCommentaires,
                'structures' => $structures,
                'isModifiable' => $isModifiable,
                'nbWaitingComments' => $nbWaitingComments
            ));
    }

    /**
     * @Route("gestion/update/serie/{serieId}", name="updateSerie")
     * @return Response
     */
    public function updateSerie(string $serieId, TranslatorInterface $translator)
    {
        //Récupération du FileManagerDAO
        $fileManagerDAO = $this->get('filemanager.dao');
        //paramètres golobaux
        $parameters = $this->get('session')->get('parameters');
        
        $em = $this->getDoctrine()->getManager();
        $serie = $em->getRepository(Serie::class)->find($serieId);

        
        $langues = $em->getRepository(Langue::class)->findBy(array(), array('langueNom' => 'ASC'));
        $axeThematics = $em->getRepository(AxeThematic::class)->findBy(array(), array('axeThematicNom' => 'ASC'));
        $communes = $em->getRepository(Commune::class)->findBy(array(), array('communeNom' => 'ASC'));
        $departements = $em->getRepository(Departement::class)->findBy(array(), array('departementNom' => 'ASC'));
        $ensemblesPaysagers = $em->getRepository(EnsemblePaysager::class)->findBy(array(), array('ensemblePaysagerNom' => 'ASC'));
        //$opps = $em->getRepository(Opp::class)->findBy(array(), array('oppNom' => 'ASC'));
        $pays = $em->getRepository(Pays::class)->findBy(array(), array('paysNom' => 'ASC'));
        $regions = $em->getRepository(Region::class)->findBy(array(), array('regionNom' => 'ASC'));
        $unitesPaysage = $em->getRepository(UnitePaysage::class)->findBy(array(), array('unitePaysageNom' => 'ASC'));
        $typologiesPaysage = $em->getRepository(TypologiePaysage::class)->findBy(array(), array('typologiePaysageNom' => 'ASC'));
        $structuresOpp = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'ASC'));
        
        $unitesPaysageLocale = $em->getRepository(UnitePaysageLocale::class)->findBy(array(), array('unitePaysageLocaleNom' => 'ASC'));
        $formats = $em->getRepository(Format::class)->findBy(array(), array('formatNom' => 'ASC'));
        $licences = $em->getRepository(Licence::class)->findBy(array(), array('licenceNom' => 'ASC'));

        $serieAxesThematics = $em->getRepository(LSerieAxeThematic::class)->findBy(array("lSatSerie" => $serie));
        $serieUnitePaysagereLocales = $em->getRepository(LSerieUnitePaysagereLocale::class)->findBy(array("lSuplSerie" => $serie));
        $sons = $em->getRepository(Son::class)->findBy(array("sonSerie" => $serie));
        $documents = $em->getRepository(Document::class)->findBy(array("documentSerie" => $serie));
        $liensExt = $em->getRepository(LienExterne::class)->findBy(array("lienExterneSerie" => $serie));
        
        $photos = $em->getRepository(Photo::class)->findBy(array("photoSerie" => $serie), array('photoDatePrise' => 'ASC'));

        $opps = $em->getRepository(Opp::class)->findBy(array(), array('oppNom' => 'ASC'));
        
        $users = $em->getRepository(Users::class)->findBy(array(), array('nom' => 'ASC', 'prenom' => 'ASC'));

        if ($this->isGranted('ROLE_FOURNISSEUR') && !$this->isGranted('ROLE_ADMIN')){
            $opps = [];
            $user = $this->getUser();
            $OppsByFournisseur = $em->getRepository(LFournisseurOpp::class)->findBy(array('lFoUsers' => $user));
            foreach($OppsByFournisseur as $OppByFournisseur){
                $opps[] = $OppByFournisseur->getLFoOpp();
            }
            $OppsByGestionnaire = $em->getRepository(LGestionnaireOpp::class)->findBy(array('lGoUsers' => $user));
            foreach($OppsByGestionnaire as $OppByGestionnaire){
                $opps[] = $OppByGestionnaire->getLGoOpp();
            }
        }

        $lstAxeThematic = [];
        $tabPhotos = [];
        foreach ($serieAxesThematics as $lSatAxeThematic) {
           $lstAxeThematic[] = $lSatAxeThematic->getlSatAxeThematic()->getAxeThematicId();
        }

        $lstUnitePaysageLocal = [];
        foreach ($serieUnitePaysagereLocales as $serieUnitePaysagereLocale) {
           $lstUnitePaysageLocal[] = $serieUnitePaysagereLocale->getLSuplUnitePaysageLocale()->getUnitePaysageLocaleId();
        }

        $repoLPhotoThesaurus = $em->getRepository(LPhotoThesaurus::class);
        $repoLThesaurusEvolution = $em->getRepository(LThesaurusEvolution::class);
        $repoLPhotoThesaurusFacultatif = $em->getRepository(LPhotoThesaurusFacultatif::class);
        $repoLThesaurusFacultatifEvolution = $em->getRepository(LThesaurusFacultatifEvolution::class);
        foreach($photos as $photo){
            //Thésaurus
            $tabThesaurusEvol = [];
            $photoThesaurus = $repoLPhotoThesaurus->findBy(array('lPtPhoto' => $photo));
            foreach($photoThesaurus as $thesaurus){
                $thesaurusEvolution = $repoLThesaurusEvolution->findBy(array('lTePhotoThesaurus' => $thesaurus));
                foreach($thesaurusEvolution as $evolution){
                    $tabThesaurusEvol[] = $thesaurus->getLPtThesaurus()->getThesaurusTreeId() . '_' . $evolution->getLTeEvolution()->getEvolutionPaysageId();
                }
            }
            
            //Thésaurus Facultatif
            $tabThesaurusFacultatifEvol = [];
            $photoThesaurusFacultatif = $repoLPhotoThesaurusFacultatif->findBy(array('lPtfPhoto' => $photo));
            foreach($photoThesaurusFacultatif as $thesaurusFacultatif){
                $thesaurusFacultatifEvolution = $repoLThesaurusFacultatifEvolution->findBy(array('lTfePhotoThesaurus' => $thesaurusFacultatif));
                foreach($thesaurusFacultatifEvolution as $evolution){
                    $tabThesaurusFacultatifEvol[] = $thesaurusFacultatif->getLPtfThesaurus()->getThesaurusTreeFacultatifId() . '_' . $evolution->getLTfeEvolution()->getEvolutionPaysageId();
                }
            }
            $tabPhotos[] = array(
                'photo' => $photo,
                'thesaurus' => implode(',', $tabThesaurusEvol),
                'thesaurusFacult' => implode(',', $tabThesaurusFacultatifEvol)
            );
        }
        
        $defaultValue = [
            'langue' => '',
            'evolution_delete' => '',
            'pays' => '',
            'region' => '',
            'frequence' => ''
        ];
        $regionDefault = $em->getRepository(Region::class)->findOneBy(array('regionNom' => $translator->trans('default_value.region')));
        if ($regionDefault){
            $defaultValue['region'] = $regionDefault->getRegionId();
        }
        $paysFr = $em->getRepository(Pays::class)->findOneBy(array('paysNom' => $translator->trans('default_value.pays')));
        if ($paysFr){
            $defaultValue['pays'] = $paysFr->getPaysId();
        }
        $langueFr = $em->getRepository(Langue::class)->findOneBy(array('langueNom' =>$translator->trans('default_value.langue') ));
        if ($langueFr){
            $defaultValue['langue'] = $langueFr->getLangueId();
        }
        $defaultValue['frequence'] = $translator->trans('default_value.frequence');
        $evolutionDelete = $em->getRepository(EvolutionPaysage::class)->findOneBy(array('evolutionPaysageNom' => $translator->trans('default_value.disparition')));
        if ($evolutionDelete){
            $defaultValue['evolution_delete'] = $evolutionDelete->getEvolutionPaysageId();
        }
        
        //Tri par ordre alphabetique avec les accents
        $tabAxeThematics = [];
        foreach($axeThematics as $axeThematic){
            $str = htmlentities($axeThematic->getAxeThematicNom(), ENT_NOQUOTES, 'utf-8');
            $themeSansAccent = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
            $tabThemeOrder[$themeSansAccent] = $axeThematic->getAxeThematicId();
            $tabAxeThematics[$axeThematic->getAxeThematicId()] = $axeThematic->getAxeThematicNom();
        }

        ksort($tabThemeOrder);
        $tabAxeThematicsOrder = [];
        foreach($tabThemeOrder as $axeThematicId){
            $tabAxeThematicsOrder[$axeThematicId] = $tabAxeThematics[$axeThematicId];
        }
        
        //Tri par ordre alphabetique avec les accents
        $tabCommunes = [];
        foreach($communes as $commune){
            $str = htmlentities($commune->getCommuneNom(), ENT_NOQUOTES, 'utf-8');
            $communeSansAccent = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
            $tabCommuneOrder[$communeSansAccent] = $commune->getCommuneId();
            $tabCommunes[$commune->getCommuneId()] = $commune->getCommuneInsee() . ' ' . $commune->getCommuneNom();
        }

        ksort($tabCommuneOrder);
        $tabCommunesOrder = [];
        foreach($tabCommuneOrder as $communeId){
            $tabCommunesOrder[$communeId] = $tabCommunes[$communeId];
        }

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        return $this->render("serie/create_serie.html.twig", [
            'serie' => $serie,
            'langues' => $langues,
            'departements' => $departements,
            'regions' => $regions,
            'pays' => $pays,
            'opps' => $opps,
            'typologiesPaysage' => $typologiesPaysage,
            'axeThematics' => $tabAxeThematicsOrder,
            'structuresOpp' => $structuresOpp,
            'ensemblesPaysagers' => $ensemblesPaysagers,
            'unitesPaysageLocale' => $unitesPaysageLocale,
            'formats' => $formats,
            'licences' => $licences,
            'unitesPaysage' => $unitesPaysage,
            'communes' => $tabCommunesOrder,
            'action' => 'update',
            'photos' => $tabPhotos,
            'lstAxeThematic' => $lstAxeThematic,
            'lstUnitePaysageLocal' => $lstUnitePaysageLocal,
            'sons' => $sons,
            'documents' => $documents,
            'structures' => $structures,
            'liensExt' => $liensExt,
            'defaultValue' => $defaultValue,
            'nbWaitingComments' => $nbWaitingComments,
            'users' => $users
        ]);
    }       
    
    /**
     * @Route("gestion/remove/serie/{serieId}", name="deleteSerie")
     * @return Response
     */
    public function deleteSerie(string $serieId)
    {
        $em = $this->getDoctrine()->getManager();
        $serie = $em->getRepository(Serie::class)->find($serieId);

        $serieDAO = $this->get('serie.dao');
        $serieDAO->removeSerie($serie);
        return new JsonResponse(array('status' => 'ok'));
    }    
    
    /**
     * @Route("gestion/serie/insertFile", name="insertSerieFile")
     * @return Response
     */
    public function insertSerieFile(){
        //On récupère les paramètres globaux
        $parameters = $this->get('session')->get('parameters');

        if (isset($_FILES['file']) AND $_FILES['file']['error'] == 0)
        {
            // Test si le fichier n'est pas trop gros
            if ($_FILES['file']['size'] <= 256000000)
            {
                // Test si l'extension est autorisée
                $infosfichier = pathinfo($_FILES['file']['name']);
                $extension_upload = strtolower($infosfichier['extension']);
                $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
                //$newPath = 'C:\wamp64\www\serv\popp_breizh\src\assets\upload\\';
                $newPath = $parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_UPLOAD_FILE . '/';
                //On créer le dossier si il n'existe pas
                if (!file_exists($newPath)) {
                    mkdir($newPath, 0777, true);
                }
                if (in_array($extension_upload, $extensions_autorisees))
                {
                    $fname = basename($_FILES['file']['name']);
                    $rawBaseName = pathinfo($fname, PATHINFO_FILENAME );
                    $counter = 0;
                    while(file_exists($newPath . $fname)) {
                        $fname = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                        $counter++;
                    };
                    // Valider le fichier et le stocker définitivement
                    move_uploaded_file($_FILES['file']['tmp_name'], $newPath . $fname);
                    //echo "L'envoi a bien été effectué !";
                }else{
                    return new JsonResponse(array("status" => "error", "message" => "Ce type de fichier n'est pas autorisé."));
                }
            }else{
                return new JsonResponse(array("status" => "error", "message" => "Le fichier est trop volumineux."));
            }
        }else{
            return new JsonResponse(array("status" => "error", "message" => "Erreur lors de l'upload."));
        }

        return new JsonResponse(array(
            "status" => "ok",
            'fileName' => $fname,
            'fileSize' => $_FILES['file']['size'],
            'fileStatut' => $_FILES['file']['error'],
            'fileFormat' => $_FILES['file']['type'],
            'fileURI' => $parameters['URL_FOLDER_FILES'] . '/' . self::FOLDER_UPLOAD_FILE . '/' . $fname,
            'filePath' => self::FOLDER_UPLOAD_FILE . '/' . $fname,
            'fileDate' => date("Y-m-d H:i:s"),
            //'fileType' => $fileType
        ));
    }
    
    /**
     * @Route("gestion/remove/serie/file/{serieFileId}", name="deleteSerieFile")
     * @return Response
     */
    public function deleteSerieFile(string $serieFileId){
        $em = $this->getDoctrine()->getManager();
        
        if($_POST["type"]){
            $fileType = htmlspecialchars($_POST["type"]);
        };
        //Récupération de l'URL
        $file = $em->getRepository(FileManager::class)->find($serieFileId);
        $fileURI = $file->getFileManagerUri();
        
        //Suppression du fichier
        unlink($fileURI);
        
        //Suppression de la clé secondaire du FileManager
        $repoSerie = $em->getRepository(Serie::class);
        
        //Mise à jour de la clé si c'est un croquis
        switch ($fileType){
            case "Croquis":
                $serie = $repoSerie->findOneBy(array('serieCroquis' => $file));
                $serie->setSerieCroquis(null);
                break;
            case "Photo aérienne":
                $serie = $repoSerie->findOneBy(array('seriePhotoAerienne' => $file));
                $serie->setSeriePhotoAerienne(null);
                break;
            case "Photo IGN":
                $serie = $repoSerie->findOneBy(array('seriePhotoIgn' => $file));
                $serie->setSeriePhotoIgn(null);
                break;
            case "Photo de trepied":
                $serie = $repoSerie->findOneBy(array('seriePhotoTrepied' => $file));
                $serie->setSeriePhotoTrepied(null);
                break;
            case "Photo contextuelle":
                $serie = $repoSerie->findOneBy(array('seriePhotoContext' => $file));
                $serie->setSeriePhotoContext(null);
                break;
        }
        
        $em->persist($serie);
                
        //Suppression dans le file Manager
        $em->remove($file);
        $em->flush();
        
        return new JsonResponse(array('status' => 'ok'));
    }

    /**
     * @Route("/public/get/serie/downloadold/{serieId}", name="serie_download_ficheterrain_old")
     * @return JsonResponse
     */
    public function getFicheTerrain(int $serieId, Request $request){

        $em = $this->getDoctrine()->getManager();
        $repoSerie = $em->getRepository(Serie::class);
        
        $serieDAO = $this->get('serie.dao');
        $return = $serieDAO->generateFicheTerrainPdf($serieId);

        return new Response($return);
        return new JsonResponse(array(
            'status' => 'ok'
        ));


        //paramètres golobaux
        $parameters = $this->get('session')->get('parameters');

        $em = $this->getDoctrine()->getManager();
        $serie = $em->getRepository(Serie::class)->find($serieId);
        $uri = $request->getHost();
        $port = $request->getPort();

        $coordinates2154 = $_POST["coordinates2154"];
        $longitude2154 = $coordinates2154[0];
        $latitude2154 = $coordinates2154[1];
        
            $photo = $em->getRepository(Photo::class)->findOneBy(array('photoSerie' => $serie), array('photoDatePrise' => 'ASC'));

        
        if (!file_exists($parameters['PATH_FOLDER_FILES'] . self::FOLDER_FICHE_SERIE)) {
            mkdir($parameters['PATH_FOLDER_FILES'] . self::FOLDER_FICHE_SERIE, 0777, true);
        }
        $datetime = new \DateTime();
        $filePath = $parameters['PATH_FOLDER_FILES'] . self::FOLDER_FICHE_SERIE . $serieId . '_' . $datetime->format('U') . '.pdf';
        $fileUrl = $parameters['URL_FOLDER_FILES'] . self::FOLDER_FICHE_SERIE . $serieId . '_' . $datetime->format('U') . '.pdf';
        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView(
                'serie/download_fiche.html.twig', array('serie' => $serie, 'photo' => $photo, 'longitude2154' => $longitude2154, 'latitude2154' => $latitude2154)
            ),
            $filePath
        );

        return new JsonResponse(array(
            'status' => 'ok', 
            'fileUrl' => $fileUrl,
            'longitude2154' => $longitude2154,
            'latitude2154' => $latitude2154,
        ));
    }

    /**
     * @Route("/public/get/serie/download/{serieId}", name="serie_download_ficheterrain")
     * @return JsonResponse
     */
    public function getFicheTerrain2(int $serieId, Request $request){

        //paramètres golobaux
        $parameters = $this->get('session')->get('parameters');
        
        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->get('kernel')->getProjectDir() . '/public';
        // e.g /var/www/project/public/mypdf.pdf
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('tempDir', $publicDirectory);
        //$pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $em = $this->getDoctrine()->getManager();
        $serie = $em->getRepository(Serie::class)->find($serieId);

        $coordinates2154 = $_POST["coordinates2154"];
        $longitude2154 = $coordinates2154[0];
        $latitude2154 = $coordinates2154[1];
        
        //Récupération des Axes thématiques pour la série
        $AxeTheBySerie = $em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatSerie' => $serie));
        
        $photo = $em->getRepository(Photo::class)->findOneBy(array('photoSerie' => $serie), array('photoDatePrise' => 'ASC'));

        // Retrieve the HTML generated in our twig file
        $base64 = "data:image/png;base64, ".base64_encode('http://geofit-popp.ataraxie.fr/assets/images/popp.c8b2847d.png');
        //return new JsonResponse($base64);
        $html = $this->renderView(
            'serie/download_fiche.html.twig', 
            array('logo' => $base64, 'serie' => $serie, 'photo' => $photo, 'longitude2154' => $longitude2154, 'latitude2154' => $latitude2154 , 'AxeTheBySerie'  => $AxeTheBySerie)
        );

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        $output = $dompdf->output();

        $datetime = new \DateTime();
        $filePath = $parameters['PATH_FOLDER_FILES'] . self::FOLDER_FICHE_SERIE . $serieId . '_' . $datetime->format('U') . '.pdf';
        $fileUrl = $parameters['URL_FOLDER_FILES'] . self::FOLDER_FICHE_SERIE . $serieId . '_' . $datetime->format('U') . '.pdf';
        $pdfFilepath =  $publicDirectory . '/mypdf.pdf';
        
        // Write file to the desired path
        file_put_contents($filePath, $output);

        return new JsonResponse(array(
            'status' => 'ok', 
            'fileUrl' => $fileUrl,
            'filePath' => $filePath,
            'AxeTheBySerie'  => $AxeTheBySerie,
            'longitude2154' => $longitude2154,
            'latitude2154' => $latitude2154,
        ));
    }
    
    /**
     * @Route("/public/get/serie/export/{serieId}", name="serie_export")
     * @return Response
     */
    public function getExportSerie(int $serieId, Request $request){
        ini_set("memory_limit","512M");
        //paramètres golobaux
        $parameters = $this->get('session')->get('parameters');
        
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $em = $this->getDoctrine()->getManager();
        $serie = $em->getRepository(Serie::class)->find($serieId);
        
        $repoLPhotoThesaurus = $em->getRepository(LPhotoThesaurus::class);
        $repoLThesaurusEvolution = $em->getRepository(LThesaurusEvolution::class);
        $tabEvolutionPaysage = $em->getRepository(EvolutionPaysage::class)->findAll();
        $evolutionPresence = $em->getRepository(EvolutionPaysage::class)->findBy(array('evolutionPaysageNom' => 'Stabilité'));

        $repoLPhotoThesaurusFacultatif = $em->getRepository(LPhotoThesaurusFacultatif::class);
        $repoLThesaurusFacultatifEvolution = $em->getRepository(LThesaurusFacultatifEvolution::class);

        $photos = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie), array('photoDatePrise' => 'ASC'));
        $tablePhotos = [];
        $firstPhoto = true;
        foreach($photos as $photo){
            //Initialisation des Repos

            $photoThesaurus =  $repoLPhotoThesaurus->findBy(array('lPtPhoto' => $photo));
            $photoThesaurusFacultatif =  $repoLPhotoThesaurusFacultatif->findBy(array('lPtfPhoto' => $photo));
            //print_r($photoThesaurus);
            $tabFinal = '';
            $isValid = false;
            if(count($photoThesaurus) > 0 || count($photoThesaurusFacultatif) > 0){
                $tableauThesaurusHead = [];
                $tableauThesaurusHead[] = "<table class='table-border'  style='font-size:0.8em'><thead><tr>";
                $tableauThesaurusHead[] = "<th>Eléments</th>";
                $tableauThesaurusHead[] = "<th>Présence</th>";
                foreach($tabEvolutionPaysage as $evolutionPaysage){
                    $tableauThesaurusHead[] = "<th>" . $evolutionPaysage->getEvolutionPaysageNom() . "</th>";
                }
                $tableauThesaurusHead[] = "</tr></thead>";
                $tabFinal = implode("\n", $tableauThesaurusHead);
            }
            //On boucle sur les thésaurus associés à la photo 
            foreach($photoThesaurus as $thesaurus){
                //echo $thesaurus->getLPtId();
                $tableauThésaurus = [];
                $tableauThésaurus[] = "<tr>";
                $tableauThésaurus[] = "<td>" . $thesaurus->getLPtThesaurus()->getThesaurusTreeNom() . "</td>";
                if($firstPhoto){
                    $thesaurusEvolution = $repoLThesaurusEvolution->findBy(array('lTeEvolution' => $evolutionPresence, 'lTePhotoThesaurus' => $thesaurus));
                    if(count($thesaurusEvolution) > 0){
                        $isValid = true;
                        $tableauThésaurus[] = "<td><center>X</center></td>";
                    }
                    foreach($tabEvolutionPaysage as $evolutionPaysage){
                        $tableauThésaurus[] = "<td></td>";
                    }
                }else{
                    $tableauThésaurus[] = "<td></td>";
                    foreach($tabEvolutionPaysage as $evolutionPaysage){
                        $thesaurusEvolution = $repoLThesaurusEvolution->findBy(array('lTeEvolution' => $evolutionPaysage, 'lTePhotoThesaurus' => $thesaurus));
                        if(count($thesaurusEvolution) > 0){
                            $isValid = true;
                            $tableauThésaurus[] = "<td><center>X</center></td>";
                        }else{
                            $tableauThésaurus[] = "<td></td>";
                        }
                    }
                }
                $tableauThésaurus[] = "</tr>";
                $tabFinal .= implode("\n", $tableauThésaurus);
            }
            
            //On boucle sur les thésaurus Facultatif associés à la photo 
            foreach($photoThesaurusFacultatif as $thesaurus){
                //echo $thesaurus->getLPtId();
                $tableauThésaurus = [];
                $tableauThésaurus[] = "<tr>";
                $tableauThésaurus[] = "<td scope='col'>" . $thesaurus->getLPtfThesaurus()->getThesaurusTreeFacultatifNom() . "</td>";
                if($firstPhoto){
                    $thesaurusEvolution = $repoLThesaurusFacultatifEvolution->findBy(array('lTfeEvolution' => $evolutionPresence, 'lTfePhotoThesaurus' => $thesaurus));
                    if(count($thesaurusEvolution) > 0){
                        $isValid = true;
                        $tableauThésaurus[] = "<td><center>X</center></td>";
                    }
                    foreach($tabEvolutionPaysage as $evolutionPaysage){
                        $tableauThésaurus[] = "<td></td>";
                    }
                }else{
                    $tableauThésaurus[] = "<td></td>";
                    foreach($tabEvolutionPaysage as $evolutionPaysage){
                        $thesaurusEvolution = $repoLThesaurusFacultatifEvolution->findBy(array('lTfeEvolution' => $evolutionPaysage, 'lTfePhotoThesaurus' => $thesaurus));
                        if(count($thesaurusEvolution) > 0){
                            $isValid = true;
                            $tableauThésaurus[] = "<td scope='col'><center>X</center></td>";
                        }else{
                            $tableauThésaurus[] = "<td scope='col'></td>";
                        }
                    }
                }
                $tableauThésaurus[] = "</tr>";
                $tabFinal .= implode("\n", $tableauThésaurus);
            }
            $firstPhoto = false;
            
            if($isValid){
                $tabFinal .= "</table>";
            }else{
                $tabFinal = "N/A";
            }
            $tablePhotos[] = array(
                'photo' => $photo, 
                'tableEvol' => $tabFinal,
            );
        }
        
        //On boucle sur les photos pour récupérer les licences
        $tabLicencesPhotos = [];
        //On boucle sur les photos pour récupérer les évolutions 
        $tabEvolutionsThesaurus = [];
        $indexPhoto = 1;
        $tabCommentaires = [];
        $firstPhoto = true;
        foreach($photos as $photo){
            if ($photo->getPhotoLicence()){
                $tabLicencesPhotos[$photo->getPhotoLicence()->getLicenceId()] = $photo->getPhotoLicence();
            }
            //thésaurus
            $photoThesaurus =  $repoLPhotoThesaurus->findBy(array('lPtPhoto' => $photo));
            $tabEvolution = [];
            //On boucle sur les thésaurus associés à la photo 
            foreach($photoThesaurus as $thesaurus){
                $thesaurusNom = $thesaurus->getLPtThesaurus()->getThesaurusTreeNom();
                $thesaurusId = $thesaurus->getLPtThesaurus()->getThesaurusTreeId();
                if($firstPhoto){
                    $thesaurusEvolution = $repoLThesaurusEvolution->findBy(array('lTePhotoThesaurus' => $thesaurus, 'lTeEvolution' => $evolutionPresence));
                    if($thesaurusEvolution){
                        $tabEvolution[0]['nom'] = "Présence";
                        $tabEvolution[0]['id'] = 0;
                        $tabEvolution[0]['nb'] = 1;
                        $tabEvolution[0]['listePhoto'] = $indexPhoto;
                        $tabEvolution[0]['libelleListePhoto'] = 'Photo concernée';
                    }
                }else{
                    $thesaurusEvolution = $repoLThesaurusEvolution->findBy(array('lTePhotoThesaurus' => $thesaurus));
                    if (isSet( $tabEvolutionsThesaurus[$thesaurusId])){
                        $tabEvolution = $tabEvolutionsThesaurus[$thesaurusId]['evolution'];
                    }else{
                        $tabEvolution = [];
                    }
                    //On boucle sur les évolctions associées au thésaurus de la photo
                    foreach($thesaurusEvolution as $evolution){
                        $evolutionNom = $evolution->getLTeEvolution()->getEvolutionPaysageNom();
                        $evolutionId = $evolution->getLTeEvolution()->getEvolutionPaysageId();
                        $tabEvolution[$evolutionId]['nom'] = $evolutionNom;
                        $tabEvolution[$evolutionId]['id'] = $evolutionId;
                        if(isSet($tabEvolution[$evolutionId]['nb'])){
                            $tabEvolution[$evolutionId]['nb']++;
                            $tabEvolution[$evolutionId]['listePhoto'] .= ', ' . $indexPhoto;
                            $tabEvolution[$evolutionId]['libelleListePhoto'] = 'Photos concernées';
                        }else{
                            $tabEvolution[$evolutionId]['nb'] = 1;
                            $tabEvolution[$evolutionId]['listePhoto'] = $indexPhoto;
                            $tabEvolution[$evolutionId]['libelleListePhoto'] = 'Photo concernée';
                        }
                    }
                }

                $tabEvolutionsThesaurus[$thesaurusId]['nom'] = $thesaurusNom;
                $tabEvolutionsThesaurus[$thesaurusId]['evolution'] = $tabEvolution;
            }
            
            //thésaurus Facultatif
            $photoThesaurusFacultatif =  $repoLPhotoThesaurusFacultatif->findBy(array('lPtfPhoto' => $photo));
            $tabEvolution = [];
            //On boucle sur les thésaurus facultatifs associés à la photo 
            foreach($photoThesaurusFacultatif as $thesaurusFacultatif){
                $thesaurusNom = $thesaurusFacultatif->getLPtfThesaurus()->getThesaurusTreeFacultatifNom();
                $thesaurusId = $thesaurusFacultatif->getLPtfThesaurus()->getThesaurusTreeFacultatifId();
                if($firstPhoto){
                    $thesaurusEvolution = $repoLThesaurusFacultatifEvolution->findBy(array('lTfePhotoThesaurus' => $thesaurusFacultatif, 'lTfeEvolution' => $evolutionPresence));
                    if($thesaurusEvolution){
                        $tabEvolution[0]['nom'] = "Présence";
                        $tabEvolution[0]['id'] = 0;
                        $tabEvolution[0]['nb'] = 1;
                        $tabEvolution[0]['listePhoto'] = $indexPhoto;
                        $tabEvolution[0]['libelleListePhoto'] = 'Photo concernée';
                    }
                }else{
                    $thesaurusEvolution = $repoLThesaurusFacultatifEvolution->findBy(array('lTfePhotoThesaurus' => $thesaurusFacultatif));
                    //TODO
                    if (isSet( $tabEvolutionsThesaurus['f_'.$thesaurusId])){
                        $tabEvolution = $tabEvolutionsThesaurus['f_'.$thesaurusId]['evolution'];
                    }else{
                        $tabEvolution = [];
                    }
                    //On boucle sur les évolutions associées au thésaurus de la photo
                    foreach($thesaurusEvolution as $evolution){
                        $evolutionNom = $evolution->getLTfeEvolution()->getEvolutionPaysageNom();
                        $evolutionId = $evolution->getLTfeEvolution()->getEvolutionPaysageId();
                        $tabEvolution[$evolutionId]['nom'] = $evolutionNom;
                        $tabEvolution[$evolutionId]['id'] = $evolutionId;
                        if(isSet($tabEvolution[$evolutionId]['nb'])){
                            $tabEvolution[$evolutionId]['nb']++;
                            $tabEvolution[$evolutionId]['listePhoto'] .= ', ' . $indexPhoto;
                            $tabEvolution[$evolutionId]['libelleListePhoto'] = 'Photos concernées';
                        }else{
                            $tabEvolution[$evolutionId]['nb'] = 1;
                            $tabEvolution[$evolutionId]['listePhoto'] = $indexPhoto;
                            $tabEvolution[$evolutionId]['libelleListePhoto'] = 'Photo concernée';
                        }
                    }
                }

                $tabEvolutionsThesaurus['f_'.$thesaurusId]['nom'] = $thesaurusNom;
                $tabEvolutionsThesaurus['f_'.$thesaurusId]['evolution'] = $tabEvolution;
            }

            $indexPhoto++;
            $commentaires = $em->getRepository(Commentaire::class)->findBy(array('commentairePhoto' => $photo, 'commentaireEtat' => 1), array('commentaireDate' => 'ASC'));
            foreach($commentaires as $commentaire){
                $tabCommentaires[] = $commentaire;
            }
            $firstPhoto = false;
        }

        //Récupération des Axes thématiques pour la série
        $AxeTheBySerie = $em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatSerie' => $serie));
        $tabAxeTheme = [];
        foreach($AxeTheBySerie as $axeThe){
            $tabAxeTheme[] = $axeThe->getLSatAxeThematic();
        }
        //return new JsonResponse($tabEvolutionsThesaurus);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView(
            'serie/export_serie_pdf.html.twig', 
            array(
                'serie' => $serie, 
                'photos' => $tablePhotos,
                'tabEvolutionPaysage' => $tabEvolutionPaysage,
                'tabEvolutionsThesaurus' => $tabEvolutionsThesaurus,
                'tabLicencesPhotos' => $tabLicencesPhotos,
                'AxeTheBySerie' => $AxeTheBySerie
            )
        );

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        $output = $dompdf->output();

        // In this case, we want to write the file in the public directory
        //$publicDirectory = $this->get('kernel')->getProjectDir() . '/public';
        // e.g /var/www/project/public/mypdf.pdf
        
        if (!file_exists($parameters['PATH_FOLDER_FILES'] . self::FOLDER_FICHE_SERIE)) {
            mkdir($parameters['PATH_FOLDER_FILES'] . self::FOLDER_FICHE_SERIE, 0777, true);
        }

        $datetime = new \DateTime();
        $fileName = 'export_serie_' . $serieId . '_' . $datetime->format('U') . '.pdf';
        $filePath = $parameters['PATH_FOLDER_FILES'] . self::FOLDER_FICHE_SERIE . $fileName;
        $fileUrl = $parameters['URL_FOLDER_FILES'] . self::FOLDER_FICHE_SERIE . $fileName;
       //$pdfFilepath =  $publicDirectory . '/mypdf.pdf';
        
        // Write file to the desired path
        file_put_contents($filePath, $output);
        
        if (!file_exists($parameters['PATH_FOLDER_FILES'] . self::FOLDER_EXPORT_SERIE)) {
            mkdir($parameters['PATH_FOLDER_FILES'] . self::FOLDER_EXPORT_SERIE, 0777, true);
        }
        //Déclaration de chemin
        $zipName = "export_serie_" . $datetime->format('U') . $serieId . '.zip';
        $archivePath = $parameters['PATH_FOLDER_FILES'] . self::FOLDER_EXPORT_SERIE . $zipName;

        //Archivage
        $zip = new ZipArchive();
        $zip->open($archivePath, ZipArchive::CREATE);
        
        //Ajout des photos dans l'archive
        foreach($photos as $photo){
            $photoFile = $photo->getPhotoFile();
            $fileUri = $photoFile->getFileManagerUri();
            $fileNom = $photoFile->getFileManagerNom();
            if("files/" . $fileUri){
                $zip->addFile($parameters['PATH_FOLDER_FILES'] . $fileUri, $fileNom);
            }
        }
        //Ajout du fichier XLS dans l'archive
        $zip->addFile($filePath , $fileName);
        //Fermeture du ZIP
        $zip->close();
        
        //Déclaration du lien de téléchargement
        $fileUrl = $parameters['URL_FOLDER_FILES'] . self::FOLDER_EXPORT_SERIE . $zipName;

        return new JsonResponse(array(
            'status' => 'ok', 
            'fileUrl' => $fileUrl
        ));
    }
    
    /**
     * @Route("/public/get/serie/exportold/{serieId}", name="serie_exportold")
     * @return Response
     */
    public function getExportSerieOld(int $serieId, Request $request){
        //paramètres golobaux
        $parameters = $this->get('session')->get('parameters');
        
        $em = $this->getDoctrine()->getManager();
        $serie = $em->getRepository(Serie::class)->find($serieId);
        $structureOpp = $em->getRepository(PorteurOpp::class)->find($serie->getSeriePorteurOpp());
        $opp = $serie->getSerieOpp();
        $region = $em->getRepository(Region::class)->find($serie->getSerieRegion());
        $departement = $em->getRepository(Departement::class)->find($serie->getSerieDepartement());
        $commune = $em->getRepository(Commune::class)->find($serie->getSerieCommune());
        $photo = $em->getRepository(Photo::class)->findOneBy(array('photoSerie' => $serie), array('photoDatePrise' => 'ASC'));

        //Création du document et sélection de la feuille active
        $spreadsheet = new Spreadsheet();
        $useCol = ["A", "B", "C", "D", "E", "F", "G"];
        for ($i = 0; $i <= (count($useCol)-1) ; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($useCol[$i])->setAutoSize(true);
        }

        $sheet = $spreadsheet->getActiveSheet();
        
        //Edition de cellValue
            //Premier tableau :
            $sheet->setCellValue('A1', 'Identification');
            $sheet->setCellValue('C1', 'Localisation');
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->getStyle('C1')->getFont()->setBold(true);
            
            $sheet->setCellValue('A2', 'Serie');
            $sheet->setCellValue('A3', 'Identifiant');
            $sheet->setCellValue('A4', 'Structure OPP');
            $sheet->setCellValue('A5', 'OPP');
            $sheet->setCellValue('A6', 'Photo');
            
            $sheet->setCellValue('B2', $serie->getSerieTitre());
            $sheet->setCellValue('B3', $serie->getSerieIdentifiantSerie());
            $sheet->setCellValue('B4', $structureOpp->getPorteurOppNom());
            if($opp){
                $sheet->setCellValue('B5', $opp->getOppNom());
            }
            $sheet->setCellValue('B6', $photo->getPhotoIdentifiantInt());
            
            $sheet->setCellValue('C2', 'Région');
            $sheet->setCellValue('C3', 'Département');
            $sheet->setCellValue('C4', 'Commune');
            $sheet->setCellValue('C5', 'Adresse/Lieu de la prise de vue');
            $sheet->setCellValue('C6', 'Coordonnées GPS');
            
            $sheet->setCellValue('D2', $region->getRegionNom());
            $sheet->setCellValue('D3', $departement->getDepartementNom());
            $sheet->setCellValue('D4', $commune->getCommuneNom());
            $sheet->setCellValue('D5', $serie->getSerieAdresse());
            $sheet->setCellValue('D6', $serie->getSerieGeomX() . " ; " . $serie->getSerieGeomY());
            //Fin du premier tableau

            //Second tableau :
            $sheet->setCellValue('A8', 'Changements par rapport à la photo précédente');
            $sheet->getStyle('A8')->getFont()->setBold(true);
            
            $changeDureeTab = $_POST["changeDuree"];
            $xlsRow = 8;
            for ($row = 0; $row < (count($changeDureeTab)); ++$row){
                $xlsCol = ["A", "B", "C", "D", "E", "F", "G"];
                $xlsRow = $xlsRow + 1;
                for ($cell = 0; $cell < (count($changeDureeTab[$row])); ++$cell){
                    $sheet->setCellValue($xlsCol[$cell] . $xlsRow, str_replace(' ','',$changeDureeTab[$row][$cell]));
                }
            }
            //Fin du second tableau
            
            //Troisième tableau :
            $sheet->setCellValue('A17', 'Changements intervenus sur la durée de la série');
            $sheet->getStyle('A17')->getFont()->setBold(true);
            
            if (isset($_POST["changePrec"]) == true){
                $changePhotoPrecTab = $_POST["changePrec"];
                $xlsRow = 18;
                for ($row = 0; $row < (count($changePhotoPrecTab)); ++$row){
                    $xlsCol = ["A", "B", "C", "D", "E", "F", "G"];
                    $xlsRow = $xlsRow + 1;
                    for ($cell = 0; $cell < (count($changePhotoPrecTab[$row])); ++$cell){
                        $sheet->setCellValue($xlsCol[$cell] . $xlsRow, str_replace(' ','',$changePhotoPrecTab[$row][$cell]));
                    }
                }
            }else{
                $sheet->setCellValue('A18', 'Pas de changements par rapport à la photo précédente');
            }
            //Fin troisième tableau

        //Sauvegarde    
        $idSerie = $serie->getSerieId();
        $identifiantSerie = $serie->getSerieIdentifiantSerie();
        
        $writer = new Xlsx($spreadsheet);
        $exportPath = $parameters['PATH_FOLDER_FILES'] . self::FOLDER_EXPORT_SERIE;
        //On créer le dossier si il n'existe pas
        if (!file_exists($exportPath)) {
            mkdir($exportPath, 0777, true);
        }
        $writer->save($exportPath . $identifiantSerie . '_thesaurus.xlsx');
        
        //Déclaration de chemin
        $archivePath = $parameters['PATH_FOLDER_FILES'] . self::FOLDER_EXPORT_SERIE . "export_serie" . $identifiantSerie . '.zip';

        //Archivage
        $zip = new ZipArchive();
        $zip->open($archivePath, ZipArchive::CREATE);
        
        $photos = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie));        
        $files = $em->getRepository(FileManager::class)->findAll();
        //Ajout des photos dans l'archive
        foreach($photos as $photo){
            $photoFile = $photo->getPhotoFile($files);
            $fileUri = $photoFile->getFileManagerUri();
            $fileNom = $photoFile->getFileManagerNom();
            if("files/" . $fileUri){
                $zip->addFile($parameters['PATH_FOLDER_FILES'] . $fileUri, $fileNom);
            }
        }
        //Ajout du fichier XLS dans l'archive
        $zip->addFile($exportPath . $identifiantSerie . '_thesaurus.xlsx' , $identifiantSerie . '_thesaurus.xlsx');
        //Fermeture du ZIP
        $zip->close();
        
        //Déclaration du lien de téléchargement
        $fileUrl = $parameters['URL_FOLDER_FILES'] . self::FOLDER_EXPORT_SERIE . "export_serie" . $identifiantSerie . '.zip';
        
        //Envoi des paramètres
        return new JsonResponse(array(
            'status' => 'ok',
            'fileUrl' => $fileUrl,
        ));
    }

    
    
    
    /**
     * @Route("/public/get/serie/exportChangement/{serieId}/{photoIndex}", name="serie_export_changement")
     * @return Response
     */
    public function getExportChangementPhoto(int $serieId, int $photoIndex, Request $request){
        //paramètres golobaux
        $parameters = $this->get('session')->get('parameters');
        
        $em = $this->getDoctrine()->getManager();
        $serie = $em->getRepository(Serie::class)->find($serieId);
        $structureOpp = $em->getRepository(PorteurOpp::class)->find($serie->getSeriePorteurOpp());
        $opp = $serie->getSerieOpp();
        $region = $em->getRepository(Region::class)->find($serie->getSerieRegion());
        $departement = $em->getRepository(Departement::class)->find($serie->getSerieDepartement());
        $commune = $em->getRepository(Commune::class)->find($serie->getSerieCommune());
        $photo = $em->getRepository(Photo::class)->findOneBy(array('photoSerie' => $serie), array('photoDatePrise' => 'ASC'));

        //Création du document et sélection de la feuille active
        $spreadsheet = new Spreadsheet();
        $useCol = ["A", "B", "C", "D", "E", "F", "G"];
        for ($i = 0; $i <= (count($useCol)-1) ; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($useCol[$i])->setAutoSize(true);
        }

        $sheet = $spreadsheet->getActiveSheet();
        
        //Edition de cellValue
            //Premier tableau :
            $sheet->setCellValue('A1', 'Identification');
            $sheet->setCellValue('C1', 'Localisation');
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->getStyle('C1')->getFont()->setBold(true);
            
            $sheet->setCellValue('A2', 'Serie');
            $sheet->setCellValue('A3', 'Identifiant');
            $sheet->setCellValue('A4', 'Structure OPP');
            $sheet->setCellValue('A5', 'OPP');
            $sheet->setCellValue('A6', 'Photo');
            
            $sheet->setCellValue('B2', $serie->getSerieTitre());
            $sheet->setCellValue('B3', $serie->getSerieIdentifiantSerie());
            $sheet->setCellValue('B4', $structureOpp->getPorteurOppNom());
            if($opp){
                $sheet->setCellValue('B5', $opp->getOppNom());
            }
            $sheet->setCellValue('B6', $photo->getPhotoIdentifiantInt());
            
            $sheet->setCellValue('C2', 'Région');
            $sheet->setCellValue('C3', 'Département');
            $sheet->setCellValue('C4', 'Commune');
            $sheet->setCellValue('C5', 'Adresse/Lieu de la prise de vue');
            $sheet->setCellValue('C6', 'Coordonnées GPS');
            
            $sheet->setCellValue('D2', $region->getRegionNom());
            $sheet->setCellValue('D3', $departement->getDepartementNom());
            $sheet->setCellValue('D4', $commune->getCommuneNom());
            $sheet->setCellValue('D5', $serie->getSerieAdresse());
            $sheet->setCellValue('D6', $serie->getSerieGeomX() . " ; " . $serie->getSerieGeomY());
            //Fin du premier tableau

            //Second tableau :
            $sheet->setCellValue('A8', 'Changements par rapport à la photo précédente');
            $sheet->getStyle('A8')->getFont()->setBold(true);
            
            $xlsRow = 8;
            if (isset($_POST["changePrec"]) == true){
                $changePhotoPrecTab = $_POST["changePrec"];
                for ($row = 0; $row < (count($changePhotoPrecTab)); ++$row){
                    $xlsCol = ["A", "B", "C", "D", "E", "F", "G", "H"];
                    $xlsRow = $xlsRow + 1;
                    for ($cell = 0; $cell < (count($changePhotoPrecTab[$row])); ++$cell){
                        $sheet->setCellValue($xlsCol[$cell] . $xlsRow, str_replace(' ','',$changePhotoPrecTab[$row][$cell]));
                        if($row == 0){
                            $sheet->getStyle($xlsCol[$cell].$xlsRow)->getFont()->setBold(true);
                        }
                    }
                }
            }else{
                $xlsRow++;
                $sheet->setCellValue('A'.$xlsRow, 'Pas de changements par rapport à la photo précédente');
            }
            //Fin du second tableau
            
            $xlsRow++;
            $xlsRow++;
            //Troisième tableau :
            $sheet->setCellValue('A'.$xlsRow, 'Changements intervenus sur la durée de la série');
            $sheet->getStyle('A'.$xlsRow)->getFont()->setBold(true);
            
            $changeDureeTab = $_POST["changeDuree"];
            //$xlsRow = 18;
            for ($row = 0; $row < (count($changeDureeTab)); ++$row){
                $xlsCol = ["A", "B", "C", "D", "E", "F", "G", "H"];
                $xlsRow = $xlsRow + 1;
                for ($cell = 0; $cell < (count($changeDureeTab[$row])); ++$cell){
                    $sheet->setCellValue($xlsCol[$cell] . $xlsRow, str_replace(' ','',$changeDureeTab[$row][$cell]));
                    if($row == 0){
                        $sheet->getStyle($xlsCol[$cell].$xlsRow)->getFont()->setBold(true);
                    }
                }
            }
            //Fin troisième tableau

        //Sauvegarde    
        $identifiantSerie = $serie->getSerieIdentifiantSerie();
        
        $writer = new Xlsx($spreadsheet);
        $exportPath = $parameters['PATH_FOLDER_FILES'] . self::FOLDER_EXPORT_SERIE;
        //On créer le dossier si il n'existe pas
        if (!file_exists($exportPath)) {
            mkdir($exportPath, 0777, true);
        }
        $filePath = $exportPath . trim($identifiantSerie) . '_' . $photoIndex . '_changement.xlsx';
        $writer->save($filePath);
        $fileUrl =  $parameters['URL_FOLDER_FILES'] . self::FOLDER_EXPORT_SERIE . trim($identifiantSerie) . '_' . $photoIndex . '_changement.xlsx';
        
        //Envoi des paramètres
        return new JsonResponse(array(
            'status' => 'ok',
            'fileUrl' => $fileUrl,
        ));
    }
    

    /**
     * @Route("/get/geojson/famillesPaysages", name="get_geojson_famillesPaysages")
     * @return JsonResponse
     */
    public function getGeojsonFamillesPaysages()
    {
        //paramètres golobaux
        $parameters = $this->get('session')->get('parameters');

        $row = 1;
        if (($handle = fopen($parameters['PATH_FOLDER_FILES'] . "/carto/ensembles_familles_paysages_out.json", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                echo "<p> $num champs à la ligne $row: <br /></p>\n";
                $row++;
                for ($c=0; $c < $num; $c++) {
                    echo $data[$c] . "<br />\n";
                }
            }
            fclose($handle);
        }
        return new JsonResponse(array('ok'));
/*
        $em = $this->getDoctrine()->getManager();
        $critereSearchSeries =  $this->get('session')->get('critere_search_series');
        $series = $critereSearchSeries['series'];
        
        //print_r($critereSearchSeries);
        $features = [];
        foreach($series as $serie){
            $typeOpp = "local";
            //Récupération du type de porteurOpp
            //$porteurOpp = $em->getRepository(PorteurOpp::class)->findAll();
            //$seriePorteur = $serie->getSeriePorteurOpp($porteurOpp);
            
            if ($serie->getSerieOpp()){
                if ($serie->getSerieOpp()->getOppParticipative() == false){
                    $typeOpp = "local";
                }else{
                    $typeOpp = "participatif";
                }
            }

            $photos = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie), array('photoDatePrise' => 'ASC' ));
            $photoSerie = [];

            $limit=10;
            $i=0;
            foreach($photos as $photo){
                $i++;
                if($photo->getPhotoFile()){
                    $fileUri = $photo->getPhotoFile()->getFileManagerUri();
                }else{
                    $fileUri = "";
                }
                $idPhoto = $photo->getPhotoId();
                $anneePhoto = $photo->getPhotoDatePrise()->format('d/m/Y');
                array_push($photoSerie, ['id' => $idPhoto, 'date' => $anneePhoto, 'url' => $fileUri]);
                if ($i >= $limit){
                    break;
                }
            };    
            $feature = array(
                "type"       => "Feature", 
                "geometry"   => array(
                    "type"        => "Point",
                    "coordinates" => [$serie->getSerieGeomX(), $serie->getSerieGeomY()],
                ),
                "properties" => array(
                    "id"          => $serie->getSerieId(),
                    "title"       => $serie->getSerieTitre(),
                    "photosSerie" => $photoSerie,
                    "type"        => $typeOpp,
                    "commune"     => $serie->getSerieCommune()->getCommuneInsee() . " " . $serie->getSerieCommune()->getCommuneNom(),
                )
            );
            $features[] = $feature;
        }
        $geoJson["type"] = "FeatureCollection";
        $geoJson["test"] = "test";
        $geoJson["features"] = $features;
        return new JsonResponse($geoJson);*/
    }

    /**
     * @Route("/get/serie/newId", name="get_serie_newid")
     * @return JsonResponse
     */
    public function getSerieNewId(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rsm = new ResultSetMapping();
        if($request->get('opp_id') == ""){
            return new JsonResponse(['newid' => '001']);
        }
        $rsm->addScalarResult('newid', 'newid');
        $query = $em->createNativeQuery('SELECT public.get_next_identifiant_interne_serie(?) as newid', $rsm);
        $query->setParameter(1, $request->get('opp_id'));

        //print_r ($query->getSQL());
        return new JsonResponse($query->getResult()[0]);
    }

    /**
     * @Route("/get/photo/newId", name="get_photo_newid")
     * @return JsonResponse
     */
    public function getPhotoNewId(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rsm = new ResultSetMapping();
        if($request->get('serie_id') == ""){
            return new JsonResponse(['newid' => '001']);
        }
        $rsm->addScalarResult('newid', 'newid');
        $query = $em->createNativeQuery('SELECT public.get_next_identifiant_interne_photo(?) as newid', $rsm);
        $query->setParameter(1, $request->get('serie_id'));

        //print_r ($query->getSQL());
        return new JsonResponse($query->getResult()[0]);
    }

    /**
     * @Route("/test2", name="test2")
     * @return JsonResponse
     */
    public function test2()
    {
        $datetime = \DateTime::createFromFormat("U", date("U"));
        
        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository(FileManager::class)->findAll();
        $file[0]->setFileManagerDate(date("U"));

        $em->persist($file[0]);
        $em->flush();


        //print_r ($query->getSQL());
        return new JsonResponse([
            "date" => date("U"),
            "datetime" => $datetime
        ]);
    }

}
