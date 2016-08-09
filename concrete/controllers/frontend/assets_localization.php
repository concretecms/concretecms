<?php
namespace Concrete\Controller\Frontend;

use Controller;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\Localization\Localization;
use Environment;

class AssetsLocalization extends Controller
{
    protected static function sendJavascriptHeader()
    {
        header('Content-type: text/javascript; charset='.APP_CHARSET);
    }

    public static function getCoreJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        ?>
var ccmi18n = {
  expand: <?=json_encode(t('Expand'))?>,
  loadingText: <?=json_encode(t('Loading'))?>,
  cancel: <?=json_encode(t('Cancel'))?>,
  collapse: <?=json_encode(t('Collapse'))?>,
  error: <?=json_encode(t('Error'))?>,
  deleteBlockConfirm: <?=json_encode(t('Delete Block'))?>,
  deleteBlock: <?=json_encode(t('Block Deleted'))?>,
  deleteBlockMsg: <?=json_encode(t('The block has been removed successfully.'))?>,
  addBlock: <?=json_encode(t('Add Block'))?>,
  addBlockNew: <?=json_encode(t('Add Block'))?>,
  addBlockStack: <?=json_encode(t('Add Stack'))?>,
  addBlockStackMsg: <?=json_encode(t('The stack has been added successfully'))?>,
  addBlockPaste: <?=json_encode(t('Paste from Clipboard'))?>,
  changeAreaCSS: <?=json_encode(t('Design'))?>,
  editAreaLayout: <?=json_encode(t('Edit Layout'))?>,
  addAreaLayout: <?=json_encode(t('Add Layout'))?>,
  moveLayoutUp: <?=json_encode(t('Move Up'))?>,
  moveLayoutDown: <?=json_encode(t('Move Down'))?>,
  moveLayoutAtBoundary: <?=json_encode(t('This layout section can not be moved further in this direction.'))?>,
  areaLayoutPresets: <?=json_encode(t('Layout Presets'))?>,
  lockAreaLayout: <?=json_encode(t('Lock Layout'))?>,
  unlockAreaLayout: <?=json_encode(t('Unlock Layout'))?>,
  deleteLayout: <?=json_encode(t('Delete'))?>,
  deleteLayoutOptsTitle: <?=json_encode(t('Delete Layout'))?>,
  confirmLayoutPresetDelete: <?=json_encode(t('Are you sure you want to delete this layout preset?'))?>,
  setAreaPermissions: <?=json_encode(t('Set Permissions'))?>,
  addBlockMsg: <?=json_encode(t('The block has been added successfully.'))?>,
  updateBlock: <?=json_encode(t('Update Block'))?>,
  updateBlockMsg: <?=json_encode(t('The block has been saved successfully.'))?>,
  copyBlockToScrapbookMsg: <?=json_encode(t('The block has been added to your clipboard.'))?>,
  content: <?=json_encode(t('Content'))?>,
  closeWindow: <?=json_encode(t('Close'))?>,
  editBlock: <?=json_encode(t('Edit'))?>,
  editBlockWithName: <?=json_encode(tc('%s is a block type name', 'Edit %s'))?>,
  setPermissionsDeferredMsg: <?=json_encode(t('Permission setting saved. You must complete the workflow before this change is active.'))?>,
  editStackContents: <?=json_encode(t('Manage Stack Contents'))?>,
  compareVersions: <?=json_encode(t('Compare Versions'))?>,
  blockAreaMenu: <?=json_encode(t('Add Block'))?>,
  arrangeBlock: <?=json_encode(t('Move'))?>,
  arrangeBlockMsg: <?=json_encode(t('Blocks arranged successfully.'))?>,
  copyBlockToScrapbook: <?=json_encode(t('Copy to Clipboard'))?>,
  changeBlockTemplate: <?=json_encode(t('Custom Template'))?>,
  changeBlockCSS: <?=json_encode(t('Design'))?>,
  errorCustomStylePresetNoName: <?=json_encode(t('You must give your custom style preset a name.'))?>,
  changeBlockBaseStyle: <?=json_encode(t('Set Block Styles'))?>,
  confirmCssReset: <?=json_encode(t('Are you sure you want to remove all of these custom styles?'))?>,
  confirmCssPresetDelete: <?=json_encode(t('Are you sure you want to delete this custom style preset?'))?>,
  setBlockPermissions: <?=json_encode(t('Set Permissions'))?>,
  setBlockAlias: <?=json_encode(t('Setup on Child Pages'))?>,
  setBlockComposerSettings: <?=json_encode(t('Composer Settings'))?>,
  themeBrowserTitle: <?=json_encode(t('Get More Themes'))?>,
  themeBrowserLoading: <?=json_encode(t('Retrieving theme data from concrete5.org marketplace.'))?>,
  addonBrowserLoading: <?=json_encode(t('Retrieving add-on data from concrete5.org marketplace.'))?>,
  clear: <?=json_encode(t('Clear'))?>,
  requestTimeout: <?=json_encode(t('This request took too long.'))?>,
  generalRequestError: <?=json_encode(t('An unexpected error occurred.'))?>,
  helpPopup: <?=json_encode(t('Help'))?>,
  community: <?=json_encode(t('concrete5 Marketplace'))?>,
  communityCheckout: <?=json_encode(t('concrete5 Marketplace - Purchase & Checkout'))?>,
  communityDownload: <?=json_encode(t('concrete5 Marketplace - Download'))?>,
  noIE6: <?=json_encode(t('concrete5 does not support Internet Explorer 6 in edit mode.'))?>,
  helpPopupLoginMsg: <?=json_encode(t('Get more help on your question by posting it to the concrete5 help center on concrete5.org'))?>,
  marketplaceErrorMsg: <?=json_encode(t('<p>You package could not be installed.  An unknown error occured.</p>'))?>,
  marketplaceInstallMsg: <?=json_encode(t('<p>Your package will now be downloaded and installed.</p>'))?>,
  marketplaceLoadingMsg: <?=json_encode(t('<p>Retrieving information from the concrete5 Marketplace.</p>'))?>,
  marketplaceLoginMsg: <?=json_encode(t('<p>You must be logged into the concrete5 Marketplace to install add-ons and themes.  Please log in.</p>'))?>,
  marketplaceLoginSuccessMsg: <?=json_encode(t('<p>You have successfully logged into the concrete5 Marketplace.</p>'))?>,
  marketplaceLogoutSuccessMsg: <?=json_encode(t('<p>You are now logged out of concrete5 Marketplace.</p>'))?>,
  deleteAttributeValue: <?=json_encode(t('Are you sure you want to remove this value?'))?>,
  search: <?=json_encode(t('Search'))?>,
  advanced: <?=json_encode(t('Advanced'))?>,
  customizeSearch: <?=json_encode(t('Customize Search'))?>,
  properties: <?=json_encode(t('Page Saved'))?>,
  savePropertiesMsg: <?=json_encode(t('Page Properties saved.'))?>,
  saveSpeedSettingsMsg: <?=json_encode(t('Full page caching settings saved.'))?>,
  saveUserSettingsMsg: <?=json_encode(t('User Settings saved.'))?>,
  ok: <?=json_encode(t('Ok'))?>,
  scheduleGuestAccess: <?=json_encode(t('Schedule Guest Access'))?>,
  scheduleGuestAccessSuccess: <?=json_encode(t('Timed Access for Guest Users Updated Successfully.'))?>,
  newsflowLoading: <?=json_encode(t('Checking for updates.'))?>,
  x: <?=json_encode(t('x'))?>,
  user_activate: <?=json_encode(t('Activate Users'))?>,
  user_deactivate: <?=json_encode(t('Deactivate Users'))?>,
  user_delete: <?=json_encode(t('Delete'))?>,
  user_group_remove: <?=json_encode(t('Remove From Group'))?>,
  user_group_add: <?=json_encode(t('Add to Group'))?>,
  chooseUser: <?=json_encode(t('Choose a User'))?>,
  none: <?=json_encode(t('None'))?>,
  editModeMsg: <?=json_encode(t('Let\'s start editing a page.'))?>,
  editMode: <?=json_encode(t('Edit Mode'))?>,
  save: <?=json_encode(t('Save'))?>,
  currentImage: <?=json_encode(t('Current Image'))?>,
  image: <?=json_encode(t('Image'))?>,
  size: <?=json_encode(t('Size'))?>,
  chooseFont: <?=json_encode(t('Choose Font'))?>,
  fontWeight: <?=json_encode(t('Font Weight'))?>,
  italic: <?=json_encode(t('Italic'))?>,
  underline: <?=json_encode(t('Underline'))?>,
  uppercase: <?=json_encode(t('Uppercase'))?>,
  fontSize: <?=json_encode(t('Font Size'))?>,
  letterSpacing: <?=json_encode(t('Letter spacing'))?>,
  lineHeight: <?=json_encode(t('Line Height'))?>,
  emptyArea: <?=json_encode(t('Empty %s Area', '<%- area_handle %>'))?>,
  fullArea: <?=json_encode(t('This area is full!'))?>
};

var ccmi18n_editor = {
  insertLinkToFile: <?=json_encode(t('Insert Link to File'))?>,
  insertImage: <?=json_encode(t('Insert Image'))?>,
  insertLinkToPage: <?=json_encode(t('Link to Page'))?>
};

var ccmi18n_express = {
    chooseEntry: <?=json_encode(t('Choose Entry'))?>,
    entriesTitle: <?=json_encode(t('Entries'))?>
};

var ccmi18n_sitemap = {
  seo: <?=json_encode(t('SEO'))?>,
  pageLocation: <?=json_encode(t('Location'))?>,
  pageLocationTitle: <?=json_encode(t('Location'))?>,
  visitExternalLink: <?=json_encode(t('Visit'))?>,
  editExternalLink: <?=json_encode(t('Edit External Link'))?>,
  deleteExternalLink: <?=json_encode(t('Delete'))?>,
  copyProgressTitle: <?=json_encode(t('Copy Progress'))?>,
  addExternalLink: <?=json_encode(t('Add External Link'))?>,
  sendToTop: <?=json_encode(t('Send To Top'))?>,
  sendToBottom: <?=json_encode(t('Send To Bottom'))?>,
  emptyTrash: <?=json_encode(t('Empty Trash'))?>,
  restorePage: <?=json_encode(t('Restore Page'))?>,
  deletePageForever: <?=json_encode(t('Delete Forever'))?>,
  previewPage: <?=json_encode(t('Preview'))?>,
  visitPage: <?=json_encode(t('Visit'))?>,
  pageAttributes: <?=json_encode(t('Attributes'))?>,
  speedSettings: <?=json_encode(t('Caching'))?>,
  speedSettingsTitle: <?=json_encode(t('Caching'))?>,
  pageAttributesTitle: <?=json_encode(t('Attributes'))?>,
  pagePermissionsTitle: <?=json_encode(t('Page Permissions'))?>,
  setPagePermissions: <?=json_encode(t('Permissions'))?>,
  setPagePermissionsMsg: <?=json_encode(t('Page permissions updated successfully.'))?>,
  pageDesignMsg: <?=json_encode(t('Theme and page type updated successfully.'))?>,
  pageDesign: <?=json_encode(t('Design &amp; Type'))?>,
  pageVersions: <?=json_encode(t('Versions'))?>,
  deletePage: <?=json_encode(t('Delete'))?>,
  deletePages: <?=json_encode(t('Delete Pages'))?>,
  deletePageSuccessMsg: <?=json_encode(t('The page has been removed successfully.'))?>,
  deletePageSuccessDeferredMsg: <?=json_encode(t('Delete request saved. You must complete the workflow before the page is fully removed.'))?>,
  addPage: <?=json_encode(t('Add Page'))?>,
  moveCopyPage: <?=json_encode(t('Move/Copy'))?>,
  reorderPage: <?=json_encode(t('Change Page Order'))?>,
  reorderPageMessage: <?=json_encode(t('Move or reorder pages by dragging their icons.'))?>,
  moveCopyPageMessage: <?=json_encode(t('Choose a new parent page from the sitemap.'))?>,
  editInComposer: <?=json_encode(t('Edit in Composer'))?>,
  searchPages: <?=json_encode(t('Search Pages'))?>,
  explorePages: <?=json_encode(t('Flat View'))?>,
  backToSitemap: <?=json_encode(t('Back to Sitemap'))?>,
  searchResults: <?=json_encode(t('Search Results'))?>,
  createdBy: <?=json_encode(t('Created By'))?>,
  choosePage: <?=json_encode(t('Choose a Page'))?>,
  viewing: <?=json_encode(t('Viewing'))?>,
  results: <?=json_encode(t('Result(s)'))?>,
  max: <?=json_encode(t('max'))?>,
  noResults: <?=json_encode(t('No results found.'))?>,
  areYouSure: <?=json_encode(t('Are you sure?'))?>,
  loadingText: <?=json_encode(t('Loading'))?>,
  loadError: <?=json_encode(t('Unable to load sitemap data. Response received: '))?>,
  loadErrorTitle: <?=json_encode(t('Unable to load sitemap data.'))?>,
  on: <?=json_encode(t('on'))?>
};

var ccmi18n_spellchecker = {
  resumeEditing: <?=json_encode(t('Resume Editing'))?>,
  noSuggestions: <?=json_encode(t('No Suggestions'))?>
};

var ccmi18n_groups = {
  editGroup: <?=json_encode(t('Edit Group'))?>,
  editPermissions: <?=json_encode(t('Edit Permissions'))?>
};

var ccmi18n_filemanager = {
  view: <?=json_encode(t('View'))?>,
  download: <?=json_encode(t('Download'))?>,
  select: <?=json_encode(t('Choose'))?>,
  duplicateFile: <?=json_encode(t('Copy File'))?>,
  clear: <?=json_encode(t('Clear'))?>,
  edit: <?=json_encode(t('Edit'))?>,
  thumbnailImages: <?=json_encode(t('Thumbnail Images'))?>,
  replace: <?=json_encode(t('Replace'))?>,
  duplicate: <?=json_encode(t('Copy'))?>,
  chooseNew: <?=json_encode(t('Choose New File'))?>,
  sets: <?=json_encode(t('Sets'))?>,
  permissions: <?=json_encode(t('Permissions'))?>,
  properties: <?=json_encode(t('Properties'))?>,
  deleteFile: <?=json_encode(t('Delete'))?>,
  title: <?=json_encode(t('File Manager'))?>,
  uploadErrorChooseFile: <?=json_encode(t('You must choose a file.'))?>,
  addFiles: <?=json_encode(t('Add Files'))?>,
  rescan: <?=json_encode(t('Rescan'))?>,
  pending: <?=json_encode(t('Pending'))?>,
  uploadComplete: <?=json_encode(t('Upload Complete'))?>,
  uploadFailed: <?=json_encode(t('Upload Failed'))?>,
  uploadProgress: <?=json_encode(t('Upload Progress'))?>,
  chosenTooMany: <?=json_encode(t('You may only select a single file.'))?>,
  PTYPE_CUSTOM: <?=json_encode(/*FilePermissions::PTYPE_CUSTOM*/ '')?>,
  PTYPE_NONE: <?=json_encode(/*FilePermissions::PTYPE_NONE*/ '')?>,
  PTYPE_ALL: <?=json_encode(/*FilePermissions::PTYPE_ALL*/ '')?>,
  FTYPE_IMAGE: <?=json_encode(FileType::T_IMAGE)?>,
  FTYPE_VIDEO: <?=json_encode(FileType::T_VIDEO)?>,
  FTYPE_TEXT: <?=json_encode(FileType::T_TEXT)?>,
  FTYPE_AUDIO: <?=json_encode(FileType::T_AUDIO)?>,
  FTYPE_DOCUMENT: <?=json_encode(FileType::T_DOCUMENT)?>,
  FTYPE_APPLICATION: <?=json_encode(FileType::T_APPLICATION)?>
};

var ccmi18n_chosen = {
  placeholder_text_multiple: <?=json_encode(t('Select Some Options'))?>,
  placeholder_text_single: <?=json_encode(t('Select an Option'))?>,
  no_results_text: <?=json_encode(t(/*i18n After this text we have a search criteria: for instance 'No results match "Criteria"'*/'No results match'))?>
};

var ccmi18n_topics = {
  addCategory: <?=json_encode(t('Add Category'))?>,
  editCategory: <?=json_encode(t('Edit Category'))?>,
  deleteCategory: <?=json_encode(t('Delete Category'))?>,
  cloneCategory: <?=json_encode(t('Clone Category'))?>,
  addTopic: <?=json_encode(t('Add Topic'))?>,
  editTopic: <?=json_encode(t('Edit Topic'))?>,
  deleteTopic: <?=json_encode(t('Delete Topic'))?>,
  cloneTopic: <?=json_encode(t('Clone Topic'))?>,
  editPermissions: <?=json_encode(t('Edit Permissions'))?>
};

var ccmi18n_tree = {
    add: <?=json_encode(t('Add'))?>,
    edit: <?=json_encode(t('Edit'))?>,
    delete: <?=json_encode(t('Delete'))?>
};
var ccmi18n_tourist = {
  skipButton: <?=json_encode('<button class="btn btn-default btn-sm pull-right tour-next">'.t('Skip →').'</button>')?>,
  nextButton: <?=json_encode('<button class="btn btn-primary btn-sm pull-right tour-next">'.t('Next →').'</button>')?>,
  finalButton: <?=json_encode('<button class="btn btn-primary btn-sm pull-right tour-next">'.t('Done').'</button>')?>,
  closeButton: <?=json_encode('<a class="btn btn-close tour-close" href="#"><i class="fa fa-remove"></i></a>')?>,
  okButton: <?=json_encode('<button class="btn btn-sm tour-close btn-primary">'.t('Ok').'</button>')?>,
  doThis: <?=json_encode(t('Do this:'))?>,
  thenThis: <?=json_encode(t('Then this:'))?>,
  nextThis: <?=json_encode(t('Next this:'))?>,
  stepXofY: <?=json_encode(t('step %1$d of %2$d'))?>
};

var ccmi18n_helpGuides = {
  'add-page': [
    {title: <?=json_encode(t('Pages Panel'))?>, text: <?=json_encode(t('The pages is where you go to add a new page to your site, or jump between existing pages. To open the pages panel, click the icon.'))?>},
    {title: <?=json_encode(t('Page Types'))?>, text: <?=json_encode(t('This is your list of page types. Click any of them to add a page.'))?>},
    {title: <?=json_encode(t('Sitemap'))?>, text: <?=json_encode(t('This is your sitemap. Use it to easily navigate your site.'))?>}
  ],
  'change-content-edit-mode': [
    {title: <?=json_encode(t('Edit Mode Active'))?>, text: <?=json_encode(t('The highlighted button makes it obvious you\'re in edit mode.'))?>},
    {title: <?=json_encode(t('Edit the Block'))?>, text: <?=json_encode(t('Just roll over any content on the page. Click or tap to get the edit menu for that block.'))?>},
    {title: <?=json_encode(t('Edit Menu'))?>, text: <?=json_encode(t('Use this menu to edit a block\'s contents, change its display, or remove it entirely.'))?>},
    {title: <?=json_encode(t('Save Changes'))?>, text: <?=json_encode(t("When you're done editing you can Save Changes for other editors to see, or Publish Changes to make your changes live immediately."))?>}
  ],
    'add-content-edit-mode': [
    {title: <?=json_encode(t('Add Mode Active'))?>, text: <?=json_encode(t('The highlighted button makes it obvious you\'re in Add Content mode.'))?>},
    {title: <?=json_encode(t('Add Panel'))?>, text: <?=json_encode(t('This is the Add Content Panel.'))?>},
    {title: <?=json_encode(t('Content Selector'))?>, text: <?=json_encode(t('Click here to choose between adding blocks, clipboard items, stacks and stack contents.'))?>},
    {title: <?=json_encode(t('Search Blocks'))?>, text: <?=json_encode(t("You can easily filter the blocks in the panel by searching here."))?>},
    {title: <?=json_encode(t('Add Blocks'))?>, text: <?=json_encode(t("Click and drag blocks from the add panel into the page to add them."))?>}
    ],
  'change-content': [
    {title: <?=json_encode(t('Enter Edit Mode'))?>, text: <?=json_encode(t('First, click the "Edit Page" button. This will enter edit mode for this page.'))?>}
  ],
  'add-content': [
    {title: <?=json_encode(t('Enter Edit Mode'))?>, text: <?=json_encode(t('Click the "Add Content" button to enter edit mode, with the Add Content panel active.'))?>}
  ],
  'dashboard': [
    {title: <?=json_encode(t('Dashboard Panel'))?>, text: <?=json_encode(t('The dashboard is where you go to manage aspects of your site that have to do with more than the content on just one page. Click the sliders icon.'))?>},
    {title: <?=json_encode(t('Sitemap'))?>, text: <?=json_encode(t("The sitemap lets manage the structure of your website. You can delete pages you don't need, or drag them around the tree to suit your needs."))?>}
  ],
  'location-panel': [
    {title: <?=json_encode(t('Choose Location'))?>, text: <?=json_encode(t('Click this button to choose the location of the page in your sitemap. If saved, the page will be moved to this location.'))?>},
    {title: <?=json_encode(t('Page URLs'))?>, text: <?=json_encode(t('Control the URLs used to access your page here. Non-canonical URLs will redirect to your page; canonical URLs can be either generated or automatically or overridden. Sub-pages to this page start with canonical URLs by default.'))?>}
  ],
  'personalize': [
    {title: <?=json_encode(t('Properties Panel'))?>, text: <?=json_encode(t('The properties panel controls data and details about the current page including design customizations. To open the properties panel, click the gear icon.'))?>},
    {title: <?=json_encode(t('Page Design'))?>, text: <?=json_encode(t('From here you can change your page template and customize your page\'s styles.'))?>},
    {title: <?=json_encode(t('Customize'))?>, text: <?=json_encode(t('Click here to load the theme customizer for the page.'))?>}
  ],
  'toolbar': [
    {title: <?=json_encode(t('Edit Mode'))?>, text: <?=json_encode(t('Edit anything on this page by clicking the pencil icon.'))?>},
    {title: <?=json_encode(t('Settings'))?>, text: <?=json_encode(t('Change the general look and options like SEO and permissions. Delete the page or roll versions back from here as well.'))?>},
    {title: <?=json_encode(t('Add Content'))?>, text: <?=json_encode(t('Place a new block on the page. Copy one using the clipboard, or try a reusable stack.'))?>},
    {title: <?=json_encode(t('Intelligent Search'))?>, text: <?=json_encode(t('At a loss? Try searching here. You can find anything from pages in your site to settings and how-to documentation.'))?>},
    {title: <?=json_encode(t('Add Page'))?>, text: <?=json_encode(t('Add a new page to your site, or quickly jump around your sitemap.'))?>},
    {title: <?=json_encode(t('Dashboard'))?>, text: <?=json_encode(t('Anything that isn\'t specific to this page happens here. Manage users, files, reporting data, and site-wide settings.'))?>}
  ]
}
<?php

    }

    public static function getSelect2Javascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        $locale = str_replace('_', '-', Localization::activeLocale());
        if ($locale === 'en-US') {
            echo '// No needs to translate '.$locale;
        } else {
            $env = Environment::get();
            /* @var $env \Concrete\Core\Foundation\Environment */
            $language = Localization::activeLanguage();
            $alternatives = array($locale);
            if (strcmp($locale, $language) !== 0) {
                $alternatives[] = $language;
            }
            $found = null;
            foreach ($alternatives as $alternative) {
                $r = $env->getRecord(DIRNAME_JAVASCRIPT."/i18n/select2_locale_{$alternative}.js");
                if (is_file($r->file)) {
                    $found = $r->file;
                    break;
                }
            }
            if (isset($found)) {
                readfile($found);
            } else {
                echo '// No select2 translations for '.implode(', ', $alternatives);
            }
        }
    }

    public static function getRedactorJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        $locale = Localization::activeLocale();
        ?>
