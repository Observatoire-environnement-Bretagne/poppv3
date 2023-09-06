<?php
namespace App\Model;

use Dompdf\Dompdf;
use App\Entity\Son;
use Dompdf\Options;
use App\Entity\Photo;
use App\Entity\Serie;
use App\Entity\Document;
use App\Entity\Commentaire;
use App\Entity\LienExterne;
use App\Entity\LPhotoThesaurus;
use App\Entity\LSerieAxeThematic;
use App\Entity\LThesaurusEvolution;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\LPhotoThesaurusFacultatif;
use App\Entity\LSerieUnitePaysagereLocale;
use Proxies\__CG__\App\Entity\DocumentRef;
use App\Entity\LThesaurusFacultatifEvolution;

use Twig\Environment;

class SerieDAO {
    // Référence au service Doctrine
    private $em;
    
    private $twig;
    
    // Constructeur pour permettre au Service Container
    // de nous donner le service Doctrine
    public function __construct(EntityManagerInterface $em, ContainerInterface $container, Environment $twig) {
        //$this->doctrine = $doctrine;
        $this->em = $em;
        $this->container = $container;
        $this->twig = $twig;
    }


    public function removeSerie($serie){
        $session = $this->container->get('session');
        $parametres = $session->get('parameters');
        
        $em = $this->em;
    
        $fileManagerDAO = $this->container->get('filemanager.dao');

        $documents = $em->getRepository(Document::class)->findBy(array('documentSerie' => $serie));
        foreach($documents as $document){
            $file = $document->getDocumentFile();
            $em->remove($document);
            $fileManagerDAO->removeFile($file);
            $em->flush();
        }
        
        $lienExternes = $em->getRepository(LienExterne::class)->findBy(array('lienExterneSerie' => $serie));
        foreach($lienExternes as $lienExterne){
            $em->remove($lienExterne);
            $em->flush();
        }
        
        $lSerieAxeThematics = $em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatSerie' => $serie));
        foreach($lSerieAxeThematics as $lSerieAxeThematic){
            $em->remove($lSerieAxeThematic);
            $em->flush();
        }
        
        
        $LSerieUnitePaysagereLocales = $em->getRepository(LSerieUnitePaysagereLocale::class)->findBy(array('lSuplSerie' => $serie));
        foreach($LSerieUnitePaysagereLocales as $LSerieUnitePaysagereLocale){
            $em->remove($LSerieUnitePaysagereLocale);
            $em->flush();
        }

        $Sons = $em->getRepository(Son::class)->findBy(array('sonSerie' => $serie));
        foreach($Sons as $Son){
            $em->remove($Son);
            $em->flush();
        }
        
        $Photos = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie));
        foreach($Photos as $Photo){
            $Commentaires = $em->getRepository(Commentaire::class)->findBy(array('commentairePhoto' => $Photo));
            foreach($Commentaires as $Commentaire){
                $em->remove($Commentaire);
                $em->flush();
            }
            
            $LPhotoThesauruss = $em->getRepository(LPhotoThesaurus::class)->findBy(array('lPtPhoto' => $Photo));
            foreach($LPhotoThesauruss as $LPhotoThesaurus){
                $LThesaurusEvolutions = $em->getRepository(LThesaurusEvolution::class)->findBy(array('lTePhotoThesaurus' => $LPhotoThesaurus));
                foreach($LThesaurusEvolutions as $LThesaurusEvolution){
                    $em->remove($LThesaurusEvolution);
                    $em->flush();
                }
                $em->remove($LPhotoThesaurus);
                $em->flush();
            }
            
            $LPhotoThesaurusFacultatifs = $em->getRepository(LPhotoThesaurusFacultatif::class)->findBy(array('lPtfPhoto' => $Photo));
            foreach($LPhotoThesaurusFacultatifs as $LPhotoThesaurusFacultatif){
                $LThesaurusFacultatifEvolutions = $em->getRepository(LThesaurusFacultatifEvolution::class)->findBy(array('lTfePhotoThesaurus' => $LPhotoThesaurusFacultatif));
                foreach($LThesaurusFacultatifEvolutions as $LThesaurusFacultatifEvolution){
                    $em->remove($LThesaurusFacultatifEvolution);
                    $em->flush();
                }
                $em->remove($LPhotoThesaurusFacultatif);
                $em->flush();
            }
        
            $file = $Photo->getPhotoFile();
            $em->remove($Photo);
            $em->flush();

            //on vérifie que la photo n'es pas déja rattaché (ne doit pas arriver mais au cas ou)
            $PhotosFromfile = $em->getRepository(Photo::class)->findBy(array('photoFile' => $file));
            if(count($PhotosFromfile) == 0 && $file){
                $fileManagerDAO->removeFile($file);
            }
            //$em->clear();
            $em->flush();
        }

        $fileCroquis = $serie->getSerieCroquis();
        $filePhotoAerienne = $serie->getSeriePhotoAerienne();
        $filePhotoContext = $serie->getSeriePhotoContext();
        $filePhotoIgn = $serie->getSeriePhotoIgn();
        $filePhotoTrepied = $serie->getSeriePhotoTrepied();
        $DocumentRef = $serie->getSerieRefdoc();

        $em->remove($serie);
        if($fileCroquis){$fileManagerDAO->removeFile($fileCroquis);}
        if($filePhotoAerienne){$fileManagerDAO->removeFile($filePhotoAerienne);}
        if($filePhotoContext){$fileManagerDAO->removeFile($filePhotoContext);}
        if($filePhotoIgn){$fileManagerDAO->removeFile($filePhotoIgn);}
        if($filePhotoTrepied){$fileManagerDAO->removeFile($filePhotoTrepied);}
        if($DocumentRef){
            $fileDocumentRef = $DocumentRef->getDocumentRefFile();
            $em->remove($DocumentRef);
            //on vérifie que la photo n'es pas déja rattaché (ne doit pas arriver mais au cas ou)
            /*$PhotosFromfile = $em->getRepository(Photo::class)->findBy(array('photoFile' => $file));
            if(count($PhotosFromfile) == 0){$fileManagerDAO->removeFile($file);}*/
            $DocumentRefFromfile = $em->getRepository(DocumentRef::class)->findBy(array('documentRefFile' => $fileDocumentRef));
            if($fileDocumentRef && !$DocumentRefFromfile){
                $fileManagerDAO->removeFile($fileDocumentRef);
            }
        }

        $em->flush();
    }

    
    public function generateFicheTerrainPdf($serieId){
        //paramètres golobaux
        $session = $this->container->get('session');
        $parametres = $session->get('parameters');

        $kernel = $this->container->get('kernel');
        // In this case, we want to write the file in the public directory
        $publicDirectory = $kernel->getProjectDir() . '/public';
        // e.g /var/www/project/public/mypdf.pdf
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('tempDir', $publicDirectory);
        //$pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        $serie = $this->em->getRepository(Serie::class)->find($serieId);

        /*$coordinates2154 = $_POST["coordinates2154"];
        $longitude2154 = $coordinates2154[0];
        $latitude2154 = $coordinates2154[1];*/
        
        //Récupération des Axes thématiques pour la série
        $AxeTheBySerie = $this->em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatSerie' => $serie));
        
        $photo = $this->em->getRepository(Photo::class)->findOneBy(array('photoSerie' => $serie), array('photoDatePrise' => 'ASC'));

        // Retrieve the HTML generated in our twig file
        //$base64 = "data:image/png;base64, ".base64_encode('http://geofit-popp.ataraxie.fr/assets/images/popp.c8b2847d.png');
        $base64 = "data:image/png;base64, ".base64_encode($parametres['URL_FOLDER_FILES'] . '/custom/popp.png');
        $path = $parametres['PATH_FOLDER_FILES'] . '/custom/popp.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        //return new JsonResponse($base64);
        //$templating = $this->container->get('templating');
        $html = $this->twig->render(
            'serie/download_fiche.html.twig', 
            array('logo' => $base64, 'serie' => $serie, 'photo' => $photo, 'longitude2154' => '', 'latitude2154' => '' , 'AxeTheBySerie'  => $AxeTheBySerie)
        );

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        //$dompdf->loadHtml("hello");
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        set_time_limit(300);
        // Render the HTML as PDF
        $dompdf->render();
        //return $html;
        $output = $dompdf->output();

        $datetime = new \DateTime();
        $filePath = $parametres['PATH_FOLDER_FILES'] . "/serie_pdf/" . $serieId . '_' . $datetime->format('U') . '.pdf';
        $fileUrl = $parametres['URL_FOLDER_FILES'] . "/serie_pdf/" . $serieId . '_' . $datetime->format('U') . '.pdf';
        
        // Write file to the desired path
        file_put_contents($filePath, $output);

        return $html;
        /*return new JsonResponse(array(
            'status' => 'ok', 
            'fileUrl' => $fileUrl,
            'filePath' => $filePath,
            'AxeTheBySerie'  => $AxeTheBySerie,
            'longitude2154' => $longitude2154,
            'latitude2154' => $latitude2154,
        ));*/
      }
}