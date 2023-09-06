<?php

namespace App\Controller;

use App\Entity\Langue;
use App\Entity\Commune;
use App\Entity\Departement;
use App\Entity\Region;
use App\Entity\Pays;
use App\Entity\Opp;
use App\Entity\TypologiePaysage;
use App\Entity\AxeThematic;
use App\Entity\PorteurOpp;
use App\Entity\EnsemblePaysager;
use App\Entity\UnitePaysage;
use App\Entity\UnitePaysageLocale;
use App\Entity\Format;
use App\Entity\Licence;
use App\Entity\LSerieAxeThematic;
use App\Entity\LPhotoThesaurus;
use App\Entity\LThesaurusEvolution;
use App\Entity\EvolutionPaysage;

use App\Entity\Serie;
use App\Entity\Photo;
use App\Entity\FileManager;
use App\Entity\LienExterne;
use App\Entity\LSerieUnitePaysagereLocale;

use App\Repository;

use ZipArchive;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportController extends Controller
{
    const FOLDER_IMPORT_SERIE = 'import';
    const FOLDER_PHOTO = 'photos';
    const FOLDER_MINIATURE = 'miniature';
    
    /**
     * @Route("gestion/import", name="showImport")
     * @return Response
     */
    public function showImport()
    {
        $em = $this->getDoctrine()->getManager();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        return $this->render("serie/import.html.twig", 
        ['structures' => $structures,
        'nbWaitingComments' => $nbWaitingComments]);
    }       
    
    /**
     * @Route("gestion/import/photos", name="importSeriePhotos")
     * @return Response
     */
    public function importSeriePhotos()
    {
        //On récupère les paramètres globaux
        $parameters = $this->get('session')->get('parameters');
        if (isset($_FILES['file']) AND $_FILES['file']['error'] == 0)
        {
            // Test si le fichier n'est pas trop gros
            if ($_FILES['file']['size'] <= 2000000000)
            {
                // Test si l'extension est autorisée
                $infosfichier = pathinfo($_FILES['file']['name']);
                $extension_upload = strtolower($infosfichier['extension']);
                $extensions_autorisees = array('zip');
                $newPath = $parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_IMPORT_SERIE . '/';
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
                    //move_uploaded_file($_FILES['file']['tmp_name'], $newPath . basename($_FILES['file']['name']));
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
            'archiveName' => $fname,
            'archiveSize' => $_FILES['file']['size'],
            'archiveStatut' => $_FILES['file']['error'],
            'archiveFormat' => $_FILES['file']['type'],
            'archiveURI' => $parameters['URL_FOLDER_FILES'] . '/' . self::FOLDER_IMPORT_SERIE . '/' . $fname,
            'archivePath' => self::FOLDER_IMPORT_SERIE . '/' . $fname,
            'archiveDate' => date("Y-m-d H:i:s"),
            //'archiveType' => $archiveType
        ));
    }
    
    /**
     * @Route("gestion/import/table", name="importSerieTable")
     * @return Response
     */
    public function importSerieTable()
    {
        //On récupère les paramètres globaux
        $parameters = $this->get('session')->get('parameters');
        if (isset($_FILES['file']) AND $_FILES['file']['error'] == 0)
        {
            // Test si le fichier n'est pas trop gros
            if ($_FILES['file']['size'] <= 2000000000)
            {
                // Test si l'extension est autorisée
                $infosfichier = pathinfo($_FILES['file']['name']);
                $extension_upload = strtolower($infosfichier['extension']);
                $extensions_autorisees = array('csv');
                $newPath = $parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_IMPORT_SERIE . '/';
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
                    //move_uploaded_file($_FILES['file']['tmp_name'], $newPath . basename($_FILES['file']['name']));
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
            'csvName' => $fname,
            'csvSize' => $_FILES['file']['size'],
            'csvStatut' => $_FILES['file']['error'],
            'csvFormat' => $_FILES['file']['type'],
            'csvURI' => $parameters['URL_FOLDER_FILES'] . '/' . self::FOLDER_IMPORT_SERIE . '/' . $fname,
            'csvPath' => self::FOLDER_IMPORT_SERIE . '/' . $fname,
            'csvDate' => date("Y-m-d H:i:s"),
        ));
    }
    
    /**
     * @Route("gestion/import/serie", name="importSerie")
     * @return Response
     */
    public function importSerie(Request $request, TranslatorInterface $translator)
    {
        //paramètres globaux
        $parameters = $this->get('session')->get('parameters');
        
        //Récupération des variables d'envoi de l'appel AJAX
        $zipArchivePhoto = $request->request->get('zip');
        $csvSerie = $request->request->get('csv');
        
        //Déclaration des URL de téléchargement
        $zipName = $zipArchivePhoto["zipName"];
        $csvName = $csvSerie["csvName"];
        
    //Traitement du ZIP    
        //Ouverture de l'archive ZIP
        $zip = new ZipArchive;
        $zipPath = $parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_IMPORT_SERIE . '/' . $zipName;
        $res = $zip->open($zipPath, ZipArchive::CHECKCONS);

    //Traitement du CSV
        //Définition des paramètres de lecture du fichier CSV
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $reader->setInputEncoding('CP1252');
        $reader->setDelimiter(';');
        $reader->setEnclosure('');
        $reader->setSheetIndex(0);

        //Chargement du fichier à lire
        $csvPath = $parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_IMPORT_SERIE . '/' . $csvName;
        $spreadsheet = $reader->load($csvPath);
        $sheetData   = $spreadsheet->getActiveSheet()->toArray();
        //var_dump($sheetData[1][0]);
        
        //Récupération des informations de la série dans l'array serieData
        $serieData = [];
        for ($i=0; $i < count($sheetData[1]); $i++) {
            array_push($serieData, $sheetData[1][$i]);
        }
        //var_dump($serieData);
        
        //Récupération des informations des photos
        $photosData = [];
        $nbPhotos = count($sheetData);
        for ($r=4; $r < $nbPhotos; $r++) {
            //Pour que l'utilisateur ait bien pensé à supprimer les indications de répétition de l'exemple
            if($sheetData[$r][0] !== '(répéter ad libitum)'){
                $photoDataRow = [];
                for ($c=0; $c < count($sheetData[$r]); $c++) {
                    array_push($photoDataRow, $sheetData[$r][$c]);
                }
                array_push($photosData,$photoDataRow);
            }
        }
        //var_dump($photosData);
        
    //Insertion des séries en base 
        //Appelle de Doctrine et des classes nécessaires
        $em = $this->getDoctrine()->getManager();
        $serie = new Serie();

        //TITRE
        $titre = $serieData[0];
        if($titre == ""){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Le champ titre " . $translator->trans('labelArtAndSerie') . " est obligatoire"
            ));
        }
        $serie->setSerieTitre($titre);
        
        //OPP
        $oppId = $serieData[1];
        if($oppId == ""){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Le champ OPP " . $translator->trans('labelArtAndSerie') . " est obligatoire"
            ));
        }
        $opp = $em->getRepository(Opp::class)->find($oppId);
        if(!isSet($opp)){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Le champ OPP " . $translator->trans('labelArtAndSerie') . " ne fait référence à aucun OPP"
            ));
        }
        $serie->setSerieOpp($opp);
        
        //Axe thématique
        $axeThemId = $serieData[2];
        $axeThem = $em->getRepository(AxeThematic::class)->find($axeThemId);

        //Si le champ est obligatoire
        if(in_array("AxeThematique", explode(";", $translator->trans('fieldRequired')) )){
            if($axeThemId == ""){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ axe thématique " . $translator->trans('labelArtAndSerie') . " est obligatoire"
                ));
            }
            
            if(!isSet($axeThem)){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ axe thématique " . $translator->trans('labelArtAndSerie') . " ne fait référence à aucun axe thématique"
                ));
            }
        }
        if(isSet($axeThem)){
            $lSerieAxeThematic = new LSerieAxeThematic();
            $lSerieAxeThematic->setLSatAxeThematic($axeThem);
            $lSerieAxeThematic->setLSatSerie($serie);
            $em->persist($lSerieAxeThematic);
        }
        
        //Typologie de paysage
        $typologiePaysageId = $serieData[3];
        
        //Si le champ est obligatoire
        if($typologiePaysageId == ""){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Le champ " . $translator->trans('labelTypologiePaysage') . " " . $translator->trans('labelArtAndSerie') . " est obligatoire"
            ));
        }
        $typologiePaysage = $em->getRepository(TypologiePaysage::class)->find($typologiePaysageId);
        if(!isSet($typologiePaysage)){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Le champ " . $translator->trans('labelTypologiePaysage') . " " . $translator->trans('labelArtAndSerie') . " ne fait référence à aucun " . $translator->trans('labelTypologiePaysage')
            ));
        }
        $serie->setSerieTypologie($typologiePaysage);

        //INTENTION
        $intention = $serieData[4];
        if($intention == ""){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Le champ intention du photographe " . $translator->trans('labelArtAndSerie') . " est obligatoire"
            ));
        }
        $serie->setSerieIntention($intention);

        //Description
        $desc = $serieData[5];
        
        //Si le champ est obligatoire
        if(in_array("descFine", explode(";", $translator->trans('fieldRequired')) )){
            if($desc == ""){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ description du photographe " . $translator->trans('labelArtAndSerie') . " est obligatoire"
                ));
            }
        }
        $serie->setSerieDesc($desc);

        //Date
        $date = $serieData[6];
        if($date == ""){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Le champ date " . $translator->trans('labelArtAndSerie') . " est obligatoire"
            ));
        }
        if (!preg_match('#^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$#', $date)){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Le champ date " . $translator->trans('labelArtAndSerie') . " est mal formaté : " . $date . ". Le format doit être dd/mm/yyyy"
            ));
        }
        $serieDate = \DateTime::createFromFormat("j/m/Y", $date);
        $serie->setSerieDate($serieDate);

        //Identifiant interne
        $idInt = $serieData[7];
        $serie->setSerieIdentifiantInt($idInt);
        
        //Departement
        $depId = $serieData[8];
        if($depId != ""){
            $departement = $em->getRepository(Departement::class)->find($depId);
            if(!isSet($departement)){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ departement " . $translator->trans('labelArtAndSerie') . " ne fait référence à aucun departement"
                ));
            }
            $serie->setSerieDepartement($departement);
        }
        
        //Commune
        $communeId = $serieData[9];
        if($communeId == ""){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Le champ commune " . $translator->trans('labelArtAndSerie') . " est obligatoire"
            ));
        }
        $commune = $em->getRepository(Commune::class)->find($communeId);
        if(!isSet($commune)){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Le champ commune " . $translator->trans('labelArtAndSerie') . " ne fait référence à aucune commune"
            ));
        }
        $serie->setSerieCommune($commune);

        //Adresse
        $adresse = $serieData[10];
        $serie->setSerieAdresse($adresse);

        //Champ geom
        $geomX = $serieData[11];
        $geomY = $serieData[12];
        if($geomX == "" || $geomY == ""){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Les champs longitude/latitude " . $translator->trans('labelArtAndSerie') . " sont obligatoires"
            ));
        }
        //A tester
        if(!is_float($geomX + 0) || !is_float($geomY + 0)){
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur : Les champs longitude/latitude " . $translator->trans('labelArtAndSerie') . " doivent être des numériques"
            ));
        }
        $serie->setSerieGeomX($geomX);
        $serie->setSerieGeomY($geomY);
        
        //Ensemble paysagé
        $ensemblePaysagerId = $serieData[13];
        $ensemblePaysager = $em->getRepository(EnsemblePaysager::class)->find($ensemblePaysagerId);
        
        //Si le champ est obligatoire
        if(in_array("EnsemblePaysager", explode(";", $translator->trans('fieldRequired')) )){
            if($ensemblePaysagerId == ""){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ " . $translator->trans('labelEnsemblePaysager') . " " . $translator->trans('labelArtAndSerie') . " est obligatoire"
                ));
            }
            
            if(!isSet($ensemblePaysager)){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ " . $translator->trans('labelEnsemblePaysager') . " " . $translator->trans('labelArtAndSerie') . " ne fait référence à aucun " . $translator->trans('labelEnsemblePaysager') 
                ));
            }
        }
        if(isSet($ensemblePaysager)){
            $serie->setSerieEnsemblePaysage($ensemblePaysager);
        }
        
        //Unité paysagère
        $unitePaysagereId = $serieData[14];
        $unitePaysagere = $em->getRepository(UnitePaysage::class)->find($unitePaysagereId);
        
        //Si le champ est obligatoire
        if(in_array("UnitePaysage", explode(";", $translator->trans('fieldRequired')) )){
            if($unitePaysagereId == ""){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ unite de paysage " . $translator->trans('labelArtAndSerie') . " est obligatoire"
                ));
            }
            if(!isSet($unitePaysagere)){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ unite de paysage " . $translator->trans('labelArtAndSerie') . " ne fait référence à aucune unite de paysage"
                ));
            }
        }
        if(isSet($unitePaysagere)){
            $serie->setSerieUnitePaysagere($unitePaysagere);
        }

        //Fréquence et période
        $serie->setSerieFreqInterval($serieData[16]);
        $serie->setSerieFreqPeriod($serieData[17]);

        //On enregistre la serie
        $em->persist($serie);
        //$em->flush();
        
        
        //Après avoir ouvert le ZIP on le traite ici puis on déplace le fichier si c'est bon
        if ($res === TRUE){
            $nbPhoto = $zip->numFiles;

            $archiveFilesNames = [];
            for($i=0; $i<$nbPhoto; $i++){
                $fileName = $zip->getNameIndex($i);
                $fileInfo = pathinfo($fileName);
                //var_dump($zipPath);
                array_push($archiveFilesNames, basename($fileName));
                /*if (!file_exists("zip://" . $zipPath . "#" . $fileName)){
                    return new JsonResponse(array(
                        "status" => "erreur",
                        'message' => "Erreur : La copie du fichier $fileName a échoué, les photos doivent être à la racine du zip sans dossier."
                    ));
                }*/
                if (!copy("zip://" . $zipPath . "#" . $fileName, $parameters['PATH_FOLDER_FILES'] . '/' . $fileInfo['basename'])){
                    return new JsonResponse(array(
                        "status" => "erreur",
                        'message' => "Erreur : La copie du fichier $fileName a échoué, les photos doivent être à la racine du zip sans dossier"
                    ));
                }

//                copy($fileName, $parameters['PATH_FOLDER_FILES'] . '/');
//                $zip->extractTo($parameters['PATH_FOLDER_FILES'] . '/' , $fileName);

            }

            //On extrait tous les fichiers qui sont dans l'archive vers les /files/
            //$zip->close();
//            return new JsonResponse(array(
//                "status" => "ok",
//            ));
        }else{
            return new JsonResponse(array(
                "status" => "erreur",
            ));
        }
        
        //Insertion des photos en base + gestion des fichiers photos
        foreach($photosData as $photoData){
            //On vérifie si chaque fichier matche bien avec le CSV
            $photoFileName = $photoData[0];
            if($photoFileName == ""){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ nom du fichier des photos est obligatoire"
                ));
            }
            if (!in_array($photoFileName, $archiveFilesNames)){
                //Le nom des photos répertoriées dans le fichier CSV n'est pas le même que celui présent dans l'archive ZIP
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le nom des photos répertoriées dans le fichier CSV n'est pas le même que celui présent dans l'archive ZIP."
                ));
            }
                
            $photo = new Photo();

            $photo->setPhotoTitre($photoFileName);

            $auteur = $photoData[1];
            if($auteur == ""){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ auteur des photos est obligatoire"
                ));
            }
            $photo->setPhotoAuteur($auteur);
            $photo->setPhotoDescChangement($photoData[2]);

            //Date
            $date = $photoData[3];
            if($date == ""){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ date des photos est obligatoire"
                ));
            }
            if (!preg_match('#^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$#', $date)){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ date des photos est mal formaté : " . $date . ". Le format doit être dd/mm/yyyy"
                ));
            }
            $photoDate = \DateTime::createFromFormat("j/m/Y", $date);
            $photo->setPhotoDatePrise($photoDate);
        
            //Format
            $formatId = $photoData[4];
            if($formatId != ""){
                $format = $em->getRepository(Format::class)->find($formatId);
                if(!isSet($format)){
                    return new JsonResponse(array(
                        'status' => "erreur",
                        'message' => "Erreur : Le champ format des photo ne fait référence à aucun format"
                    ));
                }
                $photo->setPhotoFormat($format);
            }
            
            if($photoData[5] != ""){
                $photo->setPhotoIdentifiantInt($photoData[5]);
            }
        
            //Licence
            $licenceId = $photoData[6];
            if($licenceId == ""){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ licence des photos est obligatoire"
                ));
            }
            $licence = $em->getRepository(Licence::class)->find($licenceId);
            if(!isSet($licence)){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : Le champ licence des photos ne fait référence à aucune licence"
                ));
            }
            $photo->setPhotoLicence($licence);

            //Le champ heure est maintenant formaté hh:mm
            if($photoData[7] != "" ){
                if (preg_match('/^([01]?[0-9]|2[0-3])\:+[0-5][0-9]$/', $photoData[7])){
                    $photo->setPhotoHeure($photoData[7]);
                }else{
                    //Les photos répertoriées dans le fichier CSV ne sont pas les même que celles présente dans l'archive ZIP
                    return new JsonResponse(array(
                        'status' => "erreur",
                        'message' => "Erreur : un champ heure est mal formaté : " . $photoData[7] . ". Le format doit être hh:mm"
                    ));
                }
            }
            
            $photo->setPhotoTypeAppareil($photoData[8]);
            
            $focale = $photoData[9];
            if($focale != ""){
                if(!is_numeric($focale)){
                    return new JsonResponse(array(
                        'status' => "erreur",
                        'message' => "Erreur : Le champs focale des photos doit être un numérique " . $focale 
                    ));
                }
                $photo->setPhotoFocale($focale);
            }

            $photo->setPhotoOuvertureDia($photoData[10]);
            $photo->setPhotoTypeFilm($photoData[12]);
            $photo->setPhotoIso($photoData[13]);
            $photo->setPhotoPoidsOrigine($photoData[14]);
            
            $inclinaison = $photoData[15];
            if($inclinaison != ""){
                $photo->setPhotoInclinaison($inclinaison);
            }
            
            $hauteur = $photoData[16];
            if($hauteur != ""){
                if(!is_numeric($hauteur )){
                    return new JsonResponse(array(
                        'status' => "erreur",
                        'message' => "Erreur : Le champs hauteur des photos doit être un numérique"
                    ));
                }
                $photo->setPhotoHauteur($hauteur);
            }
            
            $orientation = $photoData[17];
            if($orientation != ""){
                if(!is_numeric($orientation)){
                    return new JsonResponse(array(
                        'status' => "erreur",
                        'message' => "Erreur : Le champs orientation des photos doit être un numérique"
                    ));
                }
                $photo->setPhotoOrientation($orientation);
            }
            
            $altitude = $photoData[18];
            if($altitude != ""){
                if(!is_numeric($altitude)){
                    return new JsonResponse(array(
                        'status' => "erreur",
                        'message' => "Erreur : Le champs altitude des photos doit être un numérique"
                    ));
                }
                $photo->setPhotoAltitude($altitude);
            }
            
            $coefMaree = $photoData[19];
            if($coefMaree != ""){
                if(!is_numeric($coefMaree)){
                    return new JsonResponse(array(
                        'status' => "erreur",
                        'message' => "Erreur : Le champs coeff. de marée des photos doit être un numérique"
                    ));
                }
                $photo->setPhotoCoefMaree($coefMaree);
            }

            //Gestion des fichiers photo
            $newPhoto = new FileManager();

            $info = pathinfo($photoFileName);
            $file_name =  basename($photoFileName,'.'.$info['extension']);
            $newPhoto->setFileManagerNom($file_name);
            $newPhoto->setFileManagerUri("/" . $photoFileName);
            $newPhoto->setFileManagerMime("image/" . pathinfo($photoFileName, PATHINFO_EXTENSION));
            $newPhoto->setFileManagerStatut("1");
            $newPhoto->setFileManagerSize(filesize($parameters['PATH_FOLDER_FILES'] . "/" . $photoFileName));
            $newPhoto->setFileManagerDate(filemtime($parameters['PATH_FOLDER_FILES'] . "/" . $photoFileName));
            
            //TODO GERER LES MINIATURES
            list($width, $height, $type, $attr) = getimagesize($parameters['PATH_FOLDER_FILES'] . "/" . $photoFileName);
            $heightMiniature = 85;
            $coefMiniature = 85/$height;
            $widthMiniature = $coefMiniature*$width;
            
            if (!file_exists($parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_MINIATURE . '/' )) {
                mkdir($parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_MINIATURE . '/' , 0777, true);
            }
            $this->resize($widthMiniature, $parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_MINIATURE . '/'  . $photoFileName, $parameters['PATH_FOLDER_FILES'] . "/" . $photoFileName);
        

            $photo->setPhotoFile($newPhoto);
            $em->persist($newPhoto);

            $photo->setPhotoSerie($serie);
            $em->persist($photo);
                
            /*}else{
                //Les photos répertoriées dans le fichier CSV ne sont pas les même que celles présente dans l'archive ZIP
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur : l'archive ZIP et le fichier CSV ne correspondent pas."
                ));
            }*/
        }
        $zip->close();
        $em->flush();

        return new JsonResponse(array(
            "status" => "ok",
        ));
    }       
    
    /**
     * @Route("gestion/generation/fichierPreparation", name="generateFichierPreparation")
     * @return Response
     */
    public function generateFichierPreparation(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = $this->get('session')->get('parameters');
        $departements = $em->getRepository(Departement::class)->findAll();
        $ensemblePaysagers = $em->getRepository(EnsemblePaysager::class)->findAll();
        $unitePaysages = $em->getRepository(UnitePaysage::class)->findAll();
        $formats = $em->getRepository(Format::class)->findAll();
        $licences = $em->getRepository(Licence::class)->findAll();
        $opps = $em->getRepository(Opp::class)->findAll();
        $axeThematics = $em->getRepository(AxeThematic::class)->findAll();
        $communes = $em->getRepository(Commune::class)->findAll();
        $typologiePaysages = $em->getRepository(TypologiePaysage::class)->findAll();
        $unitePaysageLocales = $em->getRepository(UnitePaysageLocale::class)->findAll();
        
        $fileName = "export_preparation.csv";
        $response = new StreamedResponse();
 
 
        $response->setCallback(function() use ($translator, $departements, $ensemblePaysagers, $unitePaysages, $formats, $licences, $opps, $axeThematics, $communes, $typologiePaysages, $unitePaysageLocales){
            $handle = fopen('php://output', 'w+');
 
            // Nom des colonnes du CSV
            fputcsv($handle, array('Type de champs',
                mb_convert_encoding ('ID à reporter sur le fichier d\'import', 'ISO-8859-1', 'UTF-8'),
                mb_convert_encoding ('Libellé','ISO-8859-1', 'UTF-8')
            ), ';');
 
            //Champs
            foreach ($departements as $departement)
            {
                fputcsv($handle,array(
                    mb_convert_encoding ('Département', 'ISO-8859-1', 'UTF-8'),
                    $departement->getDepartementId(), 
                    mb_convert_encoding ($departement->getDepartementNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');
            }
 
            //Champs,
            foreach ($ensemblePaysagers as $ensemblePaysager)
            {
                /*fputcsv($handle,array(
                    mb_convert_encoding ('labelEnsemblePaysager', 'ISO-8859-1', 'UTF-8'),
                    $ensemblePaysager->getEnsemblePaysagerId(), 
                    mb_convert_encoding ($ensemblePaysager->getEnsemblePaysagerNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');*/
                fputcsv($handle,array(
                    mb_convert_encoding(ucfirst($translator->trans('labelEnsemblePaysager')), 'ISO-8859-1', 'UTF-8'),
                    $ensemblePaysager->getEnsemblePaysagerId(), 
                    mb_convert_encoding ($ensemblePaysager->getEnsemblePaysagerNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');
            }
 
            //Champs
            foreach ($unitePaysages as $unitePaysage)
            {
                fputcsv($handle,array(
                    mb_convert_encoding("Unité de paysage", 'ISO-8859-1', 'UTF-8'),
                    $unitePaysage->getUnitePaysageId(), 
                    mb_convert_encoding ($unitePaysage->getUnitePaysageNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');
            }
 
            //Champs
            foreach ($formats as $format)
            {
                fputcsv($handle,array(
                    mb_convert_encoding ('Format', 'ISO-8859-1', 'UTF-8'),
                    $format->getFormatId(), 
                    mb_convert_encoding ($format->getFormatNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');
            }
 
            //Licence
            foreach ($licences as $licence)
            {
                fputcsv($handle,array(
                    mb_convert_encoding ('Licence', 'ISO-8859-1', 'UTF-8'),
                    $licence->getLicenceId(), 
                    mb_convert_encoding ($licence->getLicenceNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');
            }
 
            //Opp
            foreach ($opps as $opp)
            {
                fputcsv($handle,array(
                    mb_convert_encoding ('Opp', 'ISO-8859-1', 'UTF-8'),
                    $opp->getOppId(), 
                    mb_convert_encoding ($opp->getOppNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');
            }
 
            //Axe thématique
            foreach ($axeThematics as $axeThematic)
            {
                fputcsv($handle,array(
                    mb_convert_encoding ('Axe thématique', 'ISO-8859-1', 'UTF-8'),
                    $axeThematic->getAxeThematicId(), 
                    mb_convert_encoding ($axeThematic->getAxeThematicNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');
            }
 
            //Commune
            foreach ($communes as $commune)
            {
                fputcsv($handle,array(
                    mb_convert_encoding ('Commune', 'ISO-8859-1', 'UTF-8'),
                    $commune->getCommuneId(), 
                    mb_convert_encoding ($commune->getCommuneNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');
            }
 
            //Typologie de Paysage
            foreach ($typologiePaysages as $typologiePaysage)
            {
                fputcsv($handle,array(
                    mb_convert_encoding(ucfirst($translator->trans('labelTypologiePaysage')), 'ISO-8859-1', 'UTF-8'),
                    $typologiePaysage->getTypologiePaysageId(), 
                    mb_convert_encoding ($typologiePaysage->getTypologiePaysageNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');
            }
 
            //UnitePaysageLocale
            foreach ($unitePaysageLocales as $unitePaysageLocale)
            {
                fputcsv($handle,array(
                    mb_convert_encoding(ucfirst($translator->trans('labelUnitePaysageLocale')), 'ISO-8859-1', 'UTF-8'),
                    $unitePaysageLocale->getUnitePaysageLocaleId(), 
                    mb_convert_encoding ($unitePaysageLocale->getUnitePaysageLocaleNom(), 'ISO-8859-1', 'UTF-8'),
                ),';');
            }

            fclose($handle);
        });
 
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename='.$fileName);
 
        
        return $response;

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
}
