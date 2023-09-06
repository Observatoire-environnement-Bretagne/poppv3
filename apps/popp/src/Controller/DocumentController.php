<?php

namespace App\Controller;

use App\Entity\DocumentAnnexe;
use App\Entity\FileManager;
use App\Entity\LDocumentAnnexeOpp;
use App\Entity\LFournisseurOpp;
use App\Entity\LGestionnaireOpp;
use App\Entity\Opp;
use App\Entity\PorteurOpp;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DocumentController extends Controller
{
    CONST FOLDER_DOCUMENT_ANNEXE = 'document_annexe';   
    const FOLDER_UPLOAD_FILE = 'upload';

    /**
     * @Route("gestion/show/documents", name="showDocuments")
     * @return Response
     */
    public function showDocuments()
    {
        $em = $this->getDoctrine()->getManager();
        $opps = $em->getRepository(Opp::class)->findAll();
        
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $isGestionnaire = $this->isGranted('ROLE_GESTIONNAIRE');
        $isFournisseur = $this->isGranted('ROLE_FOURNISSEUR');
        $tabOpp = [];
        foreach($opps as $opp){
            if($isAdmin){
                $tabOpp[] = $opp;
            }else if($isGestionnaire){
                $isGestionnaireOpp = $em->getRepository(LGestionnaireOpp::class)->findBy(array('lGoOpp' => $opp, 'lGoUsers' => $this->getUser()));
                $isFournisseurOpp = $em->getRepository(LFournisseurOpp::class)->findBy(array('lFoOpp' => $opp, 'lFoUsers' => $this->getUser()));
                if(count($isGestionnaireOpp) == 1 || count($isFournisseurOpp) == 1){
                    $tabOpp[] = $opp;
                }
            }else if($isFournisseur){
                $isFournisseurOpp = $em->getRepository(LFournisseurOpp::class)->findBy(array('lFoOpp' => $opp, 'lFoUsers' => $this->getUser()));
                if(count($isFournisseurOpp) == 1){
                    $tabOpp[] = $opp;
                }
            }
        }
        $documents = [];
        foreach($tabOpp as $opp){
            //$documentByOpp = $em->getRepository(DocumentAnnexe::class)->findBy(array('documentAnnexeOpp' => $opp));
            $documentsByOpp = $em->getRepository(LDocumentAnnexeOpp::class)->findBy(array('lDaoOpp' => $opp));
            if(isSet($documentsByOpp)){
                foreach($documentsByOpp as $document){
                    $documents[$document->getLDaoDocumentAnnexe()->getDocumentAnnexeId()]['document'] = $document->getLDaoDocumentAnnexe();
                    $documents[$document->getLDaoDocumentAnnexe()->getDocumentAnnexeId()]['opp'][] = $opp;
                }
            }
        }

        //ajoute les documents dispo pour tous
        $documentsAnnexeTous = $em->getRepository(DocumentAnnexe::class)->findBy(array('documentAnnexeAllOpp' => true));
        foreach($documentsAnnexeTous as $documentAnnexeTous){
            $documents[$documentAnnexeTous->getDocumentAnnexeId()]['document'] = $documentAnnexeTous;
            $documents[$documentAnnexeTous->getDocumentAnnexeId()]['opp'] = [];
        }

        //$documents = $em->getRepository(DocumentAnnexe::class)->findAll();
        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        
        return $this->render("document/document.html.twig",
            array('documents' => $documents,
                'opps' => $tabOpp,
                'structures' => $structures,
                'nbWaitingComments' => $nbWaitingComments)
        );
        
    }

    /**
     * @Route("gestion/modify/document", name="modifyDocument")
     * @return Response
     */
    public function modifyDocument(Request $request)
    {
        $fileManagerDAO = $this->get('filemanager.dao');
        $parameters = $this->get('session')->get('parameters');
        $em = $this->getDoctrine()->getManager();
        $documentId = $request->request->get('document_annexe_id');
        if($documentId == 'new'){
            $documentAnnexe = new DocumentAnnexe();
        }else{
            $documentAnnexe = $em->getRepository(DocumentAnnexe::class)->find($documentId);
        }
        $documentAnnexe->setDocumentAnnexeTitre($request->request->get('document_annexe_titre'));
        $documentAnnexe->setDocumentAnnexeDesc($request->request->get('document_annexe_desc'));
        //$opp = $em->getRepository(Opp::class)->find($request->request->get('document_annexe_opp_id'));
        //$documentAnnexe->setDocumentAnnexeOpp($opp);


        //Gestion des fichiers son
        switch($request->request->get('document_annexe_file_action')){
            case 'new' :
                //création du fichier
                $nomFile = $request->request->get('document_annexe_file_name');

                $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_DOCUMENT_ANNEXE . "/";
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
                $newDocumentAnnexe = new FileManager();
                
                $newDocumentAnnexe->setFileManagerNom($nomFile);
                $newDocumentAnnexe->setFileManagerUri("/" . self::FOLDER_DOCUMENT_ANNEXE . "/" . $nomFile);
                $newDocumentAnnexe->setFileManagerSize($request->request->get('document_annexe_file_size'));
                $datetime = \DateTime::createFromFormat("U", date("Y-m-d H:i:s"));
                $newDocumentAnnexe->setFileManagerDate(date("U"));

                $em->persist($newDocumentAnnexe);
                $em->flush();

                $oldDocumentAnnexe = $documentAnnexe->getDocumentAnnexeFile();
                
                $documentAnnexe->setDocumentAnnexeFile($newDocumentAnnexe);

                //si un son existe, on la supprime 
                if($oldDocumentAnnexe){
                    $fileManagerDAO->removeFile($oldDocumentAnnexe);
                }

            break;
        }
        
        $em->persist($documentAnnexe);
        $em->flush();
        
        //on supprime avant de créer
        $LDocumentAnnexeOpps = $em->getRepository(LDocumentAnnexeOpp::class)->findBy(array('lDaoDocumentAnnexe' => $documentAnnexe));
        foreach($LDocumentAnnexeOpps as $LDocumentAnnexeOpp){
            $em->remove($LDocumentAnnexeOpp);
        }

        if($request->request->get('document_annexe_all_opp') == 'true'){
            $documentAnnexe->setDocumentAnnexeAllOpp(true);
        }else{
            $documentAnnexe->setDocumentAnnexeAllOpp(false);
            foreach($request->request->get('document_annexe_opp_id') as $oppId){
                $opp = $em->getRepository(Opp::class)->find($oppId);
                $ldocAnnexeOpp = new LDocumentAnnexeOpp();
                $ldocAnnexeOpp->setLDaoOpp($opp);
                $ldocAnnexeOpp->setLDaoDocumentAnnexe($documentAnnexe);
    
                $em->persist($ldocAnnexeOpp);
            }
        }
        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok', 
            'documentAnnexeId' => $documentAnnexe->getDocumentAnnexeId(), 
            'documentAnnexeFileUrl' => $parameters['URL_FOLDER_FILES'] . $documentAnnexe->getDocumentAnnexeFile()->getFileManagerUri(), 
            'documentAnnexeFileName' => $documentAnnexe->getDocumentAnnexeFile()->getFileManagerNom()
        ));
    }
    
    
    /**
     * @Route("gestion/delete/document/{idDocument}", name="deleteDocument")
     * @return Response
     */
    public function deleteDocument(string $idDocument)
    {
        $fileManagerDAO = $this->get('filemanager.dao');
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository(DocumentAnnexe::class)->find($idDocument);

        $file = $document->getDocumentAnnexeFile();

        $em->remove($document);
        $em->flush();
        
        $fileManagerDAO->removeFile($file);
        return new JsonResponse(array('status' => 'ok'));
    }
}
