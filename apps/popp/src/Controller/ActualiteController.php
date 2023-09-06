<?php

namespace App\Controller;

use App\Repository;
use App\Entity\Actualites;
use App\Entity\Carrousel;
use App\Entity\CarrouselPhoto;
use App\Entity\PorteurOpp;
use App\Entity\FileManager;

use App\Model\CommentaireDAO;
use App\Model\ParametreDAO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ActualiteController extends Controller
{

    const FOLDER_UPLOAD_FILE = 'upload';
    const FOLDER_CARROUSEL_FILE = 'carrousel';

    /**
     * @Route("show/actualite", name="showActualite")
     * @return Response
     */
    public function showActualite(CommentaireDAO $commentaireDAO)
    {
        // Chargement des services
        $ParametreDAO = $this->get('parametre.dao');
        //A déplacer au lancement de l'application
        $ParametreDAO->setGlobalParamaters();

        $em = $this->getDoctrine()->getManager();
        //Les photos du carrousel en haut sont celles sans actualités
        $CarrouselPhotos = $em->getRepository(CarrouselPhoto::class)->findBy(
            ['carrouselActualite' => null, 'carrouselIsCreating' => false],
            ['carrouselPhotoOrdre' => 'ASC']);

        $tabActualites = [];
        $actualites = $em->getRepository(Actualites::class)->findBy([], ['actualiteOrdre' => 'ASC']);
        foreach($actualites as $actualite){ 
            $tabActu = [];
            $carrouselActualite =  $em->getRepository(CarrouselPhoto::class)->findBy(array(
                'carrouselActualite' => $actualite, 
                'carrouselIsCreating' => false),
                ['carrouselPhotoOrdre' => 'ASC']);
            
            $tabActu['actualite'] = $actualite;
            $tabActu['carrousel'] = $carrouselActualite;

            $tabActualites[] = $tabActu;
        }
        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        //$CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $commentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("public/actualite.html.twig", [
            'CarrouselPhotos' => $CarrouselPhotos,
            'structures' => $structures,
            'tabActualites' => $tabActualites,
            'nbWaitingComments' => $nbWaitingComments,
        ]);
    }


    /**
     * @Route("admin/create/actualite", name="createActualite")
     * @return Response
     */
    public function createActualite()
    {
        $em = $this->getDoctrine()->getManager();
        $actualite = new Actualites();

        //On récupère les photos en cours de création
        $carrousel = $em->getRepository(CarrouselPhoto::class)->findBy( array('carrouselIsCreating' => true));

         //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
         //Pour les commentaires en attentes
         $isAdmin = $this->isGranted('ROLE_ADMIN');
         $CommentaireDAO = $this->get('commentaire.dao');
         $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
         return $this->render("actualite/create_actualite.html.twig", [
            'actualite' => $actualite,
            'action' => 'new',
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments,
            'carrousel' => $carrousel
         ]);
     }

    /**
     * @Route("admin/manage/actualite", name="manageActualite")
     * @return Response
     */
    public function manageActualite()
    {
        $em = $this->getDoctrine()->getManager();

        $carrousel = $em->getRepository(CarrouselPhoto::class)->findBy(array(
            'carrouselActualite' => null, 
            'carrouselIsCreating' => false));

         //pour les logos
         $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
         //Pour les commentaires en attentes
         $isAdmin = $this->isGranted('ROLE_ADMIN');
         $CommentaireDAO = $this->get('commentaire.dao');
         $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
         return $this->render("actualite/manage_actualite.html.twig", [
             'action' => 'mainCarrousel',
             'structures' => $structures,
             'nbWaitingComments' => $nbWaitingComments,
             'carrousel' => $carrousel,
         ]);
     }

    /**
     * @Route("admin/actualite/insertPhotos", name="insertPhotos")
     * @return Response
     */
    public function insertPhotos(Request $request){
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
            'logoPath' => $newPath . $fname,
            'logoURI' => $parameters['URL_FOLDER_FILES'] . '/' . self::FOLDER_UPLOAD_FILE . '/' . $fname,
            
        ));
    }


    /**
     * @Route("admin/actualite/insertCarouselActualite", name="insertCarouselActualite")
     * @return Response
     */
    public function insertCarouselActualite(Request $request)
    {
        //on appelle Doctrine
        $em = $this->getDoctrine()->getManager(); 
        //paramètres globaux
        $parameters = $this->get('session')->get('parameters');
       
        $Photo = $request->request->get('dropzonePhotoCarrousel');
        $namePhoto = $Photo['logoName'];

        $pathUpload = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_UPLOAD_FILE . "/";
        $pathFile = $parameters['PATH_FOLDER_FILES'] . "/" . self::FOLDER_CARROUSEL_FILE . "/";
        
        if (!file_exists($pathFile)) {
            mkdir($pathFile, 0777, true);
        }
        
        //On renomme si le fichier existe
        $fname = $namePhoto;
        $rawBaseName = pathinfo($pathUpload . $namePhoto, PATHINFO_FILENAME );
        $extension_upload = pathinfo($pathUpload . $namePhoto, PATHINFO_EXTENSION  );
        $counter = 0;
        
        while(file_exists($pathFile . $namePhoto)) {
            $namePhoto = $rawBaseName . '_' . $counter . '.' . $extension_upload;
            $counter++;
        };

        // Valider le fichier et le stocker d�finitivement
       if(!rename($pathUpload . $fname, $pathFile . $namePhoto)){
            
            return new JsonResponse(array(
                'status' => "erreur",
                'message' => "Erreur lors de la copie de la photo"
            ));
        }
        
        $carrouselPhoto = new CarrouselPhoto();
        $newPhoto = new FileManager();
        
        $newPhoto->setFileManagerNom($namePhoto);
        $newPhoto->setFileManagerUri("/" . self::FOLDER_CARROUSEL_FILE . "/" . $namePhoto);
        
        $em->persist($newPhoto);
        $em->flush();
        
        //remplacer avec clé étrangere de carrousel
        $carrouselPhoto->setCarrouselPhotoFile($newPhoto);
        $carrouselPhoto->setCarrouselPhotoTitre($request->request->get('titrePhotoCarousel'));
        if($request->request->get('ordrePhotoCarousel') != ""){
            $carrouselPhoto->setCarrouselPhotoOrdre($request->request->get('ordrePhotoCarousel'));
        }
        if($request->request->get('action') != 'mainCarrousel'){
            $carrouselPhoto->setCarrouselIsCreating(true);
        }else{
            $carrouselPhoto->setCarrouselIsCreating(false);
        }

        $em->persist($carrouselPhoto);
        $em->flush();

        

        return new JsonResponse(array(
            'status' => "ok",
            'url' => $newPhoto->getFileManagerUri(),
            'title' => $carrouselPhoto->getCarrouselPhotoTitre(),
            'id' => $carrouselPhoto->getCarrouselPhotoId(),
            
        ));
        
    }


    /**
     * @Route("admin/remove/photoActualite/{carrouselPhotoId}", name="deletePhotoCarousel")
     * @return Response
     */

    public function deletePhotoCarousel(string $carrouselPhotoId)
    {
        $em = $this->getDoctrine()->getManager();

        $carrouselPhoto = $em->getRepository(CarrouselPhoto::class)->find($carrouselPhotoId);

        $em->remove($carrouselPhoto);
        $em->flush($carrouselPhoto);
       
        return  new JsonResponse(array(
            'status' => "ok"
        ));
    }


        

     /**
     * @Route("admin/save/actualite", name="saveActualite")
     * @return Response
     */
    public function saveActualite(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $actualiteId = $request->request->get('actualiteId');
        $ordreActualite = $request->request->get('ordreActualite');
        
        if (trim($actualiteId) == 'new') {
            $actualite = new Actualites();
        } else {
            $actualite = $em->getRepository(Actualites::class)->find($actualiteId);
        }
        $actualite->setactualiteEditor($request->request->get('actualiteEditor'));

        $actualite->setActualiteOrdre($ordreActualite == "" ? null : $ordreActualite);

        $em->persist($actualite);
        $em->flush();

        $carrouselPhotos = $em->getRepository(CarrouselPhoto::class)->findBy( array('carrouselIsCreating' => true));
        foreach($carrouselPhotos as $carrouselPhoto){
            $carrouselPhoto->setCarrouselIsCreating(false);
            $carrouselPhoto->setCarrouselActualite($actualite);
            $em->persist($carrouselPhoto);
        }
        $em->flush();

        return  new JsonResponse(array(
            'status' => "ok"
        ));
    }


    /**
     * @Route("admin/remove/actualite/{actualiteId}", name="deleteActualite")
     * @return Response
     */

    public function deleteActualite(string $actualiteId)
    {
        $em = $this->getDoctrine()->getManager();
        $actualite = $em->getRepository(Actualites::class)->find($actualiteId);

        $em->remove($actualite);
        $em->flush($actualite);
       
        return  new JsonResponse(array(
            'status' => "ok"
        ));
    }


    /**
     * @Route("admin/update/actualite/{actualiteId}", name="updateActualite")
     * @return Response
     */
    public function updateActualite($actualiteId)
    {
        $em = $this->getDoctrine()->getManager();
        $actualite = $em->getRepository(Actualites::class)->find($actualiteId);
        $carrousel = $em->getRepository(CarrouselPhoto::class)->findBy(array('carrouselActualite' => $actualiteId));

         //pour les logos
         $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
         //Pour les commentaires en attentes
         $isAdmin = $this->isGranted('ROLE_ADMIN');
         $CommentaireDAO = $this->get('commentaire.dao');
         $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
         return $this->render("actualite/create_actualite.html.twig", [
             'actualite' => $actualite,
             'action' => 'update',
             'structures' => $structures,
             'nbWaitingComments' => $nbWaitingComments,
             'carrousel' => $carrousel
         ]);

    }

}
