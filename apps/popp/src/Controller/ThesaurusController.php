<?php

namespace App\Controller;

//use App\Entity\The;

use App\Entity\EvolutionPaysage;
use App\Entity\LPhotoThesaurus;
use App\Entity\LPhotoThesaurusFacultatif;
use App\Entity\LThesaurusEvolution;
use App\Entity\LThesaurusFacultatifEvolution;
use App\Entity\Photo;
use App\Entity\PorteurOpp;
use App\Entity\ThesaurusTree;
use App\Entity\ThesaurusTreeFacultatif;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ThesaurusController extends Controller
{
    
    /**
     * @Route("get/tree/thesaurus/{firstPhoto}", name="getTreeThesaurus")
     * @param string $firstPhoto firstPhoto
     * @return JsonResponse
     */
    public function getTreeThesaurus($firstPhoto, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $thesaurusTree = $em->getRepository(ThesaurusTree::class)->findAll();
        $evolutionsPaysage = $em->getRepository(EvolutionPaysage::class)->findAll();

        foreach($thesaurusTree as $thesaurus){
            $parentId = $thesaurus->getThesaurusTreeParentId();
            if($thesaurus->getThesaurusTreeParentId() == 0){
                $parentId = "#";
            }
            $tabThesaurus[] = array(
                'id' => $thesaurus->getThesaurusTreeId(),
                'text' => $thesaurus->getThesaurusTreeNom(),
                'parent' => $parentId
            );
            $tabChildren[] = $thesaurus->getThesaurusTreeId();
            $tabParents[] = $parentId;
            //$thesaurus[$thesaurus->getThesaurusTreeParentId()]
        }
        foreach($tabChildren as $child){
            if(!in_array($child, $tabParents)){
                foreach($evolutionsPaysage as $evolutionPaysage){
                    if($firstPhoto == 'true' && $evolutionPaysage->getEvolutionPaysageNom() == "Stabilité"){
                        $tabThesaurus[] = array(
                            'id' => $child . '_' . $evolutionPaysage->getEvolutionPaysageId(),
                            'text' => 'Présence',
                            'parent' => $child
                        );
                    }else if($firstPhoto == 'false'){
                        $tabThesaurus[] = array(
                            'id' => $child . '_' . $evolutionPaysage->getEvolutionPaysageId(),
                            'text' => $evolutionPaysage->getEvolutionPaysageNom(),
                            'parent' => $child
                        );
                    }
                }
            }
        }
        return new JsonResponse($tabThesaurus);
    }
    
    /**
     * @Route("get/tree/thesaurusexistant", name="getTreeThesaurusExistant")
     * @return JsonResponse
     */
    public function getTreeThesaurusExistant(Request $request)
    {
        //TODO - A FINIR
        $em = $this->getDoctrine()->getManager();
        $repoLPhotoThesaurus = $em->getRepository(LPhotoThesaurus::class);
        $repoLThesaurusEvolution = $em->getRepository(LThesaurusEvolution::class);
        $repoLPhotoThesaurusFacultatif = $em->getRepository(LPhotoThesaurusFacultatif::class);
        $repoLThesaurusFacultatifEvolution = $em->getRepository(LThesaurusFacultatifEvolution::class);

        $tabTree = array();
        //On récupère tous les thésaurus utilisés
        $photos = $em->getRepository(Photo::class)->findAll();
        foreach($photos as $photo){
            $lPhotoThesaurus = $repoLPhotoThesaurus->findBy(array('lPtPhoto' => $photo));
            foreach($lPhotoThesaurus as $photoThesaurus){
                $lThesaurusEvolution = $repoLThesaurusEvolution->findOneBy(array('lTePhotoThesaurus' => $photoThesaurus));
                if($lThesaurusEvolution){
                    $tabTree[] = array('thesaurus' => $photoThesaurus->getLPtThesaurus()->getThesaurusTreeId(), 'evolution' => $lThesaurusEvolution->getLTeEvolution()->getEvolutionPaysageId());
                }
            }
            
            $lPhotoThesaurusFacultatif = $repoLPhotoThesaurusFacultatif->findBy(array('lPtfPhoto' => $photo));
            foreach($lPhotoThesaurusFacultatif as $photoThesaurusFacultatif){
                $lThesaurusFacultatifEvolution = $repoLThesaurusFacultatifEvolution->findOneBy(array('lTfePhotoThesaurus' => $photoThesaurusFacultatif));
                if($lThesaurusFacultatifEvolution){
                    $tabTree[] = array('thesaurus' => 'f-'.$photoThesaurusFacultatif->getLPtfThesaurus()->getThesaurusTreeFacultatifId(), 'evolution' => $lThesaurusFacultatifEvolution->getLTfeEvolution()->getEvolutionPaysageId());
                }
            }
        }

        /*foreach($tabTree as $treeValid){
            $treeValid
        }*/

        /* On récupère tous les thésaurus */
        $thesaurusTree = $em->getRepository(ThesaurusTree::class)->findAll();
        $evolutionsPaysage = $em->getRepository(EvolutionPaysage::class)->findAll();

        foreach($thesaurusTree as $thesaurus){
            $parentId = $thesaurus->getThesaurusTreeParentId();
            if($thesaurus->getThesaurusTreeParentId() == 0){
                $parentId = "#";
            }

            $tabThesaurus[/*$thesaurus->getThesaurusTreeId()*/] = array(
                'id' => $thesaurus->getThesaurusTreeId(),
                'text' => $thesaurus->getThesaurusTreeNom(),
                'parent' => $parentId
            );
            $tabChildren[] = $thesaurus->getThesaurusTreeId();
            $tabParents[] = $parentId;
            //$thesaurus[$thesaurus->getThesaurusTreeParentId()]
        }
        

        $thesaurusTreeFacultatif = $em->getRepository(ThesaurusTreeFacultatif::class)->findAll();

        foreach($thesaurusTreeFacultatif as $thesaurusFacultatif){
            $parentId = $thesaurusFacultatif->getThesaurusTreeFacultatifParentId();
            if($thesaurusFacultatif->getThesaurusTreeFacultatifParentId() == 0){
                $parentId = "#";
            }else{
                $parentId = 'f-' . $thesaurusFacultatif->getThesaurusTreeFacultatifParentId();
            }
            $thesaurusFacultatifId = 'f-' . $thesaurusFacultatif->getThesaurusTreeFacultatifId();

            $tabThesaurus[] = array(
                'id' => $thesaurusFacultatifId,
                'text' => $thesaurusFacultatif->getThesaurusTreeFacultatifNom(),
                'parent' => $parentId,
                //"disabled" => true
            );
            $tabChildren[] = $thesaurusFacultatifId;
            $tabParents[] = $parentId;
            //$thesaurus[$thesaurus->getThesaurusTreeParentId()]
        }

        //print_r($tabChildren);
        //print_r($tabParents);
        //print_r($tabThesaurus);// => Array ( [0] => Array ( [id] => 1 [text] => végétation [parent] => # ) [1] => Array ( [id] => 2 [text] => infrastructures et réseaux [parent] => # ) [2] => Array ( [id] => 3 [text] => eau continentale [parent] => # ) [3] => Array ( [id] => 4 [text] => bâti [
        $tabParentToCheck = [];
        foreach($tabChildren as $child){
            if(!in_array($child, $tabParents)){
                //Si le thésaurus n'est pas un parent => bout de branche 
                $branchIsValid = false;
                foreach($evolutionsPaysage as $evolutionPaysage){
                    $isValid = false;
                    foreach($tabTree as $treeValid){
                        //Si l'enfant correspond à un thésaurus d'evolution 
                        if ($treeValid['thesaurus'] == $child && $treeValid['evolution'] == $evolutionPaysage->getEvolutionPaysageId()){
                            $isValid = true;
                            $branchIsValid = true;
                        }
                    }
                    //Du coup on est bien dans la case à cocher 
                    if($isValid == true){
                        $tabThesaurus[/*$child . '_' . $evolutionPaysage->getEvolutionPaysageId()*/] = array(
                            'id' => $child . '_' . $evolutionPaysage->getEvolutionPaysageId(),
                            'text' => $evolutionPaysage->getEvolutionPaysageNom(),
                            'parent' => $child
                        );
                    }
                }
                if($branchIsValid == false){
                    for($i = 0; $i < count($tabThesaurus); $i++){
                        if($tabThesaurus[$i]['id'] == $child){
                            $tabParentToCheck[$tabThesaurus[$i]['parent']] = $tabThesaurus[$i]['id'];
                            unset($tabThesaurus[$i]);
                            $tabThesaurus = array_values($tabThesaurus);

                        }
                    }
                }
            }
        }

        //print_r($tabThesaurus); => Array ( [0] => Array ( [id] => 1 [text] => végétation [parent] => # ) [1] => Array ( [id] => 2 [text] => infrastructures et réseaux [parent] => # ) [2] => Array ( [id] => 3 [text] => eau continentale
        //print_r($tabParentToCheck);
        //On vire les niveaux 2
        $tabRootToCheck = [];
        //On commente tous ca, ca génère des incohérences
        //print_r($tabThesaurus);
        //print_r($tabParentToCheck);
        foreach( $tabParentToCheck as $thesaurusId => $thesaurusChildId){
            //echo $thesaurusId . "<br>"; 
            $findBranch = false;
            $branchIsValid = false;
            $branchNum = 0;
            for($i = 0; $i < count($tabThesaurus); $i++){
                if($tabThesaurus[$i]['parent'] == $thesaurusId){
                    $branchIsValid = true;
                }
                if($tabThesaurus[$i]['id'] == $thesaurusId){
                    $findBranch = true;
                    $branchNum = $i;
                }
            }
            if($branchIsValid == false && $findBranch){
                //print_r($tabThesaurus[$branchNum]); 
                //print_r($tabThesaurus[$branchNum]);
                // MET 03/09/2020 ajout suite au maj du script de livraison 
                //if($tabThesaurus[$branchNum]['parent'] != "#"){
                    //echo '<br> suppressions : ' . $tabThesaurus[$branchNum]['parent'] . ' - ' . $tabThesaurus[$branchNum]['id'] .'<br>';
                    $tabRootToCheck[$tabThesaurus[$branchNum]['parent']] = $tabThesaurus[$branchNum]['id'];
                    unset($tabThesaurus[$branchNum]);
                    $tabThesaurus = array_values($tabThesaurus);
                //}
            }
        }
        //print_r($tabThesaurus);
        //On vire les niveaux 1
        foreach( $tabRootToCheck as $thesaurusId => $thesaurusChildId){
            $branchIsValid = false; 
            $findBranch = false;
            $branchNum = 0;
            for($i = 0; $i < count($tabThesaurus); $i++){
                if($tabThesaurus[$i]['parent'] == $thesaurusId){
                    $branchIsValid = true;
                }
                if($tabThesaurus[$i]['id'] == $thesaurusId){
                    $findBranch = true;
                    $branchNum = $i;
                }
            }
            if($branchIsValid == false && $findBranch){
                unset($tabThesaurus[$branchNum]);
                $tabThesaurus = array_values($tabThesaurus);
            }
        }

        return new JsonResponse(($tabThesaurus));
        //return new JsonResponse(array_values($tabThesaurus));
    }
    
    /**
     * @Route("gestion/thesaurusfacultalif", name="thesaurusFacultatif")
     * @return Response
     */

    public function getThesaurusFacultatif()
    {
        $em = $this->getDoctrine()->getManager();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        return $this->render("thesaurus/thesaurus_facultatif.html.twig", [
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments]); 
    }
    
    /**
     * @Route("get/tree/thesaurusFacultatif", name="getTreeThesaurusFacultatif")
     * @return JsonResponse
     */
    public function getTreeThesaurusFacultatif(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $thesaurusTree = $em->getRepository(ThesaurusTreeFacultatif::class)->findAll();

        $tabThesaurusFinal[] = array(
            'id' => 0,
            'text' => "base",
            'parent' => '#',
            "type" => 'root'
        );
        $tabChildren = array();
        foreach($thesaurusTree as $thesaurus){
            $parentId = $thesaurus->getThesaurusTreeFacultatifParentId();
            /*if($thesaurus->getThesaurusTreeFacultatifParentId() == 0){
                $parentId = "#";
            }*/
            $tabThesaurus[$thesaurus->getThesaurusTreeFacultatifId()] = array(
                'id' => $thesaurus->getThesaurusTreeFacultatifId(),
                'text' => $thesaurus->getThesaurusTreeFacultatifNom(),
                'parent' => $parentId
            );
            $tabChildren[] = $thesaurus->getThesaurusTreeFacultatifId();
            $tabParents[] = $parentId;
            //$thesaurus[$thesaurus->getThesaurusTreeParentId()]
        }
        foreach($tabChildren as $child){
            $node = $tabThesaurus[$child];
            if(!in_array($child, $tabParents)){
                $node['type'] = 'file';
            }
            $tabThesaurusFinal[] = $node;
        }
        return new JsonResponse($tabThesaurusFinal);
    }

    
    /**
     * @Route("gestion/thesaurusFacultatif/save", name="saveTreeThesaurusFacultatif")
     * @return JsonResponse
     */
    public function saveTreeThesaurusFacultatif(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repoThesaurusTree = $em->getRepository(ThesaurusTreeFacultatif::class);
        $thesaurusTreeAll = $repoThesaurusTree->findAll();
        $tree = $request->request->get('tree');
        //On supprime avant de recréer
        foreach($thesaurusTreeAll as $thesarusFacultatif){
            $isDeleted = true;
            foreach($tree as $node){
                if($thesarusFacultatif->getThesaurusTreeFacultatifId() == $node['id']){
                    $isDeleted = false;
                }
            }
            if($isDeleted){
                $lPhotoThesaurusFacultatifs = $em->getRepository(LPhotoThesaurusFacultatif::class)->findBy(array('lPtfThesaurus' => $thesarusFacultatif));
                foreach($lPhotoThesaurusFacultatifs as $lPhotoThesaurusFacultatif){
                    $lThesaurusFacultatifEvolutions = $em->getRepository(LThesaurusFacultatifEvolution::class)->findBy(array('lTfePhotoThesaurus' => $lPhotoThesaurusFacultatif));
                    foreach($lThesaurusFacultatifEvolutions as $lThesaurusFacultatifEvolution){
                        $em->remove($lThesaurusFacultatifEvolution);
                        $em->flush();
                }
                    $em->remove($lPhotoThesaurusFacultatif);
                    $em->flush();
                }
                //TODO - Check si le thésaurus est lié
                $em->remove($thesarusFacultatif);
            }
        }
        $em->flush();

        $tabLinkId = [];
        foreach($tree as $node){
            $thesaurusTree = null;
            if(isset($node['type'])){
                if ($node['type'] == 'root'){
                    continue;
                }
            }
            if (is_numeric($node['id'])){
                $thesaurusTree = $repoThesaurusTree->find($node['id']);
            }
            if($thesaurusTree){
                if(isSet($node['parent'])){
                    //Si le node existe déja, on le modifie
                    $thesaurusTree->setThesaurusTreeFacultatifNom($node['text']);
                    $thesaurusTree->setThesaurusTreeFacultatifParentId($node['parent']);
                    $em->persist($thesaurusTree);
                }else{
                    print_r($node) ;
                }
            }else{
                //Si le node n'existe pas, on le créer
                $thesaurusTree = new ThesaurusTreeFacultatif();
                $thesaurusTree->setThesaurusTreeFacultatifNom($node['text']);
                if(isSet($tabLinkId[$node['parent']])){
                    $node['parent'] = $tabLinkId[$node['parent']];
                }
                $thesaurusTree->setThesaurusTreeFacultatifParentId($node['parent']);
                $em->persist($thesaurusTree);
                $tabLinkId[$node['id']] = $thesaurusTree->getThesaurusTreeFacultatifId();
            }

        }
        $em->flush();
        return new JsonResponse(array("status" => "ok"));
    }
    
    
    
    /**
     * @Route("get/tree/thesaurusFacult/{firstPhoto}", name="getTreeThesaurusFacult")
     * @param string $firstPhoto firstPhoto
     * @return JsonResponse
     */
    public function getTreeThesaurusFacult($firstPhoto, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $thesaurusTree = $em->getRepository(ThesaurusTreeFacultatif::class)->findAll();
        $evolutionsPaysage = $em->getRepository(EvolutionPaysage::class)->findAll();
        $tabChildren = $tabThesaurus = array();
        foreach($thesaurusTree as $thesaurus){
            $parentId = $thesaurus->getThesaurusTreeFacultatifParentId();
            if($thesaurus->getThesaurusTreeFacultatifParentId() == 0){
                $parentId = "#";
            }
            $tabThesaurus[] = array(
                'id' => $thesaurus->getThesaurusTreeFacultatifId(),
                'text' => $thesaurus->getThesaurusTreeFacultatifNom(),
                'parent' => $parentId
            );
            $tabChildren[] = $thesaurus->getThesaurusTreeFacultatifId();
            $tabParents[] = $parentId;
            //$thesaurus[$thesaurus->getThesaurusTreeParentId()]
        }
        foreach($tabChildren as $child){
            if(!in_array($child, $tabParents)){
                foreach($evolutionsPaysage as $evolutionPaysage){
                    if($firstPhoto == 'true' && $evolutionPaysage->getEvolutionPaysageNom() == "Stabilité"){
                        $tabThesaurus[] = array(
                            'id' => $child . '_' . $evolutionPaysage->getEvolutionPaysageId(),
                            'text' => 'Présence',
                            'parent' => $child
                        );
                    }else if($firstPhoto == 'false'){
                        $tabThesaurus[] = array(
                            'id' => $child . '_' . $evolutionPaysage->getEvolutionPaysageId(),
                            'text' => $evolutionPaysage->getEvolutionPaysageNom(),
                            'parent' => $child
                        );
                    }
                }
            }
        }
        return new JsonResponse($tabThesaurus);
    }
}