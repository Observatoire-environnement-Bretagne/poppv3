<?php
namespace App\Model;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class FileManagerDAO {
    // Référence au service Doctrine
    private $em;
    
    // Constructeur pour permettre au Service Container
    // de nous donner le service Doctrine
    public function __construct(EntityManagerInterface $em, ContainerInterface $container) {
        //$this->doctrine = $doctrine;
        $this->em = $em;
        $this->container = $container;
    }

    public function removeFile($file){
        $session = $this->container->get('session');
        $parametres = $session->get('parameters');
        //print_r($parametres["PATH_FOLDER_FILES"] . "\\" . $file->getFileManagerUri()) ;
        $filePath =  $parametres["PATH_FOLDER_FILES"] . "\\" . $file->getFileManagerUri();
        
        //Suppression du fichier
        if (is_file($filePath)){
            unlink($filePath);
        }
        $this->em->remove($file);
        $this->em->flush();
    }
}