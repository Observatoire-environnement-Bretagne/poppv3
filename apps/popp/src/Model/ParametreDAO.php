<?php
namespace App\Model;

use App\Entity\Parametre;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ParametreDAO {
    // Référence au service Doctrine
    //private $doctrine;
    private $em;
    private $container;
    
    /*CONST DICTIONNAIRE_OCC = [
        "labelUnitePaysageLocale" => "structure de paysage",
        "labelUnitePaysageLocales" => "structures de paysage",
        "labelExportSerie" => "Export des points de vue",
        "labelNomSeriePhoto" => "Nom du point de vue",
        "labelPublierSerie" => "Publier le point de vue",
        "labelSerie" => "Point de vue",
        "labelSeries" => "points de vue",
        "labelArtAndSerie" => "du point de vue",
        "labelModifSerie" => "Modifier le point de vue",
        "labelAjoutSerie" => "Ajouter un point de vue",
        "labelTitleExportSerie" => "Exporter le point de vue",
        "labelALaSerie" => "au point de vue",
        "labelLaSerie" => "le point de vue",
        "labelTypologiesPaysage" => "grands ensembles géographiques",
        "labelArtTypologiePaysage" => "un grand ensemble géographique",
        "labelTypologiePaysage" => "grand ensemble géographique",
        "fieldRequired" => ["AxeThematique", "EnsemblePaysager", "UnitePaysage"],
        "labelIntentionPhotographe" => "Intention de prise de vue",
        "labelEnsemblePaysager" => "famille de paysages",
        "labelEnsemblesPaysagers" => "familles de paysages",
        "labelArtEnsemblePaysager" => "une famille de paysages",
        "labelAuteurReconduction" => "Auteur de la photographie",
        "labelExportAllSeries" => "Ajouter tous les points de vue dans le panier",
        "labelSuccessAddSeries" => "Points de vue ajoutés au panier",
        "labelSuccessAddSerie" => "Point de vue ajouté au panier",
        "TextIntroPanier" => "Vous trouvez tous les points de vue que vous avez sélectionnés dans la POPP. 
        En cliquant sur « exporter les données », vous téléchargez un tableur qui recense les métadonnées 
        liées aux séries photos et les statistiques relatives aux différents changements de paysage qui 
        affectent vos séries. L’export est limité à 100 séries pour des questions de performance. 
        Aussi, cette sélection n’est pas enregistrée dans votre espace personnel, elle est valable uniquement 
        lors de cette session.",
        "showLicenceFichePhoto" => false,
        "showSerieLangue" => false,
        "showSerieDate" => true,
        "showSerieFormat" => false,
        "showButtonWizardTop" => true,
        "wizardOrder" => [1 => "Objet", 2 => "Emplacement", 3 => "Géographie", 4 => "Photos", 5 => "Fiche terrain", 6 => "Documentations annexes"],
        "blockSearch" => [0 => 
                            ["title" => "Localisation", "code" => "Glocalisation", "type" => "accordion", "class" => "show",  "block" => 
                                [   
                                    1 => ["type" => "departement"],
                                    2 => ["type" => "commune"]
                                ]
                        ],
                        1 => 
                            ["title" => "OPP", "code" => "Gopp", "type" => "accordion", "class" => "", "block" => 
                                [
                                    1 => ["type" => "opp"],
                                    2 => ["type" => "axeThematique"],
                                    3 => ["type" => "annee"],
                                    4 => ["type" => "identifiant"]
                                ]
                            ],
                        2 => 
                            ["title" => "Identification des paysages", "code" => "Gpaysage", "type" => "accordion", "class" => "", "block" => 
                                [
                                    1 => ["type" => "typoPaysage"],
                                    2 => ["type" => "ensPaysage"],
                                    3 => ["type" => "unitePaysage"]
                                ]
                            ],
                        3 => 
                            ["title" => "Localisation", "code" => "Glocalisation", "type" => "block", "class" => "", "block" => 
                                [
                                    1 => ["type" => "elementPaysage"]
                                ]
                            ]
                        ]
    ];

    CONST DICTIONNAIRE = [
        "labelUnitePaysageLocale" => "unité paysagère locale",
        "labelUnitePaysageLocales" => "unités paysagères locales", 
        "labelExportSerie" => "Export des séries",
        "labelNomSeriePhoto" => "Nom de la série photo",
        "labelPublierSerie" => "Publier la série",
        "labelSerie" => "Série",
        "labelSeries" => "séries",
        "labelArtAndSerie" => "de la série",
        "labelModifSerie" => "Modifier la série",
        "labelAjoutSerie" => "Ajouter une série photo",
        "labelTitleExportSerie" => "Exporter la série",
        "labelALaSerie" => "à la série",
        "labelLaSerie" => "la série",
        "labelTypologiesPaysage" => "typologies de paysage",
        "labelArtTypologiePaysage" => "une typologie de paysage",
        "labelTypologiePaysage" => "typologie de paysage",
        "fieldRequired" => ["descFine"],
        "labelIntentionPhotographe" => "Intention du photographe",
        "labelEnsemblePaysager" => "ensemble paysager",
        "labelEnsemblesPaysagers" => "ensembles paysagers",
        "labelArtEnsemblePaysager" => "un ensemble paysager",
        "labelAuteurReconduction" => "Auteur de la reconduction",
        "labelExportAllSeries" => "Ajouter toutes les séries dans le panier",
        "labelSuccessAddSeries" => "Séries ajoutées au panier",
        "labelSuccessAddSerie" => "Série ajoutée au panier",
        "TextIntroPanier" => "Vous trouvez toutes les séries que vous avez sélectionnées dans la POPP-Breizh. 
        En cliquant sur « exporter les données », vous téléchargez un tableur qui recense les métadonnées 
        liées aux séries photos et les statistiques relatives aux différents changements de paysage qui 
        affectent vos séries. L’export est limité à 100 séries pour des questions de performance. 
        Aussi, cette sélection n’est pas enregistrée dans votre espace personnel, elle est valable uniquement 
        lors de cette session.",
        "showLicenceFichePhoto" => true,
        "showSerieLangue" => true,
        "showSerieDate" => true,
        "showSerieFormat" => true,
        "showButtonWizardTop" => false,
        "wizardOrder" => [1 => "Objet", 2 => "Géographie", 3 => "Fiche terrain", 4 => "Emplacement", 5 => "Photos", 6 => "Documentations annexes"],
        "blockSearch" => [0 => 
                            ["title" => "", "code" => "Gpaysage", "type" => "block", "class" => "", "block" => 
                                [1 => ["type" => "elementPaysage"],
                                2 => ["type" => "commune"],
                                3 => ["type" => "opp"]
                                ]
                        ],
                        1 => 
                            ["title" => "Paysage", "code" => "Gpaysage", "type" => "accordion", "class" => "", "block" => 
                                [1 => ["type" => "typoPaysage"],
                                2 => ["type" => "axeThematique"],
                                3 => ["type" => "ensPaysage"],
                                4 => ["type" => "unitePaysage"]
                                ]
                            ],
                        2 => 
                            ["title" => "Recherche avancée", "code" => "GrechAv", "type" => "accordion", "class" => "", "block" => 
                                [1 => ["type" => "annee"],
                                2 => ["type" => "identifiant"],
                                3 => ["type" => "structureOpp"]
                                ]
                            ],
                        3 => 
                            ["title" => "Localisation", "code" => "Glocalisation", "type" => "accordion", "class" => "", "block" => 
                                [1 => ["type" => "departement"],
                                2 => ["type" => "region"],
                                3 => ["type" => "pays"]
                                ]
                            ]
                        ]
    ];

    const DEFAULT_VALUE_SERIE = array(
        'langue' => 'Français',
        'pays' => 'FRANCE',
        'region' => 'Occitanie',
        'frequence' => 'année',
        'disparition' => 'Disparition'
    );*/
    
    // Constructeur pour permettre au Service Container
    // de nous donner le service Doctrine
    public function __construct(EntityManagerInterface $em, ContainerInterface $container) {
        //$this->doctrine = $doctrine;
        $this->em = $em;
        $this->container = $container;
    }

    public function setGlobalParamaters(){
        $session = $this->container->get('session');
        //$this->session->remove('parameters');
        if($session->get('parameters')) return;
        $listeParametres= $this->em
            ->getRepository(Parametre::class)
            ->findAll();
        foreach($listeParametres as $parametre) {
            $tabParametre[$parametre->getPrmCode()] = $parametre->getPrmValeur();
        }
        $tabParametre["dictionnaire"] = '';
        $tabParametre["default_value_serie"] = '';
        $session->set('parameters', $tabParametre);
    }
}