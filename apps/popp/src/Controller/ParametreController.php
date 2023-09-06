<?php

namespace App\Controller;

use App\Entity\Parametre;
use App\Entity\PorteurOpp;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ParametreController extends Controller
{
    /*private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }*/
    // Chargement des données de la table paramètre en session
    
    /**
     * @Route("/public/loadParameters", name="load_parameters")
     */
    
    /**
     * @Route("admin/get/parametres", name="getParametres")
     * @return Response
     */

    public function getParametres()
    {
        $em = $this->getDoctrine()->getManager();
        $parametres = $em->getRepository(Parametre::class)->findAll();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        return $this->render("parametre/parametres.html.twig", [
            'parametres' => $parametres,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]); 
    }
    
    /**
     * @Route("admin/update/parametre/{idParametre}", name="updateParametre")
     * @return JsonResponse
     */
    public function updateParametre($idParametre, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $parametre = $em->getRepository(Parametre::class)->find($idParametre);

        $parametreVal = $request->get('parametreVal');
        $parametre->setPrmValeur($parametreVal);
        //On enregistre les modifs
        $em->persist($parametre);
        $em->flush();
        
        return new JsonResponse(array(
            'status' => 'ok'));
    }
    
}