<?php
namespace App\Controller;

use App\Entity\Opp;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends Controller
{
    // Ajouter l'objet de requête Request $request
    public function loginAction(Request $request)
    {/*
        // Chargement des services
        $pParametreDAO = $this->get('pparametre.dao');
        //A déplacer au lancement de l'application
        $pParametreDAO->setParamatersUsers();
        
    	// Récupération de la session de l'utilisateur
    	$session = $request->getSession();
    	$erreur = "";
    	// on vérifie s'il y a des erreurs d'authentification dans la requête
    	if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
    		//$erreur = $request->attributes->get(Security::AUTHENTICATION_ERROR);
    		$erreur = "Identification invalide";
    	}
    	// Une erreur liée à la session
    	else if($session->get(Security::AUTHENTICATION_ERROR) != "") {
    		$erreur = "Identification invalide";
    		// on supprime l'erreur
    		$session->remove(Security::AUTHENTICATION_ERROR);
    	}
    	
        return $this->render(
        		'BoFormationBundle:Login:login.html.twig',
        		// on transmet à la vue :
        		//		- le message d'erreur
        		//		- le dernier nom d'utilisateur saisi
        		array(
            		'erreur' => $erreur,
                        'messageActivation' => "",
                        'last_login' => $session->get(Security::LAST_USERNAME)
        		)
        	);*/
		
			return new JsonResponse(array('status' => 'ok'));
    }
    
    public function logoutAction()
    {
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $em = $this->getDoctrine()->getManager();
        $opps = $em->getRepository(Opp::class)->findBy(array(), array('oppNom' => 'ASC'));;
        // Chargement des services
        $pParametreDAO = $this->get('parametre.dao');
        //A déplacer au lancement de l'application
		$pParametreDAO->setGlobalParamaters();
		
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', 
            ['last_username' => $lastUsername, 'error' => $error, 'opps' => $opps]
        );
    }

}