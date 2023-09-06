<?php

namespace App\Controller;

use DateTime;
use App\Entity\Opp;
use App\Repository;
use App\Entity\Photo;
use App\Entity\Serie;
use App\Entity\Licence;
use App\Entity\Carrousel;
use App\Entity\Actualites;
use App\Entity\PorteurOpp;
use App\Entity\FileManager;



use App\Model\CommentaireDAO;
use App\Entity\CarrouselPhoto;
use App\Entity\LPhotoThesaurus;
use App\Entity\LSerieAxeThematic;
use App\Entity\LThesaurusEvolution;
use App\Repository\PhotoRepository;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Entity\LPhotoThesaurusFacultatif;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Entity\LThesaurusFacultatifEvolution;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class PanierController extends Controller
{

    
    private $tabSerieTot= [];

    /**
     * @Route("show/panier", name="showPanier")
     * @return Response
     */
    public function showPanier(CommentaireDAO $commentaireDAO)
    {
        // Chargement des services
        $ParametreDAO = $this->get('parametre.dao');
        //A déplacer au lancement de l'application
        $ParametreDAO->setGlobalParamaters();
        
        $em = $this->getDoctrine()->getManager();
        $series = [];
        $serieSelection = $this->get('session')->get('panier');
        
        foreach ((array) $serieSelection as $id){
            $serie = $em->getRepository(Serie::class)->find($id);
            $series[] = $serie;
            
        }
            
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        //$CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $commentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("public/panier.html.twig", [
            'serieSelection' => $serieSelection,
            'series' => $series,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments,
        ]);

    }

    /**
     * @Route("/public/add/panier", name="ajoutSeriePanier")
     * @return Response
     */
    public function ajoutSeriePanier(Request $request)
    {
        //récupère le tableau d'id
        $tabId = $request->request->get('tabId');
        
        //mettre les séries sélectionnées dans un tableau
        $resultat = $this->get('session')->get('panier');
        if(!isSet($resultat)){
            $resultat = [];
        }

        $nbSerieInPanier = count($resultat);
        // pour un id du tableau d'id faire
        foreach($tabId as $id ){
             //si serieSelection existe faire
            if (isset($resultat)){
                $resultat[]=$id;
            } else {
                $resultat = array($id);
            }
            
        }
        $serieSelection = array_unique($resultat);

        $status="ras";
        //Si on a bien ajouter une série
        if($nbSerieInPanier != count($serieSelection)) {
            $this->get('session')->set('panier', $serieSelection);
            $status="ok";
        }
        
        return  new JsonResponse(array(
            'status' => $status,
            'serieSelection' => $serieSelection
        ));
    }

    /**
     * @Route("public/get/session/panier", name="getSessionPanier")
     * @return Response
     */

    public function getSessionPanier(){
        
        return  new JsonResponse(array(
            'status' => 'ok',
            'serieSelection' => $this->get('session')->get('panier')
        ));
    }

    


    /**
     * @Route("public/remove/panierSelection/{panierSelectionId}", name="deleteSelectionPanier")
     * @return Response
     */

    public function deleteSelectionPanier($panierSelectionId, TranslatorInterface $translator)
    {
        $serieSelection = $this->get('session')->get('panier');

        unset($serieSelection[array_search($panierSelectionId, $serieSelection)]);

        sort($serieSelection);

        $this->get('session')->set('panier', $serieSelection);
           
        /*$this->addFlash("success", $translator->trans('suppression'));*/
        return  new JsonResponse(array(
            'status' => "ok",
            'serieSelection' => $serieSelection,
            'message' => $translator->trans('labelDeletedSerie'),
        ));    
       
        
    }

    /**
     * @Route("public/remove/viderPanier", name="ViderPanier")
     * @return Response
     */

    public function ViderPanier()
    {
        $serieSelection = array();

        $this->get('session')->set('panier', $serieSelection);
           
        return  new JsonResponse(array(
            'status' => "ok",
            'serieSelection' => $serieSelection,
            
        ));    
       
        
    }


    /**
     * @Route("public/generation/fichierPanier", name="generateFichierPanier")
     * @return Response
     */
    public function generateFichierPanier()
    {
        ini_set('set_time_limit', '600'); //10min
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '600');
        $em = $this->getDoctrine()->getManager();
        $serieSelection = $this->get('session')->get('panier');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Descriptif des séries");

        $alphabet = array_combine(range(1,26), range('A', 'Z'));

        $spreadsheet->getDefaultStyle()
                    ->getFont()
                    ->setName('Arial')
                    ->setSize(10);

        //Mise en forme de la première ligne
        $spreadsheet->getActiveSheet()->getStyle('1')->getFont()->setBold( true );

        $header = array(
            "nomSerie" => "Nom de la série",
            "urlSerie" => "URL",
            "structureOpp" => "Sructure OPP",
            "opp" => "OPP",
            "idSeriePopp" => "ID série POPP",
            "frequenceReconduction" => "Fréquence de reconduction prévue",
            "axeThematique" => "Axe(s) thématique(s)",
            "licencePhoto" => "Licence photo",
            "departement" => "Département",
            "commune" => "Commune",
            "lieuPriseVue" => "Lieu de prise de vue",
            "datePhoto" => "Date de la photo initiale",
            "nombrePhotoSerie" => "Nombre de photos dans la série",
            "elementPaysage" => "Elements de paysage"
        );

        //Création du tableau des lignes de série
        $rows = array();
        foreach ($serieSelection as $id){
            $serie = $em->getRepository(Serie::class)->find($id);
            $photos = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie));
            $url = $this->generateUrl(
                'get_serie', [
                    'id'=>$id
                ],
                UrlGeneratorInterface::ABSOLUTE_URL);

            $listeAxeThematique = $em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatSerie'=> $serie));
            $tabAxeSerie = [];
            foreach ($listeAxeThematique as $axeThematique){
                $tabAxeSerie[$axeThematique->getLSatAxeThematic()->getAxeThematicId()] = $axeThematique->getLSatAxeThematic()->getAxeThematicNom();
            }

            $tabLicence = [];
            $datePhotoInitiale = "";
            $tabElementPaysage = [];
            foreach ($photos as $photo){
                if($photo->getPhotoLicence()){
                    $tabLicence[$photo->getPhotoLicence()->getLicenceId()] = $photo->getPhotoLicence()->getLicenceNom();
                }
                if($datePhotoInitiale == "" || $datePhotoInitiale > $photo->getPhotoDatePrise()){
                    $datePhotoInitiale = $photo->getPhotoDatePrise();
                }
                $listeElementPaysage = $em->getRepository(LPhotoThesaurus::class)->findBy(array('lPtPhoto'=> $photo));
                foreach ($listeElementPaysage as $elementPaysage){
                    $tabElementPaysage[$elementPaysage->getLPtThesaurus()->getThesaurusTreeId()] = $elementPaysage->getLPtThesaurus()->getThesaurusTreeNom();
                }
            }


            $row = array(
                "nomSerie" => $serie->getSerieTitre(),
                "urlSerie" => $url,
                "structureOpp" => $serie->getSeriePorteurOpp()? $serie->getSeriePorteurOpp()->getPorteurOppNom(): '',
                "opp" => $serie->getSerieOpp()? $serie->getSerieOpp()->getOppNom(): '',
                "idSeriePopp" => $serie->getSerieId(),
                "frequenceReconduction" => $serie->getSerieFreqInterval() . " " .  $serie->getSerieFreqPeriod(),
                "axeThematique" => implode(", ", $tabAxeSerie),
                "licencePhoto" => implode(", ", $tabLicence),
                "departement" => $serie->getSerieDepartement()?  $serie->getSerieDepartement()->getDepartementNom(): '',
                "commune" => $serie->getSerieCommune()?  $serie->getSerieCommune()->getCommuneNom(): '',
                "lieuPriseVue" => $serie->getSerieAdresse()? $serie->getSerieAdresse(): '',
                "datePhoto" => $datePhotoInitiale != "" ? $datePhotoInitiale->format('d/m/Y') : "",
                "nombrePhotoSerie" => count($photos),
                "elementPaysage" => implode(", ", $tabElementPaysage)
            );
            $rows[] = $row;
        }
        
        //Insertion de l'entete dans le tableur
        $i=0;
        foreach($header as $headerTitre){
            $i++;
            if($i > 26){
                $modulo = ($i % 26);
                $reste = intdiv( $i, 26);
                $lettre = $alphabet[$reste] . $alphabet[$modulo];
            
            } else {
                $lettre = $alphabet[$i];
            }
            
            $sheet->setCellValue($lettre.'1', $headerTitre);
            $spreadsheet->getActiveSheet()->getColumnDimension($lettre)->setAutoSize(true);
            $spreadsheet->getActiveSheet()
                ->getStyle($lettre.'1', $headerTitre)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFFFD6A2');
        }

        //Insertion des lignes série dans le tableur
        $k = 1;
        foreach($rows as $row){
            $k++;
            $i = 0;
            foreach($row as $cellule){ 
                $i++;
                if($i > 26){
                    $modulo = ($i % 26);
                    $reste = intdiv( $i, 26);
                    $lettre = $alphabet[$reste] . $alphabet[$modulo];
                
                } else {
                    $lettre = $alphabet[$i];
                }

                $sheet->setCellValue($lettre . $k, $cellule);
            }
        }

        /*  -------------- -------------------- ---------------- ---------------------*/
        //Onglet sur les élements de paysage
        //$myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Suivi des séries');
        $myWorkSheet = new Worksheet($spreadsheet, 'Suivi des séries');
        // Attach the "My Data" worksheet as the first worksheet in the Spreadsheet object
        $spreadsheet->addSheet($myWorkSheet, 1);

        //$myWorkSheet->getStyle('A1:Z1')->applyFromArray($styleArray1);

        //On créer le tableau des éléments de paysage
        $tabElementPaysage = [];
        $tabSeries = [];
        $tabAnnee = [];
        foreach ($serieSelection as $id){
            $serie = $em->getRepository(Serie::class)->find($id);
            $photos = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie), array('photoDatePrise' => "ASC"));
            $first = true;
            foreach($photos as $photo){
                if($first){
                    $first = false;
                }else{
                    //Thésaurus
                    $listeElementsPaysages = $em->getRepository(LPhotoThesaurus::class)->findBy(array('lPtPhoto'=> $photo));
                    foreach ($listeElementsPaysages as $elementPaysage){
                        $tabElementPaysage[$elementPaysage->getLPtThesaurus()->getThesaurusTreeId()] = $elementPaysage->getLPtThesaurus()->getThesaurusTreeNom();
                    }
                    //Thésaurus facultatif
                    $listeElementsPaysagesFacultatif = $em->getRepository(LPhotoThesaurusFacultatif::class)->findBy(array('lPtfPhoto'=> $photo));
                    foreach ($listeElementsPaysagesFacultatif as $elementPaysageFacultatif){
                        $tabElementPaysage['f-'.$elementPaysageFacultatif->getLPtfThesaurus()->getThesaurusTreeFacultatifId()] = $elementPaysageFacultatif->getLPtfThesaurus()->getThesaurusTreeFacultatifNom();
                    }
                    $tabAnnee[] = $photo->getPhotoDatePrise()->format('Y');
                }
            }
            $tabSeries[$id] = $this->generateUrl(
                'get_serie', [
                    'id'=>$id
                ],
                UrlGeneratorInterface::ABSOLUTE_URL);
        }
        $tabAnneeUnique = array_unique($tabAnnee);
        asort($tabAnneeUnique);
        
        $tableur = [];
        $blockCol = 0;
        foreach($tabAnneeUnique as $annee){
            //on crée le header pour chaque année
            $this->createBlockYear($annee, $myWorkSheet, $blockCol);

            //On créer la colonne des élements + série
            $blockRow = 0;
            foreach($tabElementPaysage as $elementPaysageId => $elementPaysageNom){
                if(!isSet($this->tabSerieTot[$elementPaysageId])){
                    $this->tabSerieTot[$elementPaysageId] = [];
                }
                if($blockCol == 0){
                    $this->createBlockElementSerie($myWorkSheet, $tabSeries, $elementPaysageNom, $blockRow);
                }
                $this->createBlockValue($myWorkSheet, $tabSeries, $elementPaysageId, $annee, $blockRow, $blockCol);
                
                $blockRow++;
            }    
            $blockCol++;
        }
        //return new JsonResponse($tabSeries);
        
        //On redimensionne les colonnes A et B
        $myWorkSheet->getColumnDimension('A' )->setAutoSize(true);
        $myWorkSheet->getColumnDimension('B' )->setAutoSize(true);
        //on crée le header Total
        $this->createBlockYear('TOTAUX', $myWorkSheet, $blockCol);
        $this->createBlockYearValue($myWorkSheet, $blockCol);

        // TODO -- facultatif xls + stats

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $datetime = new \DateTime();
        $fileName = "export_panier_" . $datetime->format('U') . ".xlsx";
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
            
    }

    private function createBlockYear($annee, &$myWorkSheet, $blockCol){
        //pair ou impair
        $color = $blockCol%2 == 1 ?'FFC6E0B4' : 'FFFFF2CC';
        $color = $annee == 'TOTAUX' ? 'FFFCE4D6' : $color;
        $styleHeader = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' =>  [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => $color) 
            ]
        ];

        $title = $annee == 'TOTAUX' ? 'TOTAUX' : 'Année ' . $annee;
        $stabilite = $annee == 'TOTAUX' ? 'Somme des stabilités' : 'Stabilité';
        $apparition = $annee == 'TOTAUX' ? 'Somme des apparitions' : 'Apparition';
        $disparition = $annee == 'TOTAUX' ? 'Somme des disparitions' : 'Disparition';
        $augmentation = $annee == 'TOTAUX' ? 'Somme des augmentations' : 'Augmentation';
        $diminution = $annee == 'TOTAUX' ? 'Somme des diminutions' : 'Diminution';
        $changement = $annee == 'TOTAUX' ? "Somme des changements d'aspect" : "Changement d'aspect";
        $colStart = 2 + ($blockCol * 6);
        
        
        $myWorkSheet->setCellValue($this->getLetterForXls($colStart) . '1', $title)
            ->setCellValue($this->getLetterForXls($colStart) . '2', $stabilite)
            ->setCellValue($this->getLetterForXls($colStart+1) . '2', $apparition)
            ->setCellValue($this->getLetterForXls($colStart+2) . '2', $disparition)
            ->setCellValue($this->getLetterForXls($colStart+3) . '2', $augmentation)
            ->setCellValue($this->getLetterForXls($colStart+4) . '2', $diminution)
            ->setCellValue($this->getLetterForXls($colStart+5) . '2', $changement)
            ->getStyle($this->getLetterForXls($colStart).'1:'.$this->getLetterForXls($colStart+5) . '2')
            ->applyFromArray($styleHeader);
        
        $myWorkSheet->getColumnDimension($this->getLetterForXls($colStart) )->setAutoSize(true);
        $myWorkSheet->getColumnDimension($this->getLetterForXls($colStart+1) )->setAutoSize(true);
        $myWorkSheet->getColumnDimension($this->getLetterForXls($colStart+2) )->setAutoSize(true);
        $myWorkSheet->getColumnDimension($this->getLetterForXls($colStart+3) )->setAutoSize(true);
        $myWorkSheet->getColumnDimension($this->getLetterForXls($colStart+4) )->setAutoSize(true);
        $myWorkSheet->getColumnDimension($this->getLetterForXls($colStart+5) )->setAutoSize(true);
        $myWorkSheet->mergeCells($this->getLetterForXls($colStart) . '1:' . $this->getLetterForXls($colStart+5). '1');
        ;
    }

    private function createBlockElementSerie(&$myWorkSheet, $tabSeries, $elementPaysageNom, $blockRow){
        
        $styleRow = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        $rowStart = 3 + ($blockRow * (count($tabSeries) + 1));
        foreach($tabSeries as $urlSerie){
            $myWorkSheet->setCellValue('A' . $rowStart, $elementPaysageNom)
            ->setCellValue('B' . $rowStart, $urlSerie)
            ->getStyle('A'. $rowStart .':B'. $rowStart)
            ->applyFromArray($styleRow);
            $rowStart++;
        }
        $styleRowTotaux = $styleRow;
        $styleRowTotaux['fill'] = [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => array('argb' => 'FFFCE4D6') 
        ];
        $myWorkSheet->setCellValue('A' . $rowStart, 'TOTAUX')
        ->setCellValue('B' . $rowStart, "")
        ->getStyle('A'. $rowStart .':B'. $rowStart)
        ->applyFromArray($styleRowTotaux);
    }

    private function createBlockValue(&$myWorkSheet, $tabSeries, $elementPaysageId, $annee, $blockRow, $blockCol){
        $em = $this->getDoctrine()->getManager();
        $colStart = 2 + ($blockCol * 6);
        $rowStart = 3 + ($blockRow * (count($tabSeries) + 1));
        //$alphabet = array_combine(range(1,26), range('A', 'Z'));
        
        $color = $blockCol%2 == 1 ?'FFC6E0B4' : 'FFFFF2CC';
        //$color = $annee == 'TOTAUX' ? 'FFFCE4D6' : $color;
        $styleRow = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' =>  [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => $color) 
            ]
        ];
        $styleRowTotaux = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' =>  [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => 'FFFCE4D6') 
            ]
        ];

        $tabEvolTot= [];
        foreach($tabSeries as $idSerie => $urlSerie){
            if(!isSet($this->tabSerieTot[$elementPaysageId][$idSerie])){
                $this->tabSerieTot[$elementPaysageId][$idSerie] = [];
            }
            $tabEvol= [];
            //$serie = $em->getRepository(Serie::class)->find($idSerie);
            //$photos = $em->getRepository(Photo::class)->findBy(array('photoSerie' => $serie));
            $photoFirst = $em->getRepository(Photo::class)->findOneBy(array('photoSerie' => $idSerie), array('photoDatePrise' => "ASC"));

            $photos = $em->getRepository(Photo::class)->findBySerieAndYear($idSerie, $annee);
            foreach($photos as $photo){
                if($photoFirst->getPhotoId() != $photo["photo_id"]){
                    //Thésaurus normal
                    if(!str_contains($elementPaysageId, 'f-')){
                        $LPhotosThesaurus = $em->getRepository(LPhotoThesaurus::class)->findBy(array('lPtPhoto'=> $photo, 'lPtThesaurus' => $elementPaysageId));
                        foreach($LPhotosThesaurus as $LPhotoThesaurus){
                            $LThesaurusEvolution = $em->getRepository(LThesaurusEvolution::class)->findBy(array('lTePhotoThesaurus'=> $LPhotoThesaurus));
                            foreach($LThesaurusEvolution as $thesaurusEvolution){
                                if (!isSet($tabEvol[$thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageId()])){
                                    $tabEvol[$thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageId()] = 
                                        array(
                                            'nom' => $thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageNom(), 
                                            'count' => 1);
                                }else{
                                    $tabEvol[$thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageId()]['count']++;
                                }
                                //Pour les totaux
                                if (!isSet($tabEvolTot[$thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageId()])){
                                    $tabEvolTot[$thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageId()] = 
                                        array(
                                            'nom' => $thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageNom(), 
                                            'count' => 1);
                                }else{
                                    $tabEvolTot[$thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageId()]['count']++;
                                }
                                
                                //Pour les totaux des séries
                                if (!isSet($this->tabSerieTot[$elementPaysageId][$idSerie][$thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageId()])){
                                    $this->tabSerieTot[$elementPaysageId][$idSerie][$thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageId()] = 
                                        array(
                                            'nom' => $thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageNom(), 
                                            'count' => 1);
                                }else{
                                    $this->tabSerieTot[$elementPaysageId][$idSerie][$thesaurusEvolution->getLTeEvolution()->getEvolutionPaysageId()]['count']++;
                                }
                            }
                        }
                    }

                    //Facultatif
                    if(str_contains($elementPaysageId, 'f-')){
                        $elementPaysageFacultatifId = str_replace("f-", "", $elementPaysageId);
                        $LPhotosThesaurusFacultatif = $em->getRepository(LPhotoThesaurusFacultatif::class)->findBy(array('lPtfPhoto'=> $photo, 'lPtfThesaurus' => $elementPaysageFacultatifId));
                        foreach($LPhotosThesaurusFacultatif as $photoThesaurusFacultatif){
                            $LThesaurusFacultatifEvolution = $em->getRepository(LThesaurusFacultatifEvolution::class)->findBy(array('lTfePhotoThesaurus'=> $photoThesaurusFacultatif));
                            foreach($LThesaurusFacultatifEvolution as $thesaurusFacultatifEvolution){
                                if (!isSet($tabEvol[$thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageId()])){
                                    $tabEvol[$thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageId()] = 
                                        array(
                                            'nom' => $thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageNom(), 
                                            'count' => 1);
                                }else{
                                    $tabEvol[$thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageId()]['count']++;
                                }
                                //Pour les totaux
                                if (!isSet($tabEvolTot[$thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageId()])){
                                    $tabEvolTot[$thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageId()] = 
                                        array(
                                            'nom' => $thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageNom(), 
                                            'count' => 1);
                                }else{
                                    $tabEvolTot[$thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageId()]['count']++;
                                }
                                
                                //Pour les totaux des séries
                                if (!isSet($this->tabSerieTot[$elementPaysageId][$idSerie][$thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageId()])){
                                    $this->tabSerieTot[$elementPaysageId][$idSerie][$thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageId()] = 
                                        array(
                                            'nom' => $thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageNom(), 
                                            'count' => 1);
                                }else{
                                    $this->tabSerieTot[$elementPaysageId][$idSerie][$thesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageId()]['count']++;
                                }
                            }
                        }
                    }
                }
            }
            foreach($tabEvol as $idEvol => $evol){
                switch($evol['nom']){
                    case 'Stabilité':
                        $col = 0;
                        break;
                    case 'Apparition':
                        $col = 1;
                        break;
                    case 'Disparition':
                        $col = 2;
                        break;
                    case 'Augmentation':
                        $col = 3;
                        break;
                    case 'Diminution':
                        $col = 4;
                        break;
                    case "Changement d'apparence":
                        $col = 5;
                        break;
                };
                $rowStart;
                $myWorkSheet->setCellValue($this->getLetterForXls($colStart + $col) . $rowStart, $evol['count']);
            }
            
            $myWorkSheet->getStyle($this->getLetterForXls($colStart) . $rowStart . ":" . $this->getLetterForXls($colStart + 5) . $rowStart)
            ->applyFromArray($styleRow);
            $rowStart++;
        }
        
        foreach($tabEvolTot as $idEvol => $evol){
            switch($evol['nom']){
                case 'Stabilité':
                    $col = 0;
                    break;
                case 'Apparition':
                    $col = 1;
                    break;
                case 'Disparition':
                    $col = 2;
                    break;
                case 'Augmentation':
                    $col = 3;
                    break;
                case 'Diminution':
                    $col = 4;
                    break;
                case "Changement d'apparence":
                    $col = 5;
                    break;
            };
            $myWorkSheet->setCellValue($this->getLetterForXls($colStart + $col) . $rowStart, $evol['count']);
        }
        $myWorkSheet->getStyle($this->getLetterForXls($colStart) . $rowStart . ":" . $this->getLetterForXls($colStart + 5) . $rowStart)
        ->applyFromArray($styleRowTotaux);

    }

    private function createBlockYearValue(&$myWorkSheet, $blockCol){
        $colStart = 2 + ($blockCol * 6);
        $rowStart = 3;
        
        $styleRowTotaux = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' =>  [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => 'FFFCE4D6') 
            ]
        ];
        $styleRowGrey = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' =>  [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => 'FF808080') 
            ]
        ];

        foreach($this->tabSerieTot as $elementPaysageId => $tabElement){
            foreach($tabElement as $idSerie => $tabEvol){
                foreach($tabEvol as $idEvol => $evol){
                    if(isSet($evol['nom'])){
                        switch($evol['nom']){
                            case 'Stabilité':
                                $col = 0;
                                break;
                            case 'Apparition':
                                $col = 1;
                                break;
                            case 'Disparition':
                                $col = 2;
                                break;
                            case 'Augmentation':
                                $col = 3;
                                break;
                            case 'Diminution':
                                $col = 4;
                                break;
                            case "Changement d'apparence":
                                $col = 5;
                                break;
                        };
                        
                    }
                    $myWorkSheet->setCellValue($this->getLetterForXls($colStart + $col) . $rowStart, $evol['count']);
                }
                $myWorkSheet->getStyle($this->getLetterForXls($colStart) . $rowStart . ":" . $this->getLetterForXls($colStart + 5) . $rowStart)
                ->applyFromArray($styleRowTotaux);
                $rowStart ++;
            }
            $myWorkSheet->getStyle($this->getLetterForXls($colStart) . $rowStart . ":" . $this->getLetterForXls($colStart + 5) . $rowStart)
            ->applyFromArray($styleRowGrey);
            $rowStart ++;
        }
    }

    private function getLetterForXls($col){
        $alphabet = array_combine(range(0,25), range('A', 'Z'));
        if($col > 25){
            $modulo = ($col % 26);
            $reste = intdiv( $col, 26) -1;
            $lettre = $alphabet[$reste] . $alphabet[$modulo];
        } else {
            $lettre = $alphabet[$col];
        }
        return $lettre;
    }

    /**
     * @Route("public/generation/panier/ficheterrain", name="generateFicheTerrainPanier")
     * @return JsonResponse
     */
    public function generateFicheTerrainPanier(){
        /*ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '300');
        $em = $this->getDoctrine()->getManager();
        $serieSelection = $this->get('session')->get('panier');*/

        return new JsonResponse(array(
            'status' => 'ok'
        ));

    }
}