jQuery.Redactor.opts.langs[<?=json_encode($locale)?>] = {
  html: <?=json_encode(t('HTML'))?>,
  video: <?=json_encode(t('Insert Video'))?>,
  image: <?=json_encode(t('Insert Image'))?>,
  table: <?=json_encode(t('Table'))?>,
  link: <?=json_encode(t('Link'))?>,
  link_insert: <?=json_encode(t('Insert link'))?>,
  link_edit: <?=json_encode(t('Edit link'))?>,
  unlink: <?=json_encode(t('Unlink'))?>,
  formatting: <?=json_encode(t('Formatting'))?>,
  paragraph: <?=json_encode(t('Normal text'))?>,
  quote: <?=json_encode(t('Quote'))?>,
  code: <?=json_encode(t('Code'))?>,
  header1: <?=json_encode(t('Header 1'))?>,
  header2: <?=json_encode(t('Header 2'))?>,
  header3: <?=json_encode(t('Header 3'))?>,
  header4: <?=json_encode(t('Header 4'))?>,
  header5: <?=json_encode(t('Header 5'))?>,
  bold: <?=json_encode(t('Bold'))?>,
  italic: <?=json_encode(t('Italic'))?>,
  fontcolor: <?=json_encode(t('Font Color'))?>,
  backcolor: <?=json_encode(t('Back Color'))?>,
  unorderedlist: <?=json_encode(t('Unordered List'))?>,
  orderedlist: <?=json_encode(t('Ordered List'))?>,
  outdent: <?=json_encode(t('Outdent'))?>,
  indent: <?=json_encode(t('Indent'))?>,
  cancel: <?=json_encode(t('Cancel'))?>,
  insert: <?=json_encode(t('Insert'))?>,
  save: <?=json_encode(t('Save'))?>,
  _delete: <?=json_encode(t('Delete'))?>,
  insert_table: <?=json_encode(t('Insert Table'))?>,
  insert_row_above: <?=json_encode(t('Add Row Above'))?>,
  insert_row_below: <?=json_encode(t('Add Row Below'))?>,
  insert_column_left: <?=json_encode(t('Add Column Left'))?>,
  insert_column_right: <?=json_encode(t('Add Column Right'))?>,
  delete_column: <?=json_encode(t('Delete Column'))?>,
  delete_row: <?=json_encode(t('Delete Row'))?>,
  delete_table: <?=json_encode(t('Delete Table'))?>,
  rows: <?=json_encode(t('Rows'))?>,
  columns: <?=json_encode(t('Columns'))?>,
  add_head: <?=json_encode(t('Add Head'))?>,
  delete_head: <?=json_encode(t('Delete Head'))?>,
  title: <?=json_encode(t('Title'))?>,
  image_position: <?=json_encode(t('Position'))?>,
  none: <?=json_encode(t('None'))?>,
  left: <?=json_encode(t('Left'))?>,
  right: <?=json_encode(t('Right'))?>,
  center: <?=json_encode(t('Center'))?>,
  image_web_link: <?=json_encode(t('Image Web Link'))?>,
  text: <?=json_encode(t('Text'))?>,
  mailto: <?=json_encode(t('Email'))?>,
  web: <?=json_encode(t('URL'))?>,
  video_html_code: <?=json_encode(t('Video Embed Code or Youtube/Vimeo Link'))?>,
  file: <?=json_encode(t('Insert File'))?>,
  upload: <?=json_encode(t('Upload'))?>,
  download: <?=json_encode(t('Download'))?>,
  choose: <?=json_encode(t('Choose'))?>,
  or_choose: <?=json_encode(t('Or choose'))?>,
  drop_file_here: <?=json_encode(t('Drop file here'))?>,
  align_left: <?=json_encode(t('Align text to the left'))?>,
  align_center: <?=json_encode(t('Center text'))?>,
  align_right: <?=json_encode(t('Align text to the right'))?>,
  align_justify: <?=json_encode(t('Justify text'))?>,
  horizontalrule: <?=json_encode(t('Insert Horizontal Rule'))?>,
  deleted: <?=json_encode(t('Deleted'))?>,
  anchor: <?=json_encode(t('Anchor'))?>,
  open_link: <?=json_encode(t('Open Link'))?>,
  link_new_tab: <?=json_encode(t('Open link in new tab'))?>,
  /* concrete5 */
  link_same_window: <?=json_encode(t('Open link in same window'))?>,
  in_lightbox: <?=json_encode(t('Open link in Lightbox'))?>,
  lightbox_link_type: <?=json_encode(t('Link Type'))?>,
  lightbox_link_type_iframe: <?=json_encode(t('Web Page'))?>,
  lightbox_link_type_image: <?=json_encode(t('Image'))?>,
  lightbox_link_type_iframe_options: <?=json_encode(t('Frame Options'))?>,
  lightbox_link_type_iframe_width: <?=json_encode(t('Width'))?>,
  lightbox_link_type_iframe_height: <?=json_encode(t('Height'))?>,
  customStyles: <?=json_encode(t('Custom Styles'))?>,
  remove_font: <?=json_encode(t('Remove font'))?>,
  change_font_family: <?=json_encode(t('Change Font Family'))?>,
  remove_style: <?=json_encode(t('Remove Style'))?>,
  insert_character: <?=json_encode(t('Insert Character'))?>,
  undo: <?=json_encode(t('Undo'))?>,
  redo: <?=json_encode(t('Redo'))?>,
  remove_font_family: <?=json_encode(t('Remove Font Family'))?>,
  remove_font_size: <?=json_encode(t('Remove Font Size'))?>,
  change_font_size: <?=json_encode(t('Change Font Size'))?>,
  /* end concrete5 */
  underline: <?=json_encode(t('Underline'))?>,
  alignment: <?=json_encode(t('Alignment'))?>,
  filename: <?=json_encode(t('Name (optional)'))?>,
  edit: <?=json_encode(t('Edit'))?>,
  upload_label: <?=json_encode(t('Drop file here or '))?>
};

