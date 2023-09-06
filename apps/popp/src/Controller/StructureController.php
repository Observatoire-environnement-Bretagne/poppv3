<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Document;
use App\Entity\DocumentAnnexe;
use App\Entity\PorteurOpp;
use App\Entity\FileManager;
use App\Entity\LDocumentAnnexeOpp;
use App\Entity\LFournisseurOpp;
use App\Entity\LGestionnaireOpp;
use App\Entity\LienExterne;
use App\Entity\LPhotoThesaurus;
use App\Entity\LPhotoThesaurusFacultatif;
use App\Entity\LSerieAxeThematic;
use App\Entity\LSerieUnitePaysagereLocale;
use App\Entity\LThesaurusEvolution;
use App\Entity\LThesaurusFacultatifEvolution;
use App\Entity\Opp;
use App\Entity\Photo;
use App\Entity\Serie;
use App\Entity\Son;
use App\Repository;
use Proxies\__CG__\App\Entity\Photo as EntityPhoto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class StructureController extends Controller
{
    const FOLDER_LOGO_STRUCTURE = 'logo_structure';
    const FOLDER_UPLOAD_FILE = 'upload';
    
    /**
     * @Route("show/structures", name="showStructures")
     * @return Response
     */
    public function showStructures()
    {
        // Chargement des services
        $ParametreDAO = $this->get('parametre.dao');
        //A déplacer au lancement de l'application
        $ParametreDAO->setGlobalParamaters();
        
        $em = $this->getDoctrine()->getManager();
        $structures = $em->getRepository(PorteurOpp::class)->findBy([], ['porteurOppNom' => 'ASC']);
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("public/structures.html.twig", [
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    /**
     * @Route("show/structure/{structureId}", name="showStructure")
     * @return Response
     */
    public function showStructure(string $structureId)
    {
        $em = $this->getDoctrine()->getManager();
        $structure = $em->getRepository(PorteurOpp::class)->find($structureId);
        $fileManager = $em->getRepository(FileManager::class)->findAll();
        $opps = $em->getRepository(Opp::class)->findBy(array('oppPorteurOpp' => $structure));

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        return $this->render("public/show_structure.html.twig", [
            'structure' => $structure,
            'fileManager' => $fileManager,
            'structures' => $structures,
            'opps' => $opps,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
            
    /**
     * @Route("update/structure/{structureId}", name="updateStruct")
     * @return Response
     */
    public function updateStruct(string $structureId)
    {
        $em = $this->getDoctrine()->getManager();

        $structureId = $_POST['structureId'];
        
        $structure = $em->getRepository(PorteurOpp::class)->find($structureId);
        $structure->setPorteurOppNom($_POST['structureNom']);
        $structure->setPorteurOppDescCourte($_POST['structureDescCourte']);
        $structure->setPorteurOppAdresse($_POST['structureAdresse']);
        $structure->setPorteurOppContactRef($_POST['structureContactRef']);
        $structure->setPorteurOppEmail($_POST['structureEmail']);
        $structure->setPorteurOppTelephone($_POST['structureTelephone']);
        $structure->setPorteurOppSiteWeb($_POST['structureSiteWeb']);

        $em->persist($structure);
        $em->flush();      
        
        return new JsonResponse(array(
            'structureId' => $structureId,
            'structure'   => $structure,
            'status' => "ok"
        ));
    }
    
    /**
     * @Route("admin/create/structures", name="createStructure")
     * @return Response
     */
    public function createStructure()
    {
        $em = $this->getDoctrine()->getManager();

        $structure = new PorteurOpp();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        return $this->render("structure/create_structure.html.twig", [
            'structure' => $structure,
            'action' => 'new',
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }

    /**
     * @Route("admin/update/structures/{structureId}", name="updateStructure")
     * @return Response
     */
    public function updateStructure(string $structureId)
    {
        $em = $this->getDoctrine()->getManager();
        $structure = $em->getRepository(PorteurOpp::class)->find($structureId);
        //$structureLogoId = $structure->getPorteurOppLogo();

        //$fileManager = $em->getRepository(FileManager::class)->find($structureLogoId);
        //$fileManagerUri = $fileManager->getFileManagerUri();
        
        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        
        return $this->render("structure/create_structure.html.twig", [
            'structure' => $structure,
            'action' => 'update',
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }        
        
    /**
     * @Route("admin/get/structures", name="getStructure")
     * @return Response
     */
    public function getStructure()
    {
        $em = $this->getDoctrine()->getManager();
        $structures = $em->getRepository(PorteurOpp::class)->findAll();
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("structure/structures.html.twig", [
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
     
    /**
     * @Route("admin/remove/structure/{structureId}", name="deleteStructure")
     * @return Response
     */
    public function deleteStructure(string $structureId)
    {
        $fileManagerDAO = $this->get('filemanager.dao');
        $serieDAO = $this->get('serie.dao');
        $em = $this->getDoctrine()->getManager();
        $structure = $em->getRepository(PorteurOpp::class)->find($structureId);
        $opps = $em->getRepository(Opp::class)->findBy(array('oppPorteurOpp' => $structure));
        foreach($opps as $opp){
            //Suppression des documents annexes
            //Ici, on est pas censé y passer
            $documentsAnnexe = $em->getRepository(DocumentAnnexe::class)->findBy(array('documentAnnexeOpp' => $opp));
            foreach($documentsAnnexe as $documentAnnexe){
                $documentAnnexe->setDocumentAnnexeOpp(null);
                $em->persist($documentAnnexe);
            }
            $lDocumentAnnexeOpps = $em->getRepository(LDocumentAnnexeOpp::class)->findBy(array('lDaoOpp' => $opp));
            foreach($lDocumentAnnexeOpps as $lDocumentAnnexeOpp){
                $docAnnexe = $lDocumentAnnexeOpp->getLDaoDocumentAnnexe();
                $em->remove($lDocumentAnnexeOpp);

                //On vérifie si le document est encore rattaché à un OPP, sinon on le supprime
                $lDocumentAnnexeOppFromDoc = $em->getRepository(LDocumentAnnexeOpp::class)->findBy(array('lDaoDocumentAnnexe' => $docAnnexe));
                if(!$lDocumentAnnexeOppFromDoc){
                    $file = $docAnnexe->documentAnnexeFile();
                    $em->remove($docAnnexe);
                    $fileManagerDAO->removeFile($file);
                }
            }

            //On retire les opp des users
            $lFournisseurOpps = $em->getRepository(LFournisseurOpp::class)->findBy(array('lFoOpp' => $opp));
            foreach($lFournisseurOpps as $lFournisseurOpp){
                $em->remove($lFournisseurOpp);
            }
            $lGestionnaireOpps = $em->getRepository(LGestionnaireOpp::class)->findBy(array('lGoOpp' => $opp));
            foreach($lGestionnaireOpps as $lGestionnaireOpp){
                $em->remove($lGestionnaireOpp);
            }

            //suppression des series
            $series = $em->getRepository(Serie::class)->findBy(array('serieOpp' => $opp));
            foreach($series as $serie){
                $serieDAO->removeSerie($serie);
            }
            $em->remove($opp);

        }
        $em->flush();   
        $series = $em->getRepository(Serie::class)->findBy(array('seriePorteurOpp' => $structure));
        foreach($series as $serie){
            $serieDAO->removeSerie($serie);
        }

        $sons = $em->getRepository(Son::class)->findBy(array('sonStructResp' => $structure));
        foreach($sons as $son){
            $em->remove($son);
        }

        $em->remove($structure);
        $em->flush($structure);
        return new JsonResponse(array('status' => 'ok'));
    }    
        
    /**
     * @Route("admin/structure/insertStructure", name="insertStructure")
     * @return Response
     */
    public function insertStructure(Request $request)
    { 
        $em = $this->getDoctrine()->getManager(); //on appelle Doctrine
        //Récupération du FileManagerDAO
        $fileManagerDAO = $this->get('filemanager.dao');
        //paramètres globaux
        $parameters = $this->get('session')->get('parameters');
        //Déclaration des variables
        $structureId = $request->request->get('add_struct_id');
        //Date du jour
        //$datetime = \DateTime::createFromFormat("U", date("Y-m-d H:i:s"));

        //Récupération de l'objet porteur OPP
        if ($structureId == 'new'){
            $structureOpp = new PorteurOpp();
        }else{
            $structureOpp = $em->getRepository(PorteurOpp::class)->find($structureId);
            if(!isSet($structureOpp)){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur lors de la récupération de la structure"
                ));
            }
        }
        
        //gestion du logo
        $structureLogoId = $request->request->get('structureLogoId');
        
        //On gère 2 cas, la suppression du logo (delete) et l'ajout ou modif (new)
        if($structureLogoId == 'delete'){
            //gestion de la suppression du logo
            $logo = $structureOpp->getPorteurOppLogo();
            if ($logo){
                $structureOpp->setPorteurOppLogo(null);
                $fileManagerDAO->removeFile($logo);
            }
        }
        if($structureLogoId == 'new'){
            //On vérifie s'il y avait un logo avant
            $logo = $structureOpp->getPorteurOppLogo();
            if ($logo){
                $structureOpp->setPorteurOppLogo(null);
                //si c'est le cas, on le supprime physiquement
                $fileManagerDAO->removeFile($logo);
            }
            //création du logo
            $nomLogo = $request->request->get('structureLogoTitre');

            $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_LOGO_STRUCTURE . "/";
            $pathUpload = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_UPLOAD_FILE . "/";
            if (!file_exists($pathFile)) {
                mkdir($pathFile, 0777, true);
            }
                                
            //On renomme si le fichier existe
            $fname = $nomLogo;
            $rawBaseName = pathinfo($pathUpload . $nomLogo, PATHINFO_FILENAME );
            $extension_upload = pathinfo($pathUpload . $nomLogo, PATHINFO_EXTENSION  );
            $counter = 0;
            
            while(file_exists($pathFile . $nomLogo)) {
                $nomLogo = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                $counter++;
            };

            // Valider le fichier et le stocker d�finitivement
            if(!rename($pathUpload . $fname, $pathFile . $nomLogo)){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur lors de la copie du logo"
                ));
            }


            $newLogo = new FileManager();
            $newLogo->setFileManagerNom($request->request->get('structureLogoTitre'));
            $newLogo->setFileManagerUri("/" . self::FOLDER_LOGO_STRUCTURE . "/" . $nomLogo);
            //$newLogo->setFileManagerMime($request->request->get('structureLogoTitre'));
            //$newLogo->setFileManagerStatut($request->request->get('structureLogoTitre'));
            $newLogo->setFileManagerSize($request->request->get('structureLogoPoids'));
            $newLogo->setFileManagerDate(date("U"));

            $em->persist($newLogo);
            $em->flush();

            $structureOpp->setPorteurOppLogo($newLogo);
        }

        $structureOpp->setPorteurOppNom($request->request->get('add_struct_nom'));
        $structureOpp->setPorteurOppDescCourte($request->request->get('add_struct_desc_courte'));
        $structureOpp->setPorteurOppAdresse($request->request->get('add_struct_adrs'));
        $structureOpp->setPorteurOppContactRef($request->request->get('add_struct_contact'));
        $structureOpp->setPorteurOppDescTech($request->request->get('add_struct_desc_tech'));
        $structureOpp->setPorteurOppEmail($request->request->get('add_struct_email'));
        $structureOpp->setPorteurOppTelephone($request->request->get('add_struct_tel'));
        $structureOpp->setPorteurOppSiteWeb($request->request->get('add_struct_url'));
        $structureOpp->setPorteurOppPreocupationPaysagere($request->request->get('add_struct_preoc_pays'));
        if($request->request->get('add_struct_financeur') == "true"){
            $structureOpp->setPorteurOppFinanceur(true);
        }else{
            $structureOpp->setPorteurOppFinanceur(false);
        }

        $em->persist($structureOpp);
        $em->flush();

        return new JsonResponse(array(
            'status' => "ok",
            'structureId' => $structureOpp->getPorteurOppId()
        ));
    }
    
    /*TODO - A supprimer*/
    /**
     * @Route("admin/structure/insertLogo", name="insertLogo")
     * @return Response
     */
    public function insertLogo(){
        // Test si le fichier a bien �t� envoy� et s'il n'y a pas d'erreur
        $parameters = $this->get('session')->get('parameters');
        if (isset($_FILES['file']) AND $_FILES['file']['error'] == 0)
        {
            // Test si le fichier n'est pas trop gros
            if ($_FILES['file']['size'] <= 256000000)
            {
                // Test si l'extension est autoris�e
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
                    //move_uploaded_file($_FILES['file']['tmp_name'], $newPath . basename($_FILES['file']['name']));
                    //echo "L'envoi a bien �t� effectu� !";
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
            'logoName' => $fname,
            'logoSize' => $_FILES['file']['size'],
            'logoStatut' => $_FILES['file']['error'],
            'logoFormat' => $_FILES['file']['type'],
            'logoPath' => $newPath . $fname,
            'logoURI' => $parameters['URL_FOLDER_FILES'] . '/' . self::FOLDER_UPLOAD_FILE . '/' . $fname,
            'logoDate' => date("Y-m-d H:i:s"),
            'logoType' => "logo_structure",
        ));
    }
      
    /**
     * @Route("admin/remove/structure/logo/{logoId}", name="removeLogo")
     * @return Response
     */
    public function removeLogo(string $logoId){
        $em = $this->getDoctrine()->getManager();

        //Récupération de l'URL
        $logo = $em->getRepository(FileManager::class)->find($logoId);
        if ($logo){
            $logoURI = $logo->getFileManagerUri();
            
            //Suppression du fichier
            unlink($logoURI);
            
            //Suppression de la clé secondaire du FileManager
            $repoPorteurOpp = $em->getRepository(PorteurOpp::class);
            $structureOpp = $repoPorteurOpp->findOneBy(array('structureOppLogo' => $logo));
            $structureOpp->setPorteurOppLogo(null);
            $em->persist($structureOpp);
            //$em->flush();
                    
            //Suppression dans le file Manager
            $em->remove($logo);
            $em->flush();
        }
        return new JsonResponse(array('status' => 'ok'));
    }
    
    /**
     * @Route("admin/modify/structure/{structureId}", name="modifyStructure")
     * @return Response
     */
    public function modifyStructure(string $structureId)
    {
        $em = $this->getDoctrine()->getManager();
        if($structureId == 'new'){
            $structure = new PorteurOpp();
        }else{
            $structure = $em->getRepository(PorteurOpp::class)->find($structureId);
        }
        $structure->setPorteurOppNom($_POST['structureName']);
        $structure->setPorteurOppDescCourte($_POST['structureDesc']);
        $structure->setPorteurOppAdresse($_POST['structureAdrs']);
        $structure->setPorteurOppContactRef($_POST['structureContactRef']);
        $structure->setPorteurOppDescTech($_POST['structureDescTech']);
        $structure->setPorteurOppEmail($_POST['structureEmail']);
        $structure->setPorteurOppTelephone($_POST['structureTel']);
        $structure->setPorteurOppSiteWeb($_POST['structureSiteWeb']);
        $structure->setPorteurOppPreocupationPaysagere($_POST['structurePreocPaysagere']);
        
        $tabStructure = [];
        $tabStructure['nom'] = $_POST['structureName'];
        /*$structure->setPorteurOppDescCourte($_POST['structureDesc']);
        $structure->setPorteurOppAdresse($_POST['structureAdrs']);
        $structure->setPorteurOppContactRef($_POST['structureContactRef']);
        $structure->setPorteurOppDescTech($_POST['structureDescTech']);
        $structure->setPorteurOppEmail($_POST['structureEmail']);
        $structure->setPorteurOppTelephone($_POST['structureTel']);
        $structure->setPorteurOppSiteWeb($_POST['structureSiteWeb']);
        $structure->setPorteurOppPreocupationPaysagere($_POST['structurePreocPaysagere']);*/
        
        return new JsonResponse($tabStructure);
    }

    public function deleteSerie($serie){
        $fileManagerDAO = $this->get('filemanager.dao');
        $em = $this->getDoctrine()->getManager();
        $documents = $em->getRepository(Document::class)->findBy(array('documentSerie' => $serie));
        foreach($documents as $document){
            $file = $document->getDocumentFile();
            $em->remove($document);
            $fileManagerDAO->removeFile($file);
        }
        
        $lienExternes = $em->getRepository(LienExterne::class)->findBy(array('lienExterneSerie' => $serie));
        foreach($lienExternes as $lienExterne){
            $em->remove($lienExterne);
        }
        
        $lSerieAxeThematics = $em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatSerie' => $serie));
        foreach($lSerieAxeThematics as $lSerieAxeThematic){
            $em->remove($lSerieAxeThematic);
        }
        
        
        $LSerieUnitePaysagereLocales = $em->getRepository(LSerieUnitePaysagereLocale::class)->findBy(array('lSuplSerie' => $serie));
        foreach($LSerieUnitePaysagereLocales as $LSerieUnitePaysagereLocale){
            $em->remove($LSerieUnitePaysagereLocale);
        }
        
        $Sons = $em->getRepository(Son::class)->findBy(array('sonSerie' => $serie));
        foreach($Sons as $Son){
            $em->remove($Son);
        }
        
        $Photos = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie));
        foreach($Photos as $Photo){
            $Commentaires = $em->getRepository(Commentaire::class)->findBy(array('commentairePhoto' => $Photo));
            foreach($Commentaires as $Commentaire){
                $em->remove($Commentaire);
            }
            
            $LPhotoThesauruss = $em->getRepository(LPhotoThesaurus::class)->findBy(array('lPtPhoto' => $Photo));
            foreach($LPhotoThesauruss as $LPhotoThesaurus){
                $LThesaurusEvolutions = $em->getRepository(LThesaurusEvolution::class)->findBy(array('lTePhotoThesaurus' => $Photo));
                foreach($LThesaurusEvolutions as $LThesaurusEvolution){
                    $em->remove($LThesaurusEvolution);
                }
                $em->remove($LPhotoThesaurus);
            }
            
            $LPhotoThesaurusFacultatifs = $em->getRepository(LPhotoThesaurusFacultatif::class)->findBy(array('lPtfPhoto' => $Photo));
            foreach($LPhotoThesaurusFacultatifs as $LPhotoThesaurusFacultatif){
                $LThesaurusFacultatifEvolutions = $em->getRepository(LThesaurusFacultatifEvolution::class)->findBy(array('lTfePhotoThesaurus' => $Photo));
                foreach($LThesaurusFacultatifEvolutions as $LThesaurusFacultatifEvolution){
                    $em->remove($LThesaurusFacultatifEvolution);
                }
                $em->remove($LPhotoThesaurusFacultatif);
            }
            $file = $Photo->getPhotoFile();
            $em->remove($Photo);
            $fileManagerDAO->removeFile($file);
        }

        $fileCroquis = $serie->getSerieCroquis();
        $filePhotoAerienne = $serie->getSeriePhotoAerienne();
        $filePhotoContext = $serie->getSeriePhotoContext();
        $filePhotoIgn = $serie->getSeriePhotoIgn();
        $filePhotoTrepied = $serie->getSeriePhotoTrepied();
        $fileDocumentRef = $serie->getSerieRefdoc();

        $em->remove($serie);
        $fileManagerDAO->removeFile($fileCroquis);
        $fileManagerDAO->removeFile($filePhotoAerienne);
        $fileManagerDAO->removeFile($filePhotoContext);
        $fileManagerDAO->removeFile($filePhotoIgn);
        $fileManagerDAO->removeFile($filePhotoTrepied);
        $fileManagerDAO->removeFile($fileDocumentRef);

        $em->flush();
    }
}
