<?php

namespace App\Controller;

use App\Entity\AxeThematic;
use App\Entity\Faq;
use App\Entity\Licence;
use App\Entity\LSerieAxeThematic;
use App\Entity\Photo;
use App\Entity\PorteurOpp;
use App\Entity\Serie;
use App\Entity\Son;
use App\Entity\UnitePaysage;
use App\Entity\UnitePaysageLocale;
use App\Entity\Langue;
use App\Entity\EnsemblePaysager;
use App\Entity\Format;
use App\Entity\TypologiePaysage;
use App\Entity\LSerieUnitePaysagereLocale;
use Proxies\__CG__\App\Entity\DocumentRef;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class MetadataController extends Controller
{
    /* Axe thematique debut */
    
    /**
     * @Route("admin/get/axeThematics", name="getAxeThematics")
     * @return Response
     */
    public function getAxeThematics()
    {
        $em = $this->getDoctrine()->getManager();
        $axeThematics = $em->getRepository(AxeThematic::class)->findAll();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("parametre/axeThematics.html.twig",[
            'axeThematics' => $axeThematics,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    
    /**
     * @Route("admin/update/axeThematic/{idAxeThematic}", name="updateAxeThematic")
     * @return JsonResponse
     */
    public function updateAxeThematic($idAxeThematic, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($idAxeThematic == "new"){
            $axeThematic = new AxeThematic();
        }else{
            $axeThematic = $em->getRepository(AxeThematic::class)->find($idAxeThematic);
        }

        $axeThematic->setAxeThematicNom($request->get('axeThematic_nom'));
        $axeThematic->setAxeThematicDesc($request->get('axeThematic_desc'));
        //On enregistre les modifs
        $em->persist($axeThematic);
        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok', "axeThematicId" => $axeThematic->getAxeThematicId()));
    }
    
    /**
     * @Route("admin/remove/axeThematic/{idAxeThematic}", name="removeAxeThematic")
     * @return JsonResponse
     */
    public function removeAxeThematic($idAxeThematic)
    {
        $em = $this->getDoctrine()->getManager();
        $axeThematic = $em->getRepository(AxeThematic::class)->find($idAxeThematic);
        
        $lSerieAxeThematic = $em->getRepository(LSerieAxeThematic::class)->findBy(array('lSatAxeThematic' => $axeThematic));
        if(isSet($lSerieAxeThematic[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'L\'axe thématique est utilisé par des séries photos, suppression impossible'));
        }

        $em->remove($axeThematic);
        $em->flush();
        
        return new JsonResponse(array(
            'status' => 'ok'));
    }

    /* Axe thématique fin */
    
    /* Ensemble Paysager début */

    /**
     * @Route("admin/get/ensemblePaysagers", name="getEnsemblePaysager")
     * @return Response
     */
    public function getEnsemblePaysager()
    {
        $em = $this->getDoctrine()->getManager();
        $ensemblePaysagers = $em->getRepository(EnsemblePaysager::class)->findAll();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("parametre/ensemblePaysager.html.twig",[
            'ensemblePaysagers' => $ensemblePaysagers,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    
    /**
     * @Route("admin/update/ensemblePaysager/{idEnsemblePaysager}", name="updateEnsemblePaysager")
     * @return JsonResponse
     */
    public function updateEnsemblePaysager($idEnsemblePaysager, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($idEnsemblePaysager == "new"){
            $ensemblePaysager = new EnsemblePaysager();
        }else{
            $ensemblePaysager = $em->getRepository(EnsemblePaysager::class)->find($idEnsemblePaysager);
        }

        $ensemblePaysager->setEnsemblePaysagerNom($request->get('ensemblePaysager_nom'));
        $ensemblePaysager->setEnsemblePaysagerDesc($request->get('ensemblePaysager_desc'));
        //On enregistre les modifs
        $em->persist($ensemblePaysager);
        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok', "ensemblePaysagerId" => $ensemblePaysager->getEnsemblePaysagerId()));
    }
    
    /**
     * @Route("admin/remove/ensemblePaysager/{idEnsemblePaysager}", name="removeEnsemblePaysager")
     * @return JsonResponse
     */
    public function removeEnsemblePaysager($idEnsemblePaysager)
    {
        $em = $this->getDoctrine()->getManager();
        $ensemblePaysager = $em->getRepository(EnsemblePaysager::class)->find($idEnsemblePaysager);
        
        $unitePaysage = $em->getRepository(UnitePaysage::class)->findBy(array('unitePaysageEnsemble' => $ensemblePaysager));
        $serie = $em->getRepository(Serie::class)->findBy(array('serieEnsemblePaysage' => $ensemblePaysager));
        if(isSet($unitePaysage[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'L\'ensemble paysager est utilisée pour des unités de paysage, suppression impossible'));
        }
        if(isSet($serie[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'L\'ensemble paysager est utilisée pour des series, suppression impossible'));
        }

        $em->remove($ensemblePaysager);
        $em->flush();
        
        return new JsonResponse(array(
            'status' => 'ok'));
    }
    /* ENSEMBLES PAYSAGER FIN */

    /* FORMATS Debut */

    /**
     * @Route("admin/get/formats", name="getFormat")
     * @return Response
     */
    public function getFormat()
    {
        $em = $this->getDoctrine()->getManager();
        $formats = $em->getRepository(Format::class)->findAll();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("parametre/formats.html.twig",[
            'formats' => $formats,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    
    /**
     * @Route("admin/update/format/{idFormat}", name="updateFormat")
     * @return JsonResponse
     */
    public function updateFormat($idFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($idFormat == "new"){
            $format = new Format();
        }else{
            $format = $em->getRepository(Format::class)->find($idFormat);
        }

        $format->setFormatNom($request->get('format_nom'));
        $format->setFormatDesc($request->get('format_desc'));
        //On enregistre les modifs
        $em->persist($format);
        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok', "formatId" => $format->getFormatId()));
    }
    
    /**
     * @Route("admin/remove/format/{idFormat}", name="removeFormat")
     * @return JsonResponse
     */
    public function removeFormat($idFormat)
    {
        $em = $this->getDoctrine()->getManager();
        $format = $em->getRepository(Format::class)->find($idFormat);
        
        $documentRef = $em->getRepository(DocumentRef::class)->findBy(array('documentRefFormat' => $format));
        $photo = $em->getRepository(Photo::class)->findBy(array('photoFormat' => $format));
        $serie = $em->getRepository(Serie::class)->findBy(array('serieFormat' => $format));
        $son = $em->getRepository(Son::class)->findBy(array('sonFormat' => $format));
        if(isSet($documentRef[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'Le format est utilisé pour des documents de rérence, suppression impossible'));
        }
        if(isSet($photo[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'Le format est utilisé pour des photos, suppression impossible'));
        }
        if(isSet($serie[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'Le format est utilisé pour des series, suppression impossible'));
        }
        if(isSet($son[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'Le format est utilisé pour des sons, suppression impossible'));
        }

        $em->remove($format);
        $em->flush();
        
        return new JsonResponse(array(
            'status' => 'ok'));
    }
    /* FORMATS Fin */

    /* Langues DEBUT */

    /**
     * @Route("admin/get/langues", name="getLangue")
     * @return Response
     */
    public function getLangue()
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository(Langue::class)->findAll();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("parametre/langues.html.twig",[
            'langues' => $langues,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    
    /**
     * @Route("admin/update/langue/{idLangue}", name="updateLangue")
     * @return JsonResponse
     */
    public function updateLangue($idLangue, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($idLangue == "new"){
            $langue = new Langue();
        }else{
            $langue = $em->getRepository(Langue::class)->find($idLangue);
        }

        $langue->setLangueNom($request->get('langue_nom'));
        $langue->setLangueDesc($request->get('langue_desc'));
        //On enregistre les modifs
        $em->persist($langue);
        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok', "langueId" => $langue->getLangueId()));
    }
    
    /**
     * @Route("admin/remove/langue/{idLangue}", name="removeLangue")
     * @return JsonResponse
     */
    public function removeLangue($idLangue)
    {
        $em = $this->getDoctrine()->getManager();
        $langue = $em->getRepository(Langue::class)->find($idLangue);
        
        $documentRef = $em->getRepository(DocumentRef::class)->findBy(array('documentRefLangue' => $langue));
        $serie = $em->getRepository(Serie::class)->findBy(array('serieLangue' => $langue));
        $son = $em->getRepository(Son::class)->findBy(array('sonLangue' => $langue));
        if(isSet($documentRef[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'La langue est utilisée pour des documents de rérence, suppression impossible'));
        }
        if(isSet($serie[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'La langue est utilisée pour des series, suppression impossible'));
        }
        if(isSet($son[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'La langue est utilisée pour des sons, suppression impossible'));
        }

        $em->remove($langue);
        $em->flush();
        
        return new JsonResponse(array(
            'status' => 'ok'));
    }
    
    /* Langues FIN */

    /* LICENCES Debut */

    /**
     * @Route("admin/get/licences", name="getLicence")
     * @return Response
     */
    public function getLicence()
    {
        $em = $this->getDoctrine()->getManager();
        $licences = $em->getRepository(Licence::class)->findAll();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("parametre/licences.html.twig",[
            'licences' => $licences,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    
    /**
     * @Route("admin/update/licence/{idLicence}", name="updateLicence")
     * @return JsonResponse
     */
    public function updateLicence($idLicence, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($idLicence == "new"){
            $licence = new Licence();
        }else{
            $licence = $em->getRepository(Licence::class)->find($idLicence);
        }

        $licence->setLicenceNom($request->get('licence_nom'));
        $licence->setLicenceDesc($request->get('licence_desc'));
        //On enregistre les modifs
        $em->persist($licence);
        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok', "licenceId" => $licence->getLicenceId()));
    }
    
    /**
     * @Route("admin/remove/licence/{idLicence}", name="removeLicence")
     * @return JsonResponse
     */
    public function removeLicence($idLicence)
    {
        $em = $this->getDoctrine()->getManager();
        $licence = $em->getRepository(Licence::class)->find($idLicence);
        
        $documentRef = $em->getRepository(DocumentRef::class)->findBy(array('documentRefLicence' => $licence));
        $photo = $em->getRepository(Photo::class)->findBy(array('photoLicence' => $licence));
        $son = $em->getRepository(Son::class)->findBy(array('sonLicence' => $licence));
        if(isSet($documentRef[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'La licence est utilisée pour des documents de rérence, suppression impossible'));
        }
        if(isSet($photo[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'La licence est utilisée pour des photos, suppression impossible'));
        }
        if(isSet($son[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'La licence est utilisée pour des sons, suppression impossible'));
        }

        $em->remove($licence);
        $em->flush();
        
        return new JsonResponse(array(
            'status' => 'ok'));
    }
    /* LICENCES Fin */

    /* typologie Paysage debut */

    /**
     * @Route("admin/get/typologiePaysages", name="getTypologiePaysage")
     * @return Response
     */
    public function getTypologiePaysage()
    {
        $em = $this->getDoctrine()->getManager();
        $typologiePaysages = $em->getRepository(TypologiePaysage::class)->findAll();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("parametre/typologiePaysage.html.twig",[
            'typologiePaysages' => $typologiePaysages,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    
    /**
     * @Route("admin/update/typologiePaysage/{idTypologiePaysage}", name="updateTypologiePaysage")
     * @return JsonResponse
     */
    public function updateTypologiePaysage($idTypologiePaysage, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($idTypologiePaysage == "new"){
            $typologiePaysage = new TypologiePaysage();
        }else{
            $typologiePaysage = $em->getRepository(TypologiePaysage::class)->find($idTypologiePaysage);
        }

        $typologiePaysage->setTypologiePaysageNom($request->get('typologiePaysage_nom'));
        $typologiePaysage->setTypologiePaysageDesc($request->get('typologiePaysage_desc'));
        //On enregistre les modifs
        $em->persist($typologiePaysage);
        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok', "typologiePaysageId" => $typologiePaysage->getTypologiePaysageId()));
    }
    
    /**
     * @Route("admin/remove/typologiePaysage/{idTypologiePaysage}", name="removeTypologiePaysage")
     * @return JsonResponse
     */
    public function removeTypologiePaysage($idTypologiePaysage)
    {
        $em = $this->getDoctrine()->getManager();
        $typologiePaysage = $em->getRepository(TypologiePaysage::class)->find($idTypologiePaysage);
        
        $serie = $em->getRepository(Serie::class)->findBy(array('serieTypologie' => $typologiePaysage));
        if(isSet($serie[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'La typologie de paysage est utilisée par des series, suppression impossible'));
        }

        $em->remove($typologiePaysage);
        $em->flush();
        
        return new JsonResponse(array(
            'status' => 'ok'));
    }

    /* Typologie paysage fin */

    /* Unité paysagère debut */
    
    /**
     * @Route("admin/get/unitePaysages", name="getUnitePaysages")
     * @return Response
     */
    public function getUnitePaysages()
    {
        $em = $this->getDoctrine()->getManager();
        $unitePaysages = $em->getRepository(UnitePaysage::class)->findAll();
        $ensemblePaysages = $em->getRepository(EnsemblePaysager::class)->findAll();
        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("parametre/unitePaysages.html.twig",[
            'unitePaysages' => $unitePaysages,
            'ensemblePaysages' => $ensemblePaysages,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    
    /**
     * @Route("admin/update/unitePaysage/{idUnitePaysage}", name="updateUnitePaysage")
     * @return JsonResponse
     */
    public function updateUnitePaysage($idUnitePaysage, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($idUnitePaysage == "new"){
            $unitePaysage = new UnitePaysage();
        }else{
            $unitePaysage = $em->getRepository(UnitePaysage::class)->find($idUnitePaysage);
        }

        $unitePaysage->setUnitePaysageNom($request->get('unitePaysage_nom'));
        $unitePaysage->setUnitePaysageDesc($request->get('unitePaysage_desc'));
        
        if ($request->get('ensemblePaysage')){
            $ensemblePaysager = $em->getRepository(EnsemblePaysager::class)->find($request->get('ensemblePaysage'));
            $unitePaysage->setUnitePaysageEnsemble($ensemblePaysager);
        }
        else{
            $unitePaysage->setUnitePaysageEnsemble(null);
        }
        //On enregistre les modifs
        $em->persist($unitePaysage);
        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok', "unitePaysageId" => $unitePaysage->getUnitePaysageId()));
    }
    
    /**
     * @Route("admin/remove/unitePaysage/{idUnitePaysage}", name="removeUnitePaysage")
     * @return JsonResponse
     */
    public function removeUnitePaysage($idUnitePaysage)
    {
        $em = $this->getDoctrine()->getManager();
        $unitePaysage = $em->getRepository(UnitePaysage::class)->find($idUnitePaysage);
        
        $serie = $em->getRepository(Serie::class)->findBy(array('serieUnitePaysagere' => $unitePaysage));
        if(isSet($serie[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'L\'unité paysagère est utilisée par des séries photos, suppression impossible'));
        }

        $em->remove($unitePaysage);
        $em->flush();
        
        return new JsonResponse(array(
            'status' => 'ok'));
    }

    /* Unité paysagère fin */

    /* Unité paysagère local debut */
    
    /**
     * @Route("admin/get/unitePaysageLocales", name="getUnitePaysageLocales")
     * @return Response
     */
    public function getUnitePaysageLocales()
    {
        $em = $this->getDoctrine()->getManager();
        $unitePaysageLocales = $em->getRepository(UnitePaysageLocale::class)->findAll();

        //pour les logos
        $structures = $em->getRepository(PorteurOpp::class)->findBy(array(), array('porteurOppNom' => 'DESC'));
        
        //Pour les commentaires en attentes
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $CommentaireDAO = $this->get('commentaire.dao');
        $nbWaitingComments = $CommentaireDAO->getCommentairesNonVus($isAdmin, $this->getUser());

        return $this->render("parametre/unitePaysageLocales.html.twig",[
            'unitePaysageLocales' => $unitePaysageLocales,
            'structures' => $structures,
            'nbWaitingComments' => $nbWaitingComments
        ]);
    }
    
    
    /**
     * @Route("admin/update/unitePaysageLocale/{idUnitePaysageLocale}", name="updateUnitePaysageLocale")
     * @return JsonResponse
     */
    public function updateUnitePaysageLocale($idUnitePaysageLocale, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($idUnitePaysageLocale == "new"){
            $unitePaysageLocale = new UnitePaysageLocale();
        }else{
            $unitePaysageLocale = $em->getRepository(UnitePaysageLocale::class)->find($idUnitePaysageLocale);
        }

        $unitePaysageLocale->setUnitePaysageLocaleNom($request->get('unitePaysageLocale_nom'));
        $unitePaysageLocale->setUnitePaysageLocaleDesc($request->get('unitePaysageLocale_desc'));
        //On enregistre les modifs
        $em->persist($unitePaysageLocale);
        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok', "unitePaysageLocaleId" => $unitePaysageLocale->getUnitePaysageLocaleId()));
    }
    
    /**
     * @Route("admin/remove/unitePaysageLocale/{idUnitePaysageLocale}", name="removeUnitePaysageLocale")
     * @return JsonResponse
     */
    public function removeUnitePaysageLocale($idUnitePaysageLocale)
    {
        $em = $this->getDoctrine()->getManager();
        $unitePaysageLocale = $em->getRepository(UnitePaysageLocale::class)->find($idUnitePaysageLocale);
        
        $serie = $em->getRepository(LSerieUnitePaysagereLocale::class)->findBy(array('lSuplUnitePaysageLocale' => $unitePaysageLocale));
        if(isSet($serie[0])){
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'L\'unité paysagère locale est utilisée par des séries photos, suppression impossible'));
        }

        $em->remove($unitePaysageLocale);
        $em->flush();
        
        return new JsonResponse(array(
            'status' => 'ok'));
    }

    /* Unité paysagère fin */
}
