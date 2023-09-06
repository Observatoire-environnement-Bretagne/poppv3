<?php

namespace App\Controller;

use App\Entity\Faq;
use App\Entity\FileManager;

use App\Entity\LFaqFileManager;
use App\Entity\PorteurOpp;
use App\Repository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class FaqController extends Controller
{
    const FOLDER_UPLOAD_FILE = 'upload';
    const FOLDER_DOCUMENT_FAQ = 'faq';

    /**
     * @Route("show/faqs", name="showFaqs")
     * @return Response
     */
    public function showFaqs()
    {
        // Chargement des services
        $ParametreDAO = $this->get('parametre.dao');
        //A déplacer au lancement de l'application
        $ParametreDAO->setGlobalParamaters();
        
        $em = $this->getDoctrine()->getManager();
        //$faqs = $em->getRepository(Faq::class)->findAll();
        $faqs = $em->getRepository(Faq::class)->findby(array(), array('faqNumOrdre' => 'ASC'));

        //Récupération des files pour la ressource
        $FileByFaq = $em->getRepository(LFaqFileManager::class)->findAll();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        return $this->render("public/faq.html.twig", [
            'faqs' => $faqs,
            'files'=> $FileByFaq,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    /**
     * @Route("show/faq/{faqId}", name="showFaq")
     * @return Response
     */
    public function showFaq(string $faqId)
    {
        $em = $this->getDoctrine()->getManager();
        $faq = $em->getRepository(Faq::class)->find($faqId);
        
        //Récupération des files pour la ressource
        $FileByFaq = $em->getRepository(LFaqFileManager::class)->findBy(array('lFafmFaq' => $faq));
        /*$tabFileManager = [];
        foreach($FileByFaq as $file){
            $tabFileManager[] = $file->getLFafmFileManager();
        }*/

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        
        return $this->render("public/show_faq.html.twig", [
            'faq'  => $faq,
            'files'=> $FileByFaq,
            'structures'=> $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    /**
     * @Route("admin/update/faq/{faqId}", name="updateFaq")
     * @return Response
     */
    public function updateFaq(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = $this->get('session')->get('parameters'); 

        $faqId = $request->request->get('faqId');
        
        $faq = $em->getRepository(Faq::class)->find($faqId);
        $faq->setFaqTitre($request->request->get('faqTitre'));
        $faq->setFaqHeader($request->request->get('faqHeader'));
        $faq->setFaqReponse($request->request->get('faqReponse'));
        $faq->setFaqQuestion($request->request->get('faqQuestion'));
        $faq->setFaqDate(\DateTime::createFromFormat("j/m/Y", $request->request->get('faqDate')));
        
        $faqNumOrdre = $request->request->get('faqNumOrdre');
        if($faqNumOrdre == ""){
            $faqNumOrdre = 0;
        }
        $faq->setFaqNumOrdre($faqNumOrdre); 
        
        $file = json_decode($request->request->get("faqDoc"));
        $File = $file->documents;

        if (!empty($File)){
            if ($File[0]->faqDocId == "new"){
                //$fileId = $File[0]->faqDocId;
                $fileName  = $File[0]->faqDocName;
                //$fileUri = $File[0]->faqDocURI;
                //$fileFormat = $File[0]->faqDocFormat;
                //$fileStatut = $File[0]->faqDocStatut;
                $fileSize = $File[0]->faqDocSize;
                $fileDate = $File[0]->faqDocDate;
                $fileDocName = $File[0]->faqDocFileName;
                
                $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_DOCUMENT_FAQ . "/";
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
                $newFile->setFileManagerUri("/" . self::FOLDER_DOCUMENT_FAQ . "/" . $fileName);
                //$newFile->setFileManagerMime($fileFormat);
                $newFile->setFileManagerSize($fileSize);
                //$newFile->setFileManagerStatut($fileStatut);
                $newFile->setFileManagerDate($fileDate);
                $em->persist($newFile);

                //Insertion du nouveau fichier :
                $newFaFm = $em->getRepository(LFaqFileManager::class)->findOneBy(array('lFafmFaq' => $faq));
                if (!$newFaFm){
                    $newFaFm = new LFaqFileManager();
                }
                $newFaFm->setLFafmFaq($faq);
                $newFaFm->setLFafmFileManager($newFile);
                $newFaFm->setLFafmNomFichier($fileDocName);
                $em->persist($newFaFm); 
            }
        }
        $em->persist($faq);
        $em->flush();      
        
        return new JsonResponse(array(
            'faqId' => $faqId,
            'faq'   => $faq,
            'status' => "ok"
        ));
    }
      
    /**
     * @Route("admin/create/faq", name="createFaq")
     * @return Response
     */
    public function createFaq(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = $this->get('session')->get('parameters'); 
        
        $faq = new Faq();
        $faq->setFaqTitre($request->request->get('faqTitre'));
        $faq->setFaqHeader($request->request->get('faqHeader'));
        $faq->setFaqReponse($request->request->get('faqReponse'));
        $faq->setFaqQuestion($request->request->get('faqQuestion'));
        $faq->setFaqDate(\DateTime::createFromFormat("j/m/Y", $request->request->get('faqDate')));
        
        $faqNumOrdre = $request->request->get('faqNumOrdre');
        if($faqNumOrdre == ""){
            $faqNumOrdre = 0;
        }
        $faq->setFaqNumOrdre($faqNumOrdre); 

        $file = json_decode($request->request->get("faqDoc"));
        $File = $file->documents;

        if (!empty($File)){
            $fileName  = $File[0]->faqDocName;
            $fileUri = $File[0]->faqDocURI;
            //$fileFormat = $file->faqDocFormat;
            //$fileStatut = $file->faqDocStatut;
            $fileSize = $File[0]->faqDocSize;
           // $fileDate = $file->faqDocDate;
            
           $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_DOCUMENT_FAQ . "/";
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
            $newFile->setFileManagerUri("/" . self::FOLDER_DOCUMENT_FAQ . "/" . $fileName);
            //$newFile->setFileManagerUri("/" . $fileName);
            //$newFile->setFileManagerMime($fileFormat);
            $newFile->setFileManagerSize($fileSize);
            //$newFile->setFileManagerStatut($fileStatut);
            $newFile->setFileManagerDate(date("U"));
            $em->persist($newFile);

            //Insertion du nouveau fichier :
            $newFaFm = new LFaqFileManager();
            $newFaFm->setLFafmFaq($faq);
            $newFaFm->setLFafmFileManager($newFile);
            $newFaFm->setLFafmNomFichier($request->request->get('faqTitreDoc'));
            
            $em->persist($newFaFm);
        }

        $em->persist($faq);
        $em->flush();      
        return new JsonResponse(array(
            'faq'   => $faq,
            'status' => "ok"
        ));
    }
    
    /**
     * @Route("admin/remove/faq/{faqId}", name="deleteFaq")
     * @return Response
     */
    public function deldeteFaq(string $faqId)
    {
        $em = $this->getDoctrine()->getManager();
        $faq = $em->getRepository(Faq::class)->find($faqId);

        $files = $em->getRepository(LFaqFileManager::class)->findBy(array('lFafmFaq' => $faqId));
        foreach($files as $file){
            $em->remove($file);
        }
        $em->remove($faq);
        $em->flush($faq);
       
        return  new JsonResponse(array(
            'status' => "ok"
        ));
    }
    
        
    /**
     * @Route("admin/faq/insertFile", name="insertFaqFile")
     * @return Response
     */
    public function insertFaqFile(){
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
                $extensions_autorisees = array('pdf', 'txt', 'csv', 'doc', 'docx', 'pdf', 'png', 'gif', 'jpg', 'jpeg', 'xls', 'xlsx');
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
        ));
    }
    
    
          
    /**
     * @Route("admin/remove/faq/document/{docId}", name="removeDocFaq")
     * @return Response
     */
    public function removeDocFaq(int $docId){
        $em = $this->getDoctrine()->getManager();
        $fileManagerDAO = $this->get('filemanager.dao');

        //Récupération de l'URL
        $doc = $em->getRepository(FileManager::class)->find($docId);
        if ($doc){
            /*$docURI = $doc->getFileManagerUri();
            
            //Suppression du fichier
           
            $parameters = $this->get('session')->get('parameters');      
            unlink($parameters['PATH_FOLDER_FILES'] . '/' . $docURI);*/
            
            //Suppression de la clé secondaire du FileManager
            $repoFaqFileManager = $em->getRepository(LFaqFileManager::class);
            $Fafm = $repoFaqFileManager->findOneBy(array('lFafmFileManager' => $doc));
            $em->remove($Fafm);
                    
            //Suppression dans le file Manager
            $fileManagerDAO->removeFile($doc);
            /*$em->remove($doc);*/
            $em->flush();
        }
        return new JsonResponse(array('status' => 'ok'));
    }
    
}
