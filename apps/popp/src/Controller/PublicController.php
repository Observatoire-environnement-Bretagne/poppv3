<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends Controller
{
    CONST FOLDER_UPLOAD_IMG_CKE = 'CKEDITOR';

    /**
     * @Route("/public/apropos", name="public_apropos")
     * @return Response
     */
    public function getAPropos()
    {
        return $this->render("public/apropos.html.twig");
    }

    /**
     * @Route("/public/structures", name="public_structures")
     * @return Response
     */
    public function getStructures()
    {
        return $this->render("public/structures.html.twig");
    }

    /**
     * @Route("/public/ressources", name="public_ressources")
     * @return Response
     */
    public function getRessources()
    {
        return $this->render("public/ressources.html.twig");
    }

    /**
     * @Route("/public/faq", name="public_faq")
     * @return Response
     */
    public function getFaq()
    {
        return $this->render("public/faq.html.twig");
    }

    /**
     * @Route("/public/actualite", name="public_actualite")
     * @return Response
     */
    public function getActualite()
    {
        return $this->render("public/actualite.html.twig");
    }
    


    /**
     * @Route("/public/image/ckeditor", name="upload_img")
     * @return Response
     */
    public function imageCkeditor()
    {
        
        //On récupère les paramètres globaux
        $parameters = $this->get('session')->get('parameters');

        if (isset($_FILES['upload']) AND $_FILES['upload']['error'] == 0)
        {
            // Test si le fichier n'est pas trop gros
            if ($_FILES['upload']['size'] <= 256000000)
            {
                // Test si l'extension est autorisée
                $infosfichier = pathinfo($_FILES['upload']['name']);
                $extension_upload = strtolower($infosfichier['extension']);
                $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
                //$newPath = 'C:\wamp64\www\serv\popp_breizh\src\assets\upload\\';
                $newPath = $parameters['PATH_FOLDER_FILES'] . '/' . self::FOLDER_UPLOAD_IMG_CKE . '/';
                //On créer le dossier si il n'existe pas
                if (!file_exists($newPath)) {
                    mkdir($newPath, 0777, true);
                }
                if (in_array($extension_upload, $extensions_autorisees))
                {
                    $fname = basename($_FILES['upload']['name']);
                    $rawBaseName = pathinfo($fname, PATHINFO_FILENAME );
                    $counter = 0;
                    while(file_exists($newPath . $fname)) {
                        $fname = $rawBaseName . '_' . $counter . '.' . $extension_upload;
                        $counter++;
                    };
                    // Valider le fichier et le stocker définitivement
                    move_uploaded_file($_FILES['upload']['tmp_name'], $newPath . $fname);
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
            /*"status" => "ok",
            'fileName' => $fname,
            'fileSize' => $_FILES['file']['size'],
            'fileStatut' => $_FILES['file']['error'],
            'fileFormat' => $_FILES['file']['type'],*/
            'url' => $parameters['URL_FOLDER_FILES'] . '/' . self::FOLDER_UPLOAD_IMG_CKE . '/' . $fname,
            /*'url' => self::FOLDER_UPLOAD_IMG_CKE . '/' . $fname,
            'fileDate' => date("Y-m-d H:i:s"),*/
            //'fileType' => $fileType
        ));
    }

}