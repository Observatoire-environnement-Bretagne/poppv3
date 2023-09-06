// require jQuery normally
import $ from 'jquery';
// create global $ and jQuery variables
global.$ = global.jQuery = $;


import 'jstree';



import 'bootstrap' ;

require('datatables.net-bs4')( window, $ );

import {jConfirm} from 'jconfirm/jConfirm';

import './masonry/index.js';
import './scrollbar/index.js';
import './sidebar/index.js';
import './users/index.js';
import './utils/index.js';
import './login/index.js';


import './dropzone/index.js';

import './serie/index.js';
import './serie/updateSerie.js';
import './serie/getSerie.js';
import './serie/updateSerieFile.js';
import './serie/controlFormSerie.js';
import './serie/import.js';

import './photo/index.js';
import './photo/getPhotosDataTable.js';
import './photo/modifyPhoto.js';
import './parametre/index.js';


import './structure/index.js';
import './structure/controlFormStructure.js';
import './structure/showStructure.js';

import './opp/index.js';


import './public/modifyFaqContent.js';
import './public/modifyRessourceContent.js';
import './public/modifyStructureContent.js';


import './thesaurus/index.js';
import './document/index.js';
import './metadata/axeThematics.js';
import './metadata/ensemblePaysager.js';
import './metadata/formats.js';
import './metadata/langues.js';
import './metadata/licences.js';
import './metadata/typologiePaysage.js';
import './metadata/unitePaysageLocal.js';
import './metadata/unitePaysages.js';
import './commentaire/index.js';

import './public/modifyAproposContent.js';
import './public/modifyActualiteContent.js';

import './utils/jconfirm'

import './flashMessage/flashMessage.js'

import './carrousel/carrousel.js'
import './panier/panier.js'
