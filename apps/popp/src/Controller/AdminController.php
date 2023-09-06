<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Serie;
use App\Entity\Parametre;
use Symfony\Component\HttpFoundation\JsonResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_page")
     * @return Response
     */
    public function default()
    {
        return $this->render("public.html.twig");
//        return $this->render("popp.html.twig");
    }

    /**
     * @Route("admin/blank", name="admin_default")
     * @return Response
     */
    public function index()
    {
        return $this->render("blank.html.twig");
    }
    
    /**
     * @Route("/admin/export_all_series", name="export_all_series")
     */
    public function exportAllSeries()
    {
        $spreadsheet = new Spreadsheet();
        $em = $this->getDoctrine()->getManager();
        $series = $em->getRepository(Serie::class)->findAll();
        $paramURL = $em->getRepository(Parametre::class)->findOneBy(array("prmCode" => "URL_POPP"));
        $parameters = $this->get('session')->get('parameters');

        $useCol = ["A", "B", "C", "D", "E", "F"];
        for ($i = 0; $i <= (count($useCol)-1) ; $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($useCol[$i])->setAutoSize(true);
        }
        
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID de la série');
        $sheet->setCellValue('B1', 'Coordonnée X');
        $sheet->setCellValue('C1', 'Coordonnée Y');
        $sheet->setCellValue('D1', 'Code INSEE');
        $sheet->setCellValue('E1', 'Nom de la série');
        $sheet->setCellValue('F1', 'URL');

        $xlsRow = 1;
        foreach ($series as $serie){
            $xlsRow++;
            $sheet->setCellValue("A". $xlsRow, $serie->getSerieId());
            $sheet->setCellValue("B". $xlsRow, $serie->getSerieGeomX());
            $sheet->setCellValue("C". $xlsRow, $serie->getSerieGeomY());
            if ($serie->getSerieCommune()){
                $sheet->setCellValue("D". $xlsRow, $serie->getSerieCommune()->getCommuneInsee());
            }
            $sheet->setCellValue("E". $xlsRow, $serie->getSerieTitre());
            $sheet->setCellValue("F". $xlsRow, $paramURL->getPrmValeur() . "public/get/serie/" . $serie->getSerieId());
            
        }
        $writer = new Xlsx($spreadsheet);
        
        $temp_file = tempnam(sys_get_temp_dir() , 'export_series.xlsx');
        //$writer->save($parameters['PATH_FOLDER_FILES'] . "/export_serie/" . 'export_series.xlsx');
        $writer->save($temp_file);
        
        return $this->file($temp_file, 'export_series.xlsx', ResponseHeaderBag::DISPOSITION_INLINE);
        return new JsonResponse(array(
            'status' => 'ok'
        ));
    }

}