jQuery.Redactor.opts.lang = <?=json_encode($locale)?>;
jQuery.each(jQuery.Redactor.opts.langs.en, function(key, value) {
  if(!(key in jQuery.Redactor.opts.langs[<?=json_encode($locale)?>])) {
    jQuery.Redactor.opts.langs[<?=json_encode($locale)?>][key] = value;
  }
});
<?php

    }

    public static function getFancytreeJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        ?>
jQuery.ui.fancytree.prototype.options.strings.loading = <?=json_encode(t('Loading...'))?>;
jQuery.ui.fancytree.prototype.options.strings.loadError = <?=json_encode(t('Load error!'))?>;
<?php

    }

    public static function getImageEditorJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        ?>
var ccmi18n_imageeditor = {
  loadingControlSets: <?=json_encode(t('Loading Control Sets...'))?>,
  loadingComponents: <?=json_encode(t('Loading Components...'))?>,
  loadingFilters: <?=json_encode(t('Loading Filters...'))?>,
  loadingImage: <?=json_encode(t('Loading Image...'))?>,
  areYouSure: <?=json_encode(t('Are you sure?'))?>
};
        <?php

    }

    public static function getJQueryUIJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        $env = Environment::get();
        /* @var $env \Concrete\Core\Foundation\Environment */
        $alternatives = array(Localization::activeLocale());
        if (Localization::activeLocale() !== Localization::activeLanguage()) {
            $alternatives[] = Localization::activeLanguage();
        }
        $found = null;
        foreach ($alternatives as $alternative) {
            $r = $env->getRecord(DIRNAME_JAVASCRIPT.'/i18n/ui.datepicker-'.str_replace('_', '-', $alternative).'.js');
            if (is_file($r->file)) {
                $found = $r->file;
                break;
            }
        }
        if (isset($found)) {
            readfile($found);
        } else {
            echo '// No jQueryUI translations for '.Localization::activeLocale();
        }
    }
    public static function getTranslatorJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        ?>
