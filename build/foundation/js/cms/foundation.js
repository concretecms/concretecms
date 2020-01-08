
// Import required libraries.
import * as _ from 'underscore';
import NProgress from 'NProgress';
import PNotify from 'pnotify/dist/es/PNotify';

window.NProgress = NProgress;
window._ = _;
window.PNotify = PNotify;

// JavaScript/jQuery base libraries.
import 'json5';
import 'jquery.cookie';
import 'jquery-form';

// jQuery UI components
import 'jquery-ui/ui/widgets/dialog';
import 'jquery-ui/ui/widgets/datepicker';
import 'jquery-ui/ui/widgets/draggable';
import 'jquery-ui/ui/widgets/droppable';
import 'jquery-ui/ui/widgets/sortable';

// Core concrete5 backend
import './events';
import './asset-loader';
import './page-indexer';
import './concrete5';

// CMS UI Components
import './panels';
import './toolbar';
import './dialog';
import './alert';

// Edit Mode
import './edit-mode';

// AJAX Forms and in-page notifications
import './ajax-request/base';
import './ajax-request/form';
import './ajax-request/block';

// Progressive operations
import './progressive-operations';

// Search
import './search/base';
import './search/table';
import './search/field-selector';
import './search/preset-selector';

// Tree
import './tree';
import 'jquery.fancytree/dist/modules/jquery.fancytree.glyph';
import 'jquery.fancytree/dist/modules/jquery.fancytree.persist';
import 'jquery.fancytree/dist/modules/jquery.fancytree.dnd';
import 'jquery.fancytree/dist/modules/jquery.fancytree';

// Sitemap
import  './sitemap/sitemap';
import  './in-context-menu';
import  './sitemap/menu';
import  './sitemap/search';
import  './sitemap/selector';

// Users
import './users';

// Express
import './express';

// Style customizer

// In-page editable fields
// TBD

// File Manager
import './file-manager/uploader';
import './file-manager/search';
import './file-manager/selector';
import './file-manager/menu';

// Miscellaneous UI components
import 'selectize';
import 'spectrum-colorpicker';
import 'tristate/jquery.tristate';
import 'jquery-text-counter/textcounter';
import './jquery-awesome-rating';
import './liveupdate/quicksilver';
import './liveupdate/jquery-liveupdate';

// Help
import './help/help';

// Boards
import './boards';

// Calendar component
import './calendar';
