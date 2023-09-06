<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Photo::class);
        $this->entityManager = $entityManager;
    }

    
    public function findBySerieAndYear($idSerie, $annee)
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = "
                SELECT  photo_id 
                from photo
                WHERE photo_serie_id = $idSerie
                AND EXTRACT(YEAR FROM photo_date_prise) = '$annee' 
            ";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $result->fetchAllAssociative();
    }


}
