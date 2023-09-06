<?php

namespace App\Controller;

use App\Entity\PorteurOpp;
use App\Entity\Ressource;
use App\Entity\FileManager;
use App\Entity\LRessourceFileManager;

use App\Repository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class RessourceController extends Controller
{
    const FOLDER_LOGO_STRUCTURE = 'logo_ressource';
    const FOLDER_DOCUMENT_RESSOURCE = 'ressource';
    const FOLDER_UPLOAD_FILE = 'upload';
    
    /**
     * @Route("show/ressources", name="showRessources")
     * @return Response
     */
    public function showRessources()
    {
        // Chargement des services
        $ParametreDAO = $this->get('parametre.dao');
        //A déplacer au lancement de l'application
        $ParametreDAO->setGlobalParamaters();
        
        $em = $this->getDoctrine()->getManager();
        $ressources = $em->getRepository(Ressource::class)->findBy([], ['ressourceNumOrdre' => 'ASC']);

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("public/ressources.html.twig", [
            'ressources' => $ressources,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    /**
     * @Route("show/ressource/{ressourceId}", name="showRessource")
     * @return Response
     */
    public function showRessource(string $ressourceId)
    {
        $em = $this->getDoctrine()->getManager();
        $ressource = $em->getRepository(Ressource::class)->find($ressourceId);

        
        //Récupération des files pour la ressource
        $FileByRessource = $em->getRepository(LRessourceFileManager::class)->findBy(array('lRefmRessource' => $ressource));
        /*$tabFileManager = [];
        foreach($FileByRessource as $file){
            $tabFileManager[] = $file->getLRefmFileManager();
        }*/
        
        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        
        return $this->render("public/show_ressource.html.twig", [
            'ressource' => $ressource,
            'files'     => $FileByRessource,
            'structures'     => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    /**
     * @Route("admin/update/ressource/{ressourceId}", name="updateRessource")
     * @return Response
     */
    public function updateRessource(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = $this->get('session')->get('parameters'); 

        $ressourceId = $request->request->get('ressourceId');
        
        $ressource = $em->getRepository(Ressource::class)->find($ressourceId);
        $ressource->setRessourceTitre($request->request->get('ressourceTitre'));
        $ressource->setRessourceQuestion($request->request->get('ressourceQuestion'));
        $ressource->setRessourceDesc($request->request->get('ressourceDesc'));
        
        $ressourceNumOrdre = $request->request->get('ressourceNumOrdre');
        if($ressourceNumOrdre == ""){
            $ressourceNumOrdre = 0;
        }
        $ressource->setRessourceNumOrdre($ressourceNumOrdre);
        
        //gérer directement à l'ajout du document
        /*$Files = json_decode($request->request->get("ressourceDocs"));
        $files = $Files->documents;

        foreach($files as $file){
            //Insertion du nouveau fichier :
            if ($file->ressourceDocId == 'new'){
                $fileId = $file->ressourceDocId;
                $fileOldId = $file->ressourceDocOldId;
                $fileName = $file->ressourceDocName;
                //$fileUri = $file->ressourceDocURI;
                //$fileFormat = $file->ressourceDocFormat;
                //$fileStatut = $file->ressourceDocStatut;
                $fileSize = $file->ressourceDocSize;
                $fileDate = $file->ressourceDocDate;
                
                $newFile = new FileManager();
                $newFile->setFileManagerNom($fileName);
                $newFile->setFileManagerUri("/" . $fileName);
                //$newFile->setFileManagerMime($fileFormat);
                //$newFile->setFileManagerMime($fileFormat);
                $newFile->setFileManagerSize($fileSize);
                //$newFile->setFileManagerStatut($fileStatut);
                $newFile->setFileManagerDate($fileDate);
                $em->persist($newFile);

                if ($fileOldId == "new"){
                    $newReFm = new LRessourceFileManager();
                }else{
                    $newReFm = $em->getRepository(LRessourceFileManager::class)->findOneBy(array('lRefmRessource' => $ressourceId));
                }
                $newReFm->setLRefmRessource($ressource);
                $newReFm->setLRefmFileManager($newFile);
                $em->persist($newReFm);
            }else{
                if($file->ressourceDocUrl == ""){
                    //si le titre du fichier a été modifié
                    $fileManager = $em->getRepository(FileManager::class)->find($file->ressourceDocId);
                    $fileManager->setFileManagerNom($file->ressourceDocName);
                    $em->persist($fileManager);
                }
            }
        }*/
        
        $Logos = json_decode($request->request->get("ressourceLogo"));
        $logos = $Logos->documents;

        foreach($logos as $logo){
            //Insertion du nouveau fichier :
            if ($logo->ressourceLogoId == 'new'){
                $logoId = $logo->ressourceLogoId;
                $logoName = $logo->ressourceLogoName;
                //$fileUri = $logo->ressourceDocURI;
                //$fileFormat = $logo->ressourceDocFormat;
                //$fileStatut = $logo->ressourceDocStatut;
                $logoSize = $logo->ressourceLogoSize;
                $logoDate = $logo->ressourceLogoDate;
        
                $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_DOCUMENT_RESSOURCE . "/";
                $pathUpload = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_UPLOAD_FILE . "/";
                if (!file_exists($pathFile)) {
                    mkdir($pathFile, 0777, true);
                }
                                
                //On renomme si le fichier existe
                $fname = $logoName;
                $rawBaseName = pathinfo($pathUpload . $logoName, PATHINFO_FILENAME );
                $extension_upload = pathinfo($pathUpload . $logoName, PATHINFO_EXTENSION  );
                $counter = 0;
                
                while(file_exists($pathFile . $logoName)) {
                    $logoName = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                    $counter++;
                };
                
                // Valider le fichier et le stocker définitivement
                if(!rename($pathUpload . $fname, $pathFile . $logoName)){
                    return new JsonResponse(array(
                        'status' => "erreur",
                        'message' => "Erreur lors de la copie du document"
                    ));
                }
                
                $newFile = new FileManager();
                $newFile->setFileManagerNom($logoName);
                $newFile->setFileManagerUri("/" . self::FOLDER_DOCUMENT_RESSOURCE . "/" . $logoName);
                //$newFile->setFileManagerMime($fileFormat);
                //$newFile->setFileManagerMime($fileFormat);
                $newFile->setFileManagerSize($logoSize);
                //$newFile->setFileManagerStatut($fileStatut);
                $newFile->setFileManagerDate($logoDate);
                $em->persist($newFile);
                
                $ressource->setRessourceLogo($newFile);
            }
        }
        $em->persist($ressource);
        $em->flush();      
        
        return new JsonResponse(array(
            '$ressourceId' => $ressourceId,
            'ressource'   => $ressource,
            'status' => "ok"
        ));
    }
    
    /**
     * @Route("admin/create/ressource", name="createRessource")
     * @return Response
     */
    public function createRessource(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $ressource = new Ressource();
        $parameters = $this->get('session')->get('parameters');  

        $ressource->setRessourceTitre($request->request->get('ressourceTitre'));
        $ressource->setRessourceQuestion($request->request->get('ressourceQuestion'));
        $ressource->setRessourceDesc($request->request->get('ressourceDesc'));
        
        $ressourceNumOrdre = $request->request->get('ressourceNumOrdre');
        if($ressourceNumOrdre == ""){
            $ressourceNumOrdre = 0;
        }
        $ressource->setRessourceNumOrdre($ressourceNumOrdre);
        
        $file = json_decode($request->request->get("ressourceDocs"));
        $File = $file->documents;
        
        if (!empty($File)){
            $fileId = $File[0]->ressourceDocId;
            $fileName = $File[0]->ressourceDocName;
            $fileUri = $File[0]->ressourceDocURI;
            //$fileFormat = $File[0]->ressourceDocFormat;
            //$fileStatut = $File[0]->ressourceDocStatut;
            $fileSize = $File[0]->ressourceDocSize;
            $fileDate = $File[0]->ressourceDocDate;
        
            $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_DOCUMENT_RESSOURCE . "/";
            $pathUpload = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_UPLOAD_FILE . "/";
            if (!file_exists($pathFile)) {
                mkdir($pathFile, 0777, true);
            }
                                
            //On renomme si le fichier existe
            $fname = $fileName;
            $rawBaseName = pathinfo($pathUpload . $fileName, PATHINFO_FILENAME );
            $extension_upload = pathinfo($pathUpload . $fileName, PATHINFO_EXTENSION  );
            $counter = 0;
            
            while(file_exists($pathFile . $fileName)) {
                $fileName = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                $counter++;
            };

            // Valider le fichier et le stocker définitivement
            if(!rename($pathUpload . $fname, $pathFile . $fileName)){
                return new JsonResponse(array(
                    'status' => "erreur",
                    'message' => "Erreur lors de la copie du document"
                ));
            }
        //Insertion du nouveau fichier :
            $newFile = new FileManager();
            $newFile->setFileManagerNom($fileName);
            $newFile->setFileManagerUri("/" . self::FOLDER_DOCUMENT_RESSOURCE . "/" . $fileName);
            //$newFile->setFileManagerMime($fileFormat);
            $newFile->setFileManagerSize($fileSize);
            //$newFile->setFileManagerStatut($fileStatut);
            $newFile->setFileManagerDate(date("U"));
            $em->persist($newFile);

            //Insertion du nouveau fichier :
            $newReFm = new LRessourceFileManager();
            $newReFm->setLRefmRessource($ressource);
            $newReFm->setLRefmFileManager($newFile);
            $newReFm->setNomFichier($request->request->get('ressourceTitreDoc'));
            $em->persist($newReFm);
        }
        
        $Logos = json_decode($request->request->get("ressourceLogo"));
        $logos = $Logos->documents;

        foreach($logos as $logo){
            //Insertion du nouveau fichier :
            if ($logo->ressourceLogoId == 'new'){
                $logoId = $logo->ressourceLogoId;
                $logoName = $logo->ressourceLogoName;
                //$fileUri = $logo->ressourceDocURI;
                //$fileFormat = $logo->ressourceDocFormat;
                //$fileStatut = $logo->ressourceDocStatut;
                $logoSize = $logo->ressourceLogoSize;
                $logoDate = $logo->ressourceLogoDate;
        
                $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_DOCUMENT_RESSOURCE . "/";
                $pathUpload = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_UPLOAD_FILE . "/";
                if (!file_exists($pathFile)) {
                    mkdir($pathFile, 0777, true);
                }
                                
                //On renomme si le fichier existe
                $fname = $logoName;
                $rawBaseName = pathinfo($pathUpload . $logoName, PATHINFO_FILENAME );
                $extension_upload = pathinfo($pathUpload . $logoName, PATHINFO_EXTENSION  );
                $counter = 0;
                
                while(file_exists($pathFile . $logoName)) {
                    $logoName = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                    $counter++;
                };

                // Valider le fichier et le stocker définitivement
                if(!rename($pathUpload . $fname, $pathFile . $logoName)){
                    return new JsonResponse(array(
                        'status' => "erreur",
                        'message' => "Erreur lors de la copie du document"
                    ));
                }
                
                $newFile = new FileManager();
                $newFile->setFileManagerNom($logoName);
                $newFile->setFileManagerUri("/" . self::FOLDER_DOCUMENT_RESSOURCE . "/" . $logoName);
                //$newFile->setFileManagerMime($fileFormat);
                //$newFile->setFileManagerMime($fileFormat);
                $newFile->setFileManagerSize($logoSize);
                //$newFile->setFileManagerStatut($fileStatut);
                $newFile->setFileManagerDate($logoDate);
                $em->persist($newFile);
                
                $ressource->setRessourceLogo($newFile);
            }
        }
        
        $em->persist($ressource);
        $em->flush();      
        
        return  new JsonResponse(array(
            'status' => "ok",
            'ressource' => $ressource
        ));
    }
    
        
    /**
     * @Route("admin/remove/ressource/{ressourceId}", name="deleteRessource")
     * @return Response
     */
    public function deleteRessource(string $ressourceId)
    {
        $em = $this->getDoctrine()->getManager();
        $fileManagerDAO = $this->get('filemanager.dao');
        
        $ressource = $em->getRepository(Ressource::class)->find($ressourceId);
        
        $files = $em->getRepository(LRessourceFileManager::class)->findBy(array('lRefmRessource' => $ressourceId));
        foreach($files as $file){       
            $fileManager = $em->getRepository(FileManager::class)->find($file->getLRefmFileManager());
            
            $em->remove($file);
            
            $fileManagerDAO->removeFile($fileManager); 
            $em->remove($fileManager);
        }
        
        $em->remove($ressource);
        
        $em->flush($ressource);
       
        return  new JsonResponse(array(
            'status' => "ok"
        ));
    }
    
    /**
     * @Route("admin/remove/ressource/document/{docId}", name="removeDocRessource")
     * @return Response
     */
    public function removeDocRessource(int $docId){
        $em = $this->getDoctrine()->getManager();
        $fileManagerDAO = $this->get('filemanager.dao');

        $lRessourceFileManager = $em->getRepository(LRessourceFileManager::class)->find($docId);
        if ($lRessourceFileManager){
            //Suppression de la clé secondaire du FileManager
            $doc = $lRessourceFileManager->getLRefmFileManager();
            $em->remove($lRessourceFileManager);

            //Suppression dans le file Manager
            $fileManagerDAO->removeFile($doc);   
            $em->remove($doc);
            $em->flush();
        }

/*
        //Récupération de l'URL
        $doc = $em->getRepository(FileManager::class)->find($docId);
        if ($doc){
            //$docURI = $doc->getFileManagerUri();
            
            //Suppression du fichier
            //$parameters = $this->get('session')->get('parameters');   
            //unlink($parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_UPLOAD_FILE . $docURI);
            
            //Suppression de la clé secondaire du FileManager
            $repoRessourceFileManager = $em->getRepository(LRessourceFileManager::class);
            $Refm = $repoRessourceFileManager->findOneBy(array('lRefmFileManager' => $doc));
            $em->remove($Refm);

            //Suppression dans le file Manager
            $fileManagerDAO->removeFile($doc);   
            $em->remove($doc);
            $em->flush();
        }*/
        return new JsonResponse(array('status' => 'ok'));
    }
    
    /**
     * @Route("admin/ressource/insertLogo", name="insertRessourceLogo")
     * @return Response
     */
    public function insertRessourceLogo(){
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
                $extensions_autorisees = array('png', 'gif', 'jpg', 'jpeg', 'tif');
                $newPath = $parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_UPLOAD_FILE . '/';
                //On créer le dossier si il n'existe pas
                if (!file_exists($newPath)) {
                    mkdir($newPath, 0777, true);
                }
                if (in_array($extension_upload, $extensions_autorisees))
                {
                    // Valider le fichier et le stocker définitivement
                    move_uploaded_file($_FILES['file']['tmp_name'], $newPath . basename($_FILES['file']['name']));
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
            'fileName' => $_FILES['file']['name'],
            'fileSize' => $_FILES['file']['size'],
            'fileStatut' => $_FILES['file']['error'],
            'fileFormat' => $_FILES['file']['type'],
            'fileURI' => $parameters['URL_FOLDER_FILES'] . '/' . self::FOLDER_UPLOAD_FILE . '/' . basename($_FILES['file']['name']),
            'filePath' => self::FOLDER_UPLOAD_FILE . '/' . basename($_FILES['file']['name']),
            'fileDate' => date("Y-m-d H:i:s"),
        ));
    }

    /**
     * @Route("admin/modify/ressource/document", name="modifyDocumentRessource")
     * @return Response
     */
    public function modifyDocumentRessource(Request $request)
    {
        $fileManagerDAO = $this->get('filemanager.dao');
        $parameters = $this->get('session')->get('parameters');
        $documentId = $request->request->get('document_ressource_id');
        $ressourceId = $request->request->get('ressource_id');
        
        $em = $this->getDoctrine()->getManager();
        if($documentId == 'new'){
            $documentRessource = new LRessourceFileManager();
        }else{
            $documentRessource = $em->getRepository(LRessourceFileManager::class)->find($documentId);
        }
        $documentRessource->setNomFichier($request->request->get('document_ressource_titre'));

        $ressource = $em->getRepository(Ressource::class)->find($ressourceId);
        
        //Gestion des fichiers son
        switch($request->request->get('document_ressource_file_action')){
            case 'new' :
                //création du fichier
                $nomFile = $request->request->get('document_ressource_file_name');

                $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_DOCUMENT_RESSOURCE . "/";
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
                $newDocumentRessource = new FileManager();
                
                $newDocumentRessource->setFileManagerNom($nomFile);
                $newDocumentRessource->setFileManagerUri("/" . self::FOLDER_DOCUMENT_RESSOURCE . "/" . $nomFile);
                $newDocumentRessource->setFileManagerSize($request->request->get('document_ressource_file_size'));
                $newDocumentRessource->setFileManagerDate(date("U"));

                $em->persist($newDocumentRessource);
                $em->flush();

                $oldDocumentRessource = $documentRessource->getLRefmFileManager();
                
                $documentRessource->setLRefmFileManager($newDocumentRessource);
                $documentRessource->setLRefmRessource($ressource);

                //si un document existe, on la supprime 
                if($oldDocumentRessource){
                    $fileManagerDAO->removeFile($oldDocumentRessource);
                }

            break;
        }
        
        $em->persist($documentRessource);
        $em->flush();
        return new JsonResponse(array(
            'status' => 'ok', 
            'documentRessourceId' => $documentRessource->getLRefmId(), 
            'documentRessourceFileUrl' => $parameters['URL_FOLDER_FILES'] . $documentRessource->getLRefmFileManager()->getFileManagerUri()
        ));
    }
    
 }
