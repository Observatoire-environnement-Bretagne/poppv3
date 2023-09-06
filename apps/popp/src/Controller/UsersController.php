<?php

namespace App\Controller;

use DateTime;
use Swift_Message;
use App\Entity\Opp;
use App\Repository;
use App\Entity\Serie;
use App\Entity\Users;

use App\Entity\PorteurOpp;

use App\Entity\LFournisseurOpp;
use App\Entity\LGestionnaireOpp;
use App\Entity\LSerieAxeThematic;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UsersController extends Controller
{
    /**
     * @Route("create/user", name="createUser")
     * @return JsonResponse
     */
    public function createUser(Request $request)
    {
        
        //Chargement des variables globales
        // Chargement des services
        $ParametreDAO = $this->get('parametre.dao');
        //A déplacer au lancement de l'application
        $ParametreDAO->setGlobalParamaters();

        $parameters = $this->get('session')->get('parameters');
        // Récupération des informations du user
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $sexe = $request->request->get('sexe');
        $email = $request->request->get('email');
        $mdp = $request->request->get('mdp');
        $adresse = $request->request->get('adresse');
        $code_postal = $request->request->get('code_postal');
        $ville = $request->request->get('ville');
        $telephone = $request->request->get('telephone');
        $opp_id = $request->request->get('opp_id');
        $typeCompte = $request->request->get('typeCompte');

        //echo "email : " . $email;
        $user = new Users();
        $user->setEmail($email);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setSexe($sexe);
        $user->setRoles( array('ROLE_USER') );
        $user->setAdresse($adresse);
        $user->setCodePostal($code_postal);
        $user->setVille($ville);
        $user->setTelephone($telephone);
        
        // Date de mise à jour
        $dateMaj = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
        $user->setDatederncnx($dateMaj);

        $passwordEncoder = $this->container->get('security.password_encoder');
        $passwordCrypted = $passwordEncoder->encodePassword($user, $mdp);
        $user->setPassword($passwordCrypted);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        if($typeCompte == 'isUser'){
            $message = (new Swift_Message('[POPP] Bienvenue dans la POPP Sud Ouest'))
            ->setFrom($parameters['EMAIL_EXPEDITEUR'])
            ->setTo($email)
            ->setBody(
                $this->container->get('templating')->render(
                    'mail/confirmationInscription.html.twig',
                    array('user' => $user)
                ),
                'text/html'
            );
            $this->container->get('mailer')->send($message);
        }else{
            if($opp_id != ""){
                $opp = $this->getDoctrine()->getRepository(Opp::class)->find($opp_id);
                $lFournisseurOpp = new LFournisseurOpp();
                $lFournisseurOpp->setLFoUsers($user);
                $lFournisseurOpp->setLFoOpp($opp);
                $em->persist($lFournisseurOpp);
                $em->flush();
    
                //on envoi un mail de confirmation aux gestionnaires de l'OPP
                $lGestionnairesOpp = $this->getDoctrine()->getRepository(LGestionnaireOpp::class)->findBy(array('lGoOpp' => $opp));
                foreach($lGestionnairesOpp as $lGestionnaireOpp ){
                    $email = $lGestionnaireOpp->getLGoUsers()->getEmail();
                    
                    $encrypted = base64_encode($user->getId());
                    //a tester
                    $message = (new Swift_Message('[POPP] Demande d\'inscription'))
                    ->setFrom($parameters['EMAIL_EXPEDITEUR'])
                    ->setTo($email)
                    ->setBody(
                        $this->container->get('templating')->render(
                            'mail/demandeInscription.html.twig',
                            array('user' => $user, 'id' => $encrypted, 'url' => $parameters['URL_POPP'])
                        ),
                        'text/html'
                    );
                    $this->container->get('mailer')->send($message);
                }
                $message = (new Swift_Message('[POPP] Bienvenue dans la POPP Sud Ouest'))
                ->setFrom($parameters['EMAIL_EXPEDITEUR'])
                ->setTo($email)
                ->setBody(
                    $this->container->get('templating')->render(
                        'mail/accuseInscription.html.twig',
                        array('user' => $user)
                    ),
                    'text/html'
                );
                $this->container->get('mailer')->send($message);
                //$em->getRepository(Users::class)->findBy("")
            }
    
            //TODO => Envoie de mail au super ADMIN -> Pas de super Admin
            //A tester
            /*$tabSuperAdmin = $em->getRepository(Users::class)->findByRole('ROLE_SUPER_ADMIN');
            //print_r($tabSuperAdmin);
            foreach($tabSuperAdmin as $superAdmin){
                $email = $superAdmin['email'];
                
                $encrypted = base64_encode($user->getId());
                //a tester
                $message = (new Swift_Message('[POPP] Demande d\'inscription'))
                ->setFrom($parameters['EMAIL_EXPEDITEUR'])
                ->setTo($email)
                ->setBody(
                    $this->container->get('templating')->render(
                        'mail/demandeInscription.html.twig',
                        array('user' => $user, 'id' => $encrypted, 'url' => $parameters['URL_POPP'])
                    ),
                    'text/html'
                );
                $this->container->get('mailer')->send($message);
            }*/
    
            /*$tabAdmin = $em->getRepository(Users::class)->findByRole('ROLE_ADMIN');
            //print_r($tabSuperAdmin);
            foreach($tabAdmin as $admin){
                $email = $admin['email'];
                
                $encrypted = base64_encode($user->getId());
                //a tester
                $message = (new Swift_Message('[POPP] Demande d\'inscription'))
                ->setFrom($parameters['EMAIL_EXPEDITEUR'])
                ->setTo($email)
                ->setBody(
                    $this->container->get('templating')->render(
                        'mail/demandeInscription.html.twig',
                        array('user' => $user, 'id' => $encrypted, 'url' => $parameters['URL_POPP'])
                    ),
                    'text/html'
                );
                $this->container->get('mailer')->send($message);
            }
            
            $email = $admin['email'];*/
                
            //Pas de mail au compte configurer pour la reception des messages
            $encrypted = base64_encode($user->getId());
            //a tester
            $message = (new Swift_Message('[POPP] Demande d\'inscription'))
            ->setFrom($parameters['EMAIL_EXPEDITEUR'])
            ->setTo($parameters['EMAIL_DESTINATAIRE'])
            ->setBody(
                $this->container->get('templating')->render(
                    'mail/demandeInscription.html.twig',
                    array('user' => $user, 'id' => $encrypted, 'url' => $parameters['URL_POPP'])
                ),
                'text/html'
            );
            $this->container->get('mailer')->send($message);
        }

        //var_dump($encrypted); // eyJpdiI6Img2dStibzhWMGlTamFmakJDeUd0a1E9PSIsInZhbHVlIjoicnpwTWZmcTRDa1JXOVRuVGtudW5ldXkwanBIUlFBQ3I1czNwTjFib1dqWT0iLCJtYWMiOiI3OTBkYzg5YmE0MWFjYTMzMGU2N2NhMDY0MTRkZjc1ZGQ0MGM4MzM4MDI2N2FhYjk0YWE5Y2M5NDk5YTY2NGQzIn0=
        //var_dump(base64_decode($encrypted)); // hello world
        return new JsonResponse(array('status' => 'ok', 'userId' => $user->getId()));
    }

    /**
     * @Route("check/mail", name="checkMailBdd")
     * @return JsonResponse
     */
    public function checkMailBdd(Request $request){
        // Récupération de l'id du user
        $id = $request->request->get('id');
        $mail = $request->request->get('mail');

        //Récupérer le service DAO contact
        $repository = $this->getDoctrine()->getRepository(Users::class);
        
        $dataUser = "";
        foreach($repository->findBy(['email' => $mail]) as $user){
            $dataUser = [
                'userId' => $user->getId(),
                'userEmail' => $user->getEmail()
                ];
        }
        
        $output = array(
            'data' => array()
        );
        $output['data'][] = $dataUser;
        return new JsonResponse($output);
    }
    

    /**
     * @Route("user/reinitPassword", name="reinitPassword")
     * @return JsonResponse
     */

    public function reinitPasswordAction(Request $request)
    {
    	try {
            // Récupération de l'email du contact
            $email = $request->request->get('email');
            
            $repository = $this->getDoctrine()->getRepository(Users::class);
            //Chargement du user
            $users = $repository->findBy(['email' => $email]);
            //$users = $repository->findAll();
            
            // La fonction renvoie un tableau de contact, mais comme le mail est unique en bdd
            // alors on se position sur le 1er
            if (empty($users)){
                $erreur = [
                    'erreurCode' => 0,
                    'erreurMessage' => "le compte n'existe pas"
                ];
                $etat = 'erreur';
                $output = array('etat' => $etat, 'erreur' => $erreur);
                return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
            }else {
                $user = $users[0];
                $password = $this->generatePassword($user);
                
                $output = array(
                    'data' => array()
                );
                $output['data'][] = [
                    'userId' => $user->getId(),
                    'password' => $password
                ];
                $output['etat'] = 'succes';
                return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
            }
        } catch (\Exception $e) {
            $erreur = [
                'erreurCode' => $e->getCode(),
                'erreurMessage' => $e->getMessage()
            ];
            $etat = 'erreur';
            $output = array('etat' => $etat, 'erreur' => $erreur);

            return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
        }
    }

    private function generatePassword($user) {
    	try {
            $parameters = $this->get('session')->get('parameters');
            $password = $this->generateRandomString(8);
            // Mise à jour du mot de passe
            $passwordEncoder = $this->container->get('security.password_encoder');
            $passwordCrypted = $passwordEncoder->encodePassword($user, $password);

            //Mise à jour du user
            $user->setPassword($passwordCrypted);
            $user->setDatederncnx(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $email = $user->getEmail();

            //TODO Juste pour les tests
            //return $password;
            //a tester
            $message = (new Swift_Message('[POPP] Réinitialisation du mot de passe'))
                ->setFrom($parameters['EMAIL_EXPEDITEUR'])
                ->setTo($email)
                ->setBody(
                    $this->container->get('templating')->render(
                        'mail/mailReinitPassword.html.twig',
                        array('password' => $password, 'user' => $user)
                    ),
                    'text/html'
                );
            $this->container->get('mailer')->send($message);
        } catch (\Exception $e) {
            $erreur = [
                'erreurCode' => $e->getCode(),
                'erreurMessage' => $e->getMessage()
            ];
            $etat = 'erreur';
            $output = array('etat' => $etat, 'erreur' => $erreur);

            return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
        }
    }
    
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }   
    

    /**
     * @Route("user/changePassword", name="change_password")
     * @return JsonResponse
     */
    public function changePassword(){
        try {
            // Redirection vers la page de modificatin de mot de passe
            return $this->render(
                'login/changePassword.html.twig'
            );
   	    } catch (\Exception $e) {
	     	return $this->render(
                '500.html.twig', 
                /*array(
                'erreur' => $e 
            )*/
            );
   	    }
    }

    /**
     * @Route("user/updatePassword", name="update_password")
     * @return JsonResponse
     */
    public function updatePassword(Request $request)
    {
        // Récupération de l'id du user
        $id = $request->request->get('userId');
        // Date de mise à jour
        $dateMaj = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
        // Chargement du user
        $repository = $this->getDoctrine()->getRepository(Users::class);
        $user = $repository->find($id);

        // Use this to see if the user is logged in
        /*$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // (. Never check for the User object to see if they're logged in
        $user = $this->getUser();    */  
        
        // Mise à jour du mot de passe
        $passwordEncoder = $this->get('security.password_encoder');
        $passwordCrypted = $passwordEncoder->encodePassword($user, $request->request->get('userPwd'));
        $user->setPassword($passwordCrypted);
        // Mise à jour de la date de dernière connexion
        $user->setDatederncnx($dateMaj);
        //Mise à jour du user
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $output = array(
            'data' => array()
        );
        $output['data'][] = [
            'Id' => $user->getId(),
        ];

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
  
    }
    
    /**
     * @Route("admin/utilisateurs", name="getUsers")
     * @return Response
     */
    public function getUsers()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(Users::class)->findAll();

        $originalRoles = $this->getParameter('security.role_hierarchy.roles');

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        
        return $this->render("user/utilisateurs.html.twig", [
            'users' => $users,
            'roles' => $originalRoles,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    /**
     * @Route("gestion/delete/user/{idUser}", name="deleteUser")
     * @return Response
     */
    public function deleteUser(string $idUser)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->find($idUser);

        $LFournisseurOpps = $em->getRepository(LFournisseurOpp::class)->findBy(array('lFoUsers' => $user));
        $LGestionnaireOpps = $em->getRepository(LGestionnaireOpp::class)->findBy(array('lGoUsers' => $user));
        foreach($LFournisseurOpps as $LFournisseurOpp){
            $em->remove($LFournisseurOpp);
        }
        foreach($LGestionnaireOpps as $LGestionnaireOpp){
            $em->remove($LGestionnaireOpp);
        }

        $em->remove($user);
        $em->flush();
        return new JsonResponse(array('status' => 'ok'));
    }
    
    /**
     * @Route("gestion/modify/user/{idUser}", name="modifyUser")
     * @return Response
     */
    public function modifyUser(string $idUser, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($idUser == 'new'){
            $user_exist = $em->getRepository(Users::class)->findOneBy(array('email' => $request->request->get('email')));
            if($user_exist){
                return new JsonResponse(array('status' => 'error', 'message' => "L'email est déjà utilisé par un autre utilisateur"));
            }
            $user = new Users();
        }else{
            $user = $em->getRepository(Users::class)->find($idUser);
        }
        $user->setNom($request->request->get('userName'));
        $user->setPrenom($request->request->get('userPrenom'));
        $user->setSexe($request->request->get('userSexe'));
        $user->setEmail($request->request->get('email'));
        /* recette 31/08/2020 */
        $user->setAdresse($request->request->get('userAdresse'));
        $user->setCodepostal($request->request->get('userCodepostal'));
        $user->setVille($request->request->get('userVille'));
        $user->setTelephone($request->request->get('userTelephone'));

        if ($request->request->get('password') !== ""){
            $passwordEncoder = $this->container->get('security.password_encoder');
            $passwordCrypted = $passwordEncoder->encodePassword($user, $request->request->get('password'));
            $user->setPassword($passwordCrypted);
        }
        if($request->request->get('userRole') != ''){
            $user->setRoles(array($request->request->get('userRole')));
        }else{
            //TODO TEST création
            $user->setRoles(array('ROLE_USER'));
        }
        
        $em->persist($user);
        $em->flush();
        
        //Pour les fournisseurs, on associe les opp
        //d'abord on supprime
        if($request->request->get('isFournisseur') == 'true' ){
            $linkOpp = $em->getRepository(LFournisseurOpp::class)->findBy(array('lFoUsers' => $user));
            foreach($linkOpp as $lOpp){
                $em->remove($lOpp);
            }
            if($request->request->get('userOpp') != ''){
                foreach($request->request->get('userOpp') as $oppId){
                    $opp = $em->getRepository(Opp::class)->find($oppId);
                    $lFournisseurOpp = new LFournisseurOpp();
                    $lFournisseurOpp->setLFoUsers($user);
                    $lFournisseurOpp->setLFoOpp($opp);
    
                    $em->persist($lFournisseurOpp);
                }
                $addRole = true;
                foreach($user->getRoles() as $role){
                    if($role != null){
                        if($role == 'ROLE_SUPER_ADMIN' || $role == 'ROLE_ADMIN' || $role == 'ROLE_GESTIONNAIRE' || $role == 'ROLE_FOURNISSEUR'){
                            $addRole = false;
                        }
                    }
                }
                if($addRole){
                    $user->setRoles(array($request->request->get('userRole')));
                }
                $user->setRoles(array('ROLE_FOURNISSEUR'));
            }
            $em->flush();
        }

        //print_r($user);
        return new JsonResponse(array('status' => 'ok', 'userId' => $user->getId()));
    }
    
    /**
     * @Route("gestion/founisseurs", name="getFournisseurs")
     * @return Response
     */
    public function getFournisseurs()
    {
        $em = $this->getDoctrine()->getManager();
        $repoLFournisseurOpp = $em->getRepository(LFournisseurOpp::class);
        $users = [];
        $tabOpp = [];
        if($this->isGranted('ROLE_ADMIN')){
            $fournisseurs = $em->getRepository(Users::class)->findByRole('ROLE_FOURNISSEUR');
            foreach($fournisseurs as $fournisseur){
                $user = $em->getRepository(Users::class)->find($fournisseur['id']);
                //Utilisateurs
                $users[$user->getId()]['user'] = $user;
                
                //OPP
                $oppByFournisseur = $repoLFournisseurOpp->findBy(array('lFoUsers' => $user));
                $tabOppId=[];
                foreach($oppByFournisseur as $opp){
                    $tabOppId[] = $opp->getLFoOpp()->getOppId();
                }
                $users[$user->getId()]['opp'] = $tabOppId;
                $tabOpp = $em->getRepository(Opp::class)->findAll();
                
                //Serie
                $seriesCreatByUser = $em->getRepository(Serie::class)->findBy(array('serieUserCrea' => $user));
                $tabSerie = [];
                $tabSerie["nb"] = $seriesCreatByUser ? count($seriesCreatByUser) : 0;
                $tabSerie['freq'] = [];
                $tabSerie['nom'] = [];
                $tabSerie['axe'] = [];
                //$users[$user->getId()]['series']["nb"] = $seriesCreatByUser ? count($seriesCreatByUser) : 0;

                foreach($seriesCreatByUser as $serieCreatByUser){
                    if(!isSet($tabSerie['freq']) || !in_array($serieCreatByUser->getSerieFreqInterval() . " " . $serieCreatByUser->getSerieFreqPeriod() , $tabSerie['freq'])){
                        $tabSerie['freq'][] = $serieCreatByUser->getSerieFreqInterval() . " " . $serieCreatByUser->getSerieFreqPeriod() ;
                    }
                    $tabSerie['nom'][] = $serieCreatByUser->getSerieTitre() ;
                    
                    $lAxesBySerie = $em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatSerie' => $serieCreatByUser));
                    foreach($lAxesBySerie as $lAxeBySerie){
                        if(!isSet($tabSerie['axe']) || !in_array($lAxeBySerie->getLSatAxeThematic()->getAxeThematicNom() , $tabSerie['axe'])){
                            $tabSerie['axe'][] = $lAxeBySerie->getLSatAxeThematic()->getAxeThematicNom() ;
                        }
                    }
                    //$tabSeries[] = $infoSeries;
                }
                $users[$user->getId()]['infoSeries'] = $tabSerie;
            }
        }else{
            $user = $this->getUser();
            $OppsByGestionnaire = $em->getRepository(LGestionnaireOpp::class)->findBy(array('lGoUsers' => $user));
            foreach($OppsByGestionnaire as $OppByGestionnaire){
                $tabOpp[] = $OppByGestionnaire->getLGoOpp();
            }
            $users = [];
            foreach($OppsByGestionnaire as $OppByGestionnaire){
                $fournisseursByOpp = $repoLFournisseurOpp->findBy(array('lFoOpp' => $OppByGestionnaire->getLGoOpp()));
                foreach($fournisseursByOpp as $fournisseurByOpp){
                    //Utilisateurs
                    $users[$fournisseurByOpp->getLFoUsers()->getId()]['user'] = $fournisseurByOpp->getLFoUsers();

                    //OPP
                    $oppByFournisseur = $repoLFournisseurOpp->findBy(array('lFoUsers' => $fournisseurByOpp->getLFoUsers()));
                    $tabOppId=[];
                    foreach($oppByFournisseur as $opp){
                        foreach($OppsByGestionnaire as $oppValid){
                            if($oppValid->getLGoOpp() == $opp->getLFoOpp()){
                                //On prend seulement les opp du gestionnaire
                                $tabOppId[] = $opp->getLFoOpp()->getOppId();
                            }
                        }
                    }
                    $users[$fournisseurByOpp->getLFoUsers()->getId()]['opp'] = $tabOppId;

                    //Serie
                    $seriesCreatByUser = $em->getRepository(Serie::class)->findBy(array('serieUserCrea' => $fournisseurByOpp->getLFoUsers()));
                    $users[$fournisseurByOpp->getLFoUsers()->getId()]['series'] = $seriesCreatByUser ? count($seriesCreatByUser) : 0;

                        
                    //Serie
                    $tabSerie = [];
                    $tabSerie["nb"] = $seriesCreatByUser ? count($seriesCreatByUser) : 0;
                    $tabSerie['freq'] = [];
                    $tabSerie['nom'] = [];
                    $tabSerie['axe'] = [];
                    //$users[$user->getId()]['series']["nb"] = $seriesCreatByUser ? count($seriesCreatByUser) : 0;

                    foreach($seriesCreatByUser as $serieCreatByUser){
                        if(!isSet($tabSerie['freq']) || !in_array($serieCreatByUser->getSerieFreqInterval() . " " . $serieCreatByUser->getSerieFreqPeriod() , $tabSerie['freq'])){
                            $tabSerie['freq'][] = $serieCreatByUser->getSerieFreqInterval() . " " . $serieCreatByUser->getSerieFreqPeriod() ;
                        }
                        $tabSerie['nom'][] = $serieCreatByUser->getSerieTitre() ;
                        
                        $lAxesBySerie = $em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatSerie' => $serieCreatByUser));
                        foreach($lAxesBySerie as $lAxeBySerie){
                            if(!isSet($tabSerie['axe']) || !in_array($lAxeBySerie->getLSatAxeThematic()->getAxeThematicNom() , $tabSerie['axe'])){
                                $tabSerie['axe'][] = $lAxeBySerie->getLSatAxeThematic()->getAxeThematicNom() ;
                            }
                        }
                        //$tabSeries[] = $infoSeries;
                    }
                    $users[$fournisseurByOpp->getLFoUsers()->getId()]['infoSeries'] = $tabSerie;
                }
            }
        }

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());
        
        return $this->render("user/fournisseurs.html.twig", [
            'users' => $users,
            'opp' => $tabOpp,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    /**
     * @Route("gestion/show/user/{userId}", name="showUser")
     * @return Response
     */
    public function showUser(string $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->find($userId);

        $LFournisseurOpps = $em->getRepository(LFournisseurOpp::class)->findBy(array('lFoUsers' => $user));
        $LGestionnaireOpps = $em->getRepository(LGestionnaireOpp::class)->findBy(array('lGoUsers' => $user));

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("user/show_user.html.twig", [
            'user' => $user,
            'fournisseurOpps' => $LFournisseurOpps,
            'gestionnaireOpps' => $LGestionnaireOpps,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }

    
    /**
     * @Route("activeCompte/{id}", name="activeCompte")
     * @return Response
     */
    public function activeCompte(string $id)
    {
        $ParametreDAO = $this->get('parametre.dao');
        //A déplacer au lancement de l'application
        $ParametreDAO->setGlobalParamaters();
        
        $parameters = $this->get('session')->get('parameters');

        $em = $this->getDoctrine()->getManager();
        $userId = base64_decode($id);
        $user = $em->getRepository(Users::class)->find($userId);

        if($user){
            $user->setRoles(array('ROLE_FOURNISSEUR'));
            $em->persist($user);
            $em->flush();
        }

        $message = (new Swift_Message('[POPP] Activation de votre compte'))
        ->setFrom($parameters['EMAIL_EXPEDITEUR'])
        ->setTo($user->getEmail())
        ->setBody(
            $this->container->get('templating')->render(
                'mail/confirmationInscriptionFournisseur.html.twig',
                array('user' => $user)
            ),
            'text/html'
        );
        $this->container->get('mailer')->send($message);

        return $this->redirectToRoute('public_page', ['message' => 'Compte activé']);
    }


    /**
     * @Route("nonActivationCompte/{id}", name="nonActivationCompte")
     * @return Response
     */
    public function nonActivationCompte(string $id)
    {
        $parameters = $this->get('session')->get('parameters');

        $em = $this->getDoctrine()->getManager();
        $userId = base64_decode($id);
        $user = $em->getRepository(Users::class)->find($userId);

        $linkOpp = $em->getRepository(LFournisseurOpp::class)->findOneBy(array('lFoUsers' => $user));
        $em->remove($linkOpp);
        $em->flush();

        $message = (new Swift_Message('[POPP] Activation refusée de votre compte'))
        ->setFrom($parameters['EMAIL_EXPEDITEUR'])
        ->setTo($user->getEmail())
        ->setBody(
            $this->container->get('templating')->render(
                'mail/nonActivationFournisseur.html.twig',
                array('user' => $user)
            ),
            'text/html'
        );
        $this->container->get('mailer')->send($message);

        return $this->redirectToRoute('public_page', ['message' => 'Compte non activé']);
    }

    
    /**
     * @Route("test", name="test")
     * @return Response
     */
    public function test()
    {
        $string    = '17-5';
        $encrypted = base64_encode($string);
        var_dump($encrypted); // eyJpdiI6Img2dStibzhWMGlTamFmakJDeUd0a1E9PSIsInZhbHVlIjoicnpwTWZmcTRDa1JXOVRuVGtudW5ldXkwanBIUlFBQ3I1czNwTjFib1dqWT0iLCJtYWMiOiI3OTBkYzg5YmE0MWFjYTMzMGU2N2NhMDY0MTRkZjc1ZGQ0MGM4MzM4MDI2N2FhYjk0YWE5Y2M5NDk5YTY2NGQzIn0=
        var_dump(base64_decode($encrypted)); // hello world
        return new JsonResponse(array('status' => 'ok'));
    }
}
