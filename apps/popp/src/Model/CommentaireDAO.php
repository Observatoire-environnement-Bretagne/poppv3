<?php
namespace App\Model;

use App\Entity\Commentaire;
use App\Entity\Document;
use App\Entity\LGestionnaireOpp;
use App\Entity\LienExterne;
use App\Entity\LPhotoThesaurus;
use App\Entity\LPhotoThesaurusFacultatif;
use App\Entity\LSerieAxeThematic;
use App\Entity\LSerieUnitePaysagereLocale;
use App\Entity\LThesaurusEvolution;
use App\Entity\LThesaurusFacultatifEvolution;
use App\Entity\Photo;
use App\Entity\Son;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\DocumentRef;
use Psr\Container\ContainerInterface;

class CommentaireDAO {
    // Référence au service Doctrine
    private $em;
    
    // Constructeur pour permettre au Service Container
    // de nous donner le service Doctrine
    public function __construct(EntityManagerInterface $em, ContainerInterface $container) {
        //$this->doctrine = $doctrine;
        $this->em = $em;
        $this->container = $container;
    }


    public function getCommentairesNonVus($isAdmin, $user){
        
        $em = $this->em;

        if($isAdmin){
            $commentaires = $em->getRepository(Commentaire::class)->findBy(array('commentaireEtat' => 0));
        }else{
            $commentaires = [];
            $allCommentaires = $em->getRepository(Commentaire::class)->findBy(array('commentaireEtat' => 0));
            foreach($allCommentaires as $commentaire){
                $photoCommentaire = $commentaire->getCommentairePhoto();
                if($photoCommentaire){
                    $serieCommentaire = $photoCommentaire->getPhotoSerie();
                    if($serieCommentaire){
                        $commentaireGestionnaire = $em->getRepository(LGestionnaireOpp::class)->findBy(array('lGoOpp' => $serieCommentaire->getSerieOpp(), 'lGoUsers' => $user));
                        if($commentaireGestionnaire){
                            $commentaires[] = $commentaire;
                        }
                    }
                }
            }
        }
        return count($commentaires);
    }

}