ccmTranslator.setI18NDictionart({
  AskDiscardDirtyTranslation: <?=json_encode(t("The current item has changed.\nIf you proceed you will lose your changes.\n\nDo you want to proceed anyway?"))?>,
  Approved: <?=json_encode(tc('Translation', 'Approved')); ?>,
  Comments: <?=json_encode(t('Comments'))?>,
  Context: <?=json_encode(t('Context'))?>,
  ExamplePH: <?=json_encode(t('Example: %s'))?>,
  Filter: <?=json_encode(t('Filter'))?>,
  Original_String: <?=json_encode(t('Original String'))?>,
  Please_fill_in_all_plurals: <?=json_encode(t('Please fill-in all plural forms'))?>,
  Plural_Original_String: <?=json_encode(t('Plural Original String'))?>,
  References: <?=json_encode(t('References'))?>,
  Save_and_Continue: <?=json_encode(t('Save & Continue'))?>,
  Search_for_: <?=json_encode(t('Search for...'))?>,
  Search_in_contexts: <?=json_encode(t('Search in contexts'))?>,
  Search_in_originals: <?=json_encode(t('Search in originals'))?>,
  Search_in_translations: <?=json_encode(t('Search in translations'))?>,
  Show_approved: <?=json_encode(t('Show approved'))?>,
  Show_translated: <?=json_encode(t('Show translated'))?>,
  Show_unapproved: <?=json_encode(t('Show unapproved'))?>,
  Show_untranslated: <?=json_encode(t('Show untranslated'))?>,
  Singular_Original_String: <?=json_encode(t('Singular Original String'))?>,
  Toggle_Dropdown: <?=json_encode(t('Toggle Dropdown'))?>,
  TAB: <?=json_encode(t('[TAB] Forward'))?>,
  TAB_SHIFT: <?=json_encode(t('[SHIFT]+[TAB] Backward'))?>,
  Translate: <?=json_encode(t('Translate'))?>,
  Translation: <?=json_encode(t('Translation'))?>,
  TranslationIsApproved_WillNeedApproval: <?=json_encode(t('This translation is approved: your changes will need approval.')); ?>,
  TranslationIsNotApproved: <?=json_encode(t('This translation is not approved.')); ?>,
  PluralNames: {
    zero: <?=json_encode(tc('PluralCase', 'Zero'))?>,
    one: <?=json_encode(tc('PluralCase', 'One'))?>,
    two: <?=json_encode(tc('PluralCase', 'Two'))?>,
    few: <?=json_encode(tc('PluralCase', 'Few'))?>,
    many: <?=json_encode(tc('PluralCase', 'Many'))?>,
    other: <?=json_encode(tc('PluralCase', 'Other'))?>
  }
});<?php

    }
    public static function getDropzoneJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        ?>
