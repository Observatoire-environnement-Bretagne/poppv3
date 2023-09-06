<?php

namespace App\Controller;

use App\Entity\Apropos;
use App\Entity\FileManager;
use App\Entity\PorteurOpp;
use App\Repository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AproposController extends Controller
{
    
    /**
     * @Route("show/apropos", name="showApropos")
     * @return Response
     */
    public function showApropos()
    {
        // Chargement des services
        $ParametreDAO = $this->get('parametre.dao');
        //A déplacer au lancement de l'application
        $ParametreDAO->setGlobalParamaters();
        
        $em = $this->getDoctrine()->getManager();
        $apropos = $em->getRepository(Apropos::class)->findBy(array(), array('aproposNumOrdre' => 'ASC'));

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("public/apropos.html.twig", [
            'apropos' => $apropos,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    /**
     * @Route("admin/update/apropos/{aproposId}", name="updateApropos")
     * @return Response
     */
    public function updateApropos(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $aproposId = $request->request->get('aproposId');
        
        $apropos = $em->getRepository(Apropos::class)->find($aproposId);
        $apropos->setAproposTitre($request->request->get('aproposTitre'));
        $apropos->setAproposDescription($request->request->get('aproposDesc'));
        $apropos->setAproposDocUrl($request->request->get('aproposDocUrlValue'));
        $apropos->setAproposDocLabel($request->request->get('aproposDocLabelValue'));
        $aproposNumOrdre = $request->request->get('aproposNumOrdre');
        if($aproposNumOrdre == ""){
            $aproposNumOrdre = 0;
        }
        $apropos->setAproposNumOrdre($aproposNumOrdre);
        
        $em->persist($apropos);
        $em->flush();      
        
        return new JsonResponse(array(
            'aproposId' => $aproposId,
            'apropos'   => $apropos,
            'status' => "ok"
        ));
    }
      
    /**
     * @Route("admin/create/apropos", name="createApropos")
     * @return Response
     */
    public function createApropos(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $apropos = new Apropos();

        $apropos->setAproposTitre($request->request->get('aproposTitre'));
        $apropos->setAproposDescription($request->request->get('aproposDesc'));
        $apropos->setAproposDocUrl($request->request->get('aproposDocUrlValue'));
        $apropos->setAproposDocLabel($request->request->get('aproposDocLabelValue'));
        
        $aproposNumOrdre = $request->request->get('aproposNumOrdre');
        if($aproposNumOrdre == ""){
            $aproposNumOrdre = 0;
        }
        $apropos->setAproposNumOrdre($aproposNumOrdre);

        $em->persist($apropos);
        $em->flush();
        
        return  new JsonResponse(array(
            'status' => "ok",
            'apropos' => $apropos
        ));
    }
    
        
    /**
     * @Route("admin/remove/apropos/{aproposId}", name="deleteApropos")
     * @return Response
     */
    public function deleteApropos(string $aproposId)
    {
        $em = $this->getDoctrine()->getManager();
        $apropos = $em->getRepository(Apropos::class)->find($aproposId);

        $em->remove($apropos);
        $em->flush($apropos);
       
        return  new JsonResponse(array(
            'status' => "ok"
        ));
    }
            
    /**
     * @Route("contact/apropos/", name="contactApropos")
     * @return Response
     */
    public function contactApropos(Request $request, \Swift_Mailer $mailer)
    {
        //paramètres golobaux
        $parameters = $this->get('session')->get('parameters');
        
        $expediteurNom = $request->request->get('expediteurNom');
        $expediteurMail = $request->request->get('expediteurMail');
        $expediteurTel = $request->request->get('expediteurTel');
        $expediteurMsg = $request->request->get('expediteurMsg');

        $expediteurMsg = "Message de $expediteurMail (tel : $expediteurTel) : \n\n" . $expediteurMsg;
        /*ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        $from = $parameters['EMAIL_EXPEDITEUR'];
        $to = 'matthieu.etourneau@gmail.com';
        $subject = $expediteurMsg;
        $message = 'Prise de contact de ' . $expediteurNom . " depuis la rubrique A propos de la POPP Breizh";
        $headers = "De :" . $from;
        mail($to,$subject,$message, $headers);*/
        /*print_r(error_get_last());*/
        //echo "L'email a été envoyé.";
        $message = (new \Swift_Message('Prise de contact de ' . $expediteurNom . " depuis la rubrique A propos de la POPP Breizh"))
            ->setFrom($parameters['EMAIL_EXPEDITEUR'])
            //->setTo(['matthieu.etourneau@gmail.com'])
            ->setTo($parameters['EMAIL_DESTINATAIRE'])
            ->setBody($expediteurMsg)
        ;

        $mailer->send($message);

        
        return  new JsonResponse(array(
            'status' => "ok"
        ));
    }
}
