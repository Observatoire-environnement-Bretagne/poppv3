<?php

namespace App\Controller;

use App\Entity\DocumentRef;
use App\Entity\Format;
use App\Entity\Licence;
use App\Entity\FileManager;
use App\Entity\Photo;
use App\Entity\Serie;
use App\Entity\LPhotoThesaurus;
use App\Entity\LThesaurusEvolution;
use App\Entity\EvolutionPaysage;


use App\Repository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DocumentRefController extends Controller
{
    
    const FOLDER_UPLOAD_FILE = 'upload';
        
    /**
     * @Route("gestion/add/docref", name="addDocRef")
     * @return Response
     */
    
    public function addDocRef(){
        //On récupère les paramètres globaux
        //Pensez a modifier le php.ini - upload_max_filesize
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
            'newPath' => $newPath
        ));
    }
}