Dropzone.prototype.defaultOptions.dictDefaultMessage = <?=json_encode(t('Drop files here to upload'))?>;
Dropzone.prototype.defaultOptions.dictFallbackMessage = <?=json_encode(t("Your browser does not support drag'n'drop file uploads."))?>;
Dropzone.prototype.defaultOptions.dictFallbackText = <?=json_encode(t('Please use the fallback form below to upload your files like in the olden days.'))?>;
    <?php

    }
    public static function getConversationsJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        ?>
jQuery.fn.concreteConversation.localize({
  Confirm_remove_message: <?=json_encode(t('Remove this message? Replies to it will not be removed'))?>,
  Confirm_mark_as_spam: <?=json_encode(t('Are you sure you want to flag this message as spam?'))?>,
  Warn_currently_editing: <?=json_encode(t('Please complete or cancel the current message editing session before editing this message.'))?>,
  Unspecified_error_occurred: <?=json_encode(t('An unspecified error occurred.'))?>,
  Error_deleting_message: <?=json_encode(t('Something went wrong while deleting this message, please refresh and try again.'))?>,
  Error_flagging_message: <?=json_encode(t('Something went wrong while flagging this message, please refresh and try again.'))?>
});
jQuery.fn.concreteConversationAttachments.localize({
  Too_many_files: <?=json_encode(t('Too many files'))?>,
  Invalid_file_extension: <?=json_encode(t('Invalid file extension'))?>,
  Max_file_size_exceeded: <?=json_encode(t('Max file size exceeded'))?>,
  Error_deleting_attachment: <?=json_encode(t('Something went wrong while deleting this attachment, please refresh and try again.'))?>,
  Confirm_remove_attachment: <?=json_encode(t('Remove this attachment?'))?>
});
        <?php

    }
}
