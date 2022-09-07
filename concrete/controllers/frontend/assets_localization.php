<?php

namespace Concrete\Controller\Frontend;

use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\File\Upload\Dropzone;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Localization;
use Controller;
use Environment;

class AssetsLocalization extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCoreJavascript()
    {
        $content = '
var ccmi18n = ' . json_encode([
    'addAccessEntityDialogTitle' => t('Add Access Entity'),
    'addAreaLayout' => t('Add Layout'),
    'addBlock' => t('Add Block'),
    'addBlockContainer' => t('Add Container'),
    'addBlockContainerMsg' => t('The container has been added successfully.'),
    'addBlockMsg' => t('The block has been added successfully.'),
    'addBlockNew' => t('Add Block'),
    'addBlockPaste' => t('Paste from Clipboard'),
    'addBlockStack' => t('Add Stack'),
    'addBlockStackMsg' => t('The stack has been added successfully'),
    'addonBrowserLoading' => t('Retrieving add-on data from the marketplace.'),
    'advanced' => t('Advanced'),
    'advancedSearch' => t('Advanced Search'),
    'areaLayoutPresets' => t('Layout Presets'),
    'arrangeBlock' => t('Move'),
    'arrangeBlockMsg' => t('Blocks arranged successfully.'),
    'blockAreaMenu' => t('Add Block'),
    'cancel' => t('Cancel'),
    'changeAreaCSS' => t('Design'),
    'changeBlockBaseStyle' => t('Set Block Styles'),
    'changeBlockCSS' => t('Design'),
    'changeBlockTemplate' => t('Block Template'),
    'chooseFont' => t('Choose Font'),
    'chooseUser' => t('Choose a User'),
    'clear' => t('Clear'),
    'closeWindow' => t('Close'),
    'collapse' => t('Collapse'),
    'community' => t('Marketplace'),
    'communityCheckout' => t('Marketplace - Purchase & Checkout'),
    'communityDownload' => t('Marketplace - Download'),
    'compareVersions' => t('Compare Versions'),
    'confirm' => t('Confirm'),
    'confirmCssPresetDelete' => t('Are you sure you want to delete this custom style preset?'),
    'confirmCssReset' => t('Are you sure you want to remove all of these custom styles?'),
    'confirmLayoutPresetDelete' => t('Are you sure you want to delete this layout preset?'),
    'content' => t('Content'),
    'copyBlockToScrapbook' => t('Copy to Clipboard'),
    'copyBlockToScrapbookMsg' => t('The block has been added to your clipboard.'),
    'currentImage' => t('Current Image'),
    'customizeSearch' => t('Customize Search'),
    'deleteAttributeValue' => t('Are you sure you want to remove this value?'),
    'deleteBlock' => t('Block Deleted'),
    'deleteBlockMsg' => t('The block has been removed successfully.'),
    'deleteBlockTitle' => t('Delete'),
    'deleteLayout' => t('Delete'),
    'deleteLayoutOptsTitle' => t('Delete Layout'),
    'editAreaLayout' => t('Edit Layout'),
    'editBlock' => t('Edit'),
    'editBlockWithName' => tc('%s is a block type name', 'Edit %s'),
    'editMode' => t('Edit Mode'),
    'editModeMsg' => t('Let\'s start editing a page.'),
    'editStackContents' => t('Manage Stack Contents'),
    'emptyArea' => t('Empty %s Area', '<%- area_handle %>'),
    'error' => t('Error'),
    'errorCustomStylePresetNoName' => t('You must give your custom style preset a name.'),
    'errorDetails' => t('Details'),
    'expand' => t('Expand'),
    'fontSize' => t('Font Size'),
    'fontWeight' => t('Font Weight'),
    'fullArea' => t('This area is full!'),
    'generalRequestError' => t('An unexpected error occurred.'),
    'go' => t('Go'),
    'helpPopup' => t('Help'),
    'image' => t('Image'),
    'invalidIconType' => t('Invalid icon type.'),
    'italic' => t('Italic'),
    'letterSpacing' => t('Letter spacing'),
    'lineHeight' => t('Line Height'),
    'loadingText' => t('Loading'),
    'lockAreaLayout' => t('Lock Layout'),
    'marketplaceErrorMsg' => t('<p>You package could not be installed.  An unknown error occurred.</p>'),
    'marketplaceInstallMsg' => t('<p>Your package will now be downloaded and installed.</p>'),
    'marketplaceLoadingMsg' => t('<p>Retrieving information from the Concrete Marketplace.</p>'),
    'marketplaceLoginMsg' => t('<p>You must be logged into the Concrete Marketplace to install add-ons and themes.  Please log in.</p>'),
    'marketplaceLoginSuccessMsg' => t('<p>You have successfully logged into the Concrete Marketplace.</p>'),
    'marketplaceLogoutSuccessMsg' => t('<p>You are now logged out of Concrete Marketplace.</p>'),
    'moveLayoutAtBoundary' => t('This layout section can not be moved further in this direction.'),
    'moveLayoutDown' => t('Move Down'),
    'moveLayoutUp' => t('Move Up'),
    'next' => t('Next'),
    'noIE6' => t('Concrete does not support Internet Explorer 6 in edit mode.'),
    'none' => t('None'),
    'notifications' => t('Notifications'),
    'ok' => t('Ok'),
    'permissionsOverrideWarning' => t('Changing this setting will affect this page immediately. Are you sure?'),
    'permissionsUpdatedMessage' => t('The permissions has been successfully updated.'),
    'permissionsUpdatedTitle' => t('Permissions Updated'),
    'previous' => t('Previous'),
    'progressiveOperationLoading' => t('Determining items remaining...'),
    'properties' => t('Page Saved'),
    'requestTimeout' => t('This request took too long.'),
    'save' => t('Save'),
    'savePropertiesMsg' => t('Page Properties saved.'),
    'saveSpeedSettingsMsg' => t('Full page caching settings saved.'),
    'saveUserSettingsMsg' => t('User Settings saved.'),
    'scheduleGuestAccess' => t('Schedule Guest Access'),
    'scheduleGuestAccessSuccess' => t('Timed Access for Guest Users Updated Successfully.'),
    'search' => t('Search'),
    'selectNoResult' => t('No result for %s. Add it?', '{0}'),
    'setAreaPermissions' => t('Set Permissions'),
    'setBlockAlias' => t('Setup on Child Pages'),
    'setBlockComposerSettings' => t('Composer Settings'),
    'setBlockPermissions' => t('Set Permissions'),
    'setPermissionsDeferredMsg' => t('Permission setting saved. You must complete the workflow before this change is active.'),
    'siteActivity' => t('Activity'),
    'size' => t('Size'),
    'themeBrowserLoading' => t('Retrieving theme data from the marketplace.'),
    'themeBrowserTitle' => t('Get More Themes'),
    'underline' => t('Underline'),
    'unlockAreaLayout' => t('Unlock Layout'),
    'updateBlock' => t('Update Block'),
    'updateBlockMsg' => t('The block has been saved successfully.'),
    'uppercase' => t('Uppercase'),
    'user_activate' => t('Activate Users'),
    'user_deactivate' => t('Deactivate Users'),
    'user_delete' => t('Delete'),
    'user_group_add' => t('Add to Group'),
    'user_group_remove' => t('Remove From Group'),
    'x' => t('x'),
]) . ';

var ccmi18n_editor = ' . json_encode([
    'imageLink' => t('Linking to an image'),
    'insertImage' => t('Insert Image'),
    'insertLinkToFile' => t('Insert Link to File'),
    'insertLinkToPage' => t('Link to Page'),
    'lightboxFeatures' => t('Lightbox Features'),
    'sitemap' => t('Sitemap'),
    'snippets' => t('Snippets'),
]) . ';

var ccmi18n_express = ' . json_encode([
    'cancel' => t('Cancel'),
    'chooseEntry' => t('Choose Entry'),
    'dateAdded' => t('Date Added'),
    'dateModified' => t('Date Modified'),
    'entriesTitle' => t('Entries'),
    'name' => t('Name'),
    'search' => t('Search'),
    'select' => t('Select'),
]) . ';

var ccmi18n_sitemap = ' . json_encode([
    'addExternalLink' => t('Add External Link'),
    'addPage' => t('Add Page'),
    'areYouSure' => t('Are you sure?'),
    'backToSitemap' => t('Back to Sitemap'),
    'choosePage' => t('Choose a Page'),
    'copyProgressTitle' => t('Copy Progress'),
    'createdBy' => t('Created By'),
    'date' => t('Date'),
    'deleteExternalLink' => t('Delete'),
    'deletePage' => t('Delete'),
    'deletePageForever' => t('Delete Forever'),
    'deletePages' => t('Delete Pages'),
    'deletePageSuccessDeferredMsg' => t('Delete request saved. You must complete the workflow before the page is fully removed.'),
    'deletePageSuccessMsg' => t('The page has been removed successfully.'),
    'editAlias' => t('Edit Alias'),
    'editExternalLink' => t('Edit External Link'),
    'editInComposer' => t('Edit in Composer'),
    'emptyTrash' => t('Empty Trash'),
    'explorePages' => t('Flat View'),
    'initialPageSearchChooserTip' => t("Let's get some info on what you're looking for."),
    'lastModified' => tc('Page', 'Last Modified'),
    'loadError' => t('Unable to load sitemap data. Response received: '),
    'loadErrorTitle' => t('Unable to load sitemap data.'),
    'loadingText' => t('Loading'),
    'max' => t('max'),
    'moveCopyPage' => t('Move/Copy'),
    'moveCopyPageMessage' => t('Choose a new parent page from the sitemap.'),
    'name' => t('Name'),
    'noResults' => t('No results found.'),
    'on' => t('on'),
    'pageAttributes' => t('Attributes'),
    'pageAttributesTitle' => t('Attributes'),
    'pageDesign' => t('Design &amp; Type'),
    'pageDesignMsg' => t('Theme and page type updated successfully.'),
    'pageLocation' => t('Location'),
    'pageLocationTitle' => t('Location'),
    'pagePermissionsTitle' => t('Page Permissions'),
    'pageVersions' => t('Versions'),
    'previewPage' => t('Preview'),
    'reorderPage' => t('Change Page Order'),
    'reorderPageMessage' => t('Move or reorder pages by dragging their icons.'),
    'restorePage' => t('Restore Page'),
    'results' => t('Result(s)'),
    'search' => t('Search'),
    'searchPages' => t('Search Pages'),
    'searchResults' => t('Search Results'),
    'sendToBottom' => t('Send To Bottom'),
    'sendToTop' => t('Send To Top'),
    'seo' => t('SEO'),
    'setPagePermissions' => t('Permissions'),
    'setPagePermissionsMsg' => t('Page permissions updated successfully.'),
    'speedSettings' => t('Caching'),
    'speedSettingsTitle' => t('Caching'),
    'viewing' => t('Viewing'),
    'visitExternalLink' => t('Visit'),
    'visitPage' => t('Visit'),
]) . ';

var ccmi18n_spellchecker = ' . json_encode([
    'noSuggestions' => t('No Suggestions'),
    'resumeEditing' => t('Resume Editing'),
]) . ';

var ccmi18n_groups = ' . json_encode([
    'editGroup' => t('Edit Group'),
    'editPermissions' => t('Edit Permissions'),
    'id' => t('ID'),
    'name' => t('Name'),
    'noResults' => t('No results found.'),
    'search' => t('Search'),
]) . ';

var ccmi18n_fileuploader = ' . json_encode([
    'cancelButton' => t('Cancel'),
    'confirmButton' => t('Remove pending files'),
    'confirmMessage' => t('Are you sure that you want to close this dialog? All pending files will be removed.'),
    'continueButton' => t('Continue'),
    'createNewDirectoryButton' => t('Create New Folder'),
    'dialogTitle' => t('Import Files'),
    'directoryName' => t('Name'),
    'directoryPlaceholder' => t('Please enter a name...'),
    'dropFilesHere' => t('Drop files here or click to upload'),
    'enterMultipleUrls' => t('Enter URL to valid file(s), one URL per line'),
    'enterSingleUrl' => t('Enter URL to valid file'),
    'errorNotificationTitle' => t('Error'),
    'filename' => t('Filename'),
    'incomingDirectory' => t('Incoming Directory'),
    'invalidFileExtension' => t('Invalid file extension'),
    'loading' => t('Loading...'),
    'noFilesFound' => t('No files found in {0} for the storage location "{1}".'),
    'remoteFiles' => t('Remote Files'),
    'removeFilesAfterPost' => t('Remove files from {0} directory of {1} storage location.'),
    'size' => t('Size'),
    'uploadFilesTo' => t('Upload files to'),
    'uploadSuccessfulMessage' => t('Upload complete.'),
    'uploadSuccessfulTitle' => t('Complete'),
    'yourComputer' => t('Your Computer'),
]) . ';

var ccmi18n_filemanager = ' . json_encode([
    'add' => t('Add'),
    'addFiles' => t('Add Files'),
    'cancel' => t('Cancel'),
    'choose' => t('Choose'),
    'chooseFile' => t('Choose File'),
    'chooseNew' => t('Choose New File'),
    'chosenTooMany' => t('You may only select a single file.'),
    'clear' => t('Clear'),
    'createNewFolder' => t('Create New Folder'),
    'deleteFile' => t('Delete'),
    'download' => t('Download'),
    'duplicate' => t('Copy'),
    'duplicateFile' => t('Copy File'),
    'edit' => t('Edit'),
    'fileManager' => t('File Manager'),
    'fileSet' => t('File Set'),
    'FTYPE_APPLICATION' => FileType::T_APPLICATION,
    'FTYPE_AUDIO' => FileType::T_AUDIO,
    'FTYPE_DOCUMENT' => FileType::T_DOCUMENT,
    'FTYPE_IMAGE' => FileType::T_IMAGE,
    'FTYPE_TEXT' => FileType::T_TEXT,
    'FTYPE_VIDEO' => FileType::T_VIDEO,
    'id' => t('ID'),
    'import' => t('Import'),
    'initialExternalFileProviderChooserTip' => t("Let's get some info on what you're looking for."),
    'initialSearchChooserTip' => t("Let's get some info on what you're looking for."),
    'jumpToFolder' => t('Jump to Folder'),
    'name' => t('Name'),
    'pending' => t('Pending'),
    'permissions' => t('Permissions'),
    'properties' => t('Properties'),
    'PTYPE_ALL' => '', // /*FilePermissions::PTYPE_ALL
    'PTYPE_CUSTOM' => '', // FilePermissions::PTYPE_CUSTOM
    'PTYPE_NONE' => '', // FilePermissions::PTYPE_NONE
    'replace' => t('Replace'),
    'recentlyUploaded' => t('Recently Uploaded'),
    'rescan' => t('Rescan'),
    'search' => t('Search'),
    'searchPresets' => t('Search Presets'),
    'select' => t('Choose'),
    'selectFileSet' => t('Select a Set'),
    'selectPreset' => t('Select a Preset'),
    'sets' => t('Sets'),
    'specifyName' => t('Please enter a name...'),
    'thumbnailImages' => t('Thumbnail Images'),
    'title' => t('File Manager'),
    'upload' => t('Upload'),
    'uploadComplete' => t('Upload Complete'),
    'uploaded' => tc('UploadDateTime', 'Uploaded'),
    'uploadErrorChooseFile' => t('You must choose a file.'),
    'uploadFailed' => t('Upload Failed'),
    'uploadProgress' => t('Upload Progress'),
    'view' => t('View'),
    'uploadFiles' => t('Upload Files'),
]) . ';

var ccmi18n_chosen = ' . json_encode([
    'no_results_text' => t(/*i18n After this text we have a search criteria: for instance 'No results match "Criteria"'*/'No results match'),
    'placeholder_text_multiple' => t('Select Some Options'),
    'placeholder_text_single' => t('Select an Option'),
]) . ';

var ccmi18n_topics = ' . json_encode([
    'addCategory' => t('Add Category'),
    'addTopic' => t('Add Topic'),
    'cloneCategory' => t('Clone Category'),
    'cloneTopic' => t('Clone Topic'),
    'deleteCategory' => t('Delete Category'),
    'deleteTopic' => t('Delete Topic'),
    'editCategory' => t('Edit Category'),
    'editPermissions' => t('Edit Permissions'),
    'editTopic' => t('Edit Topic'),
]) . ';

var ccmi18n_tree = ' . json_encode([
    'add' => t('Add'),
    'delete' => t('Delete'),
    'edit' => t('Edit'),
]) . ';

var ccmi18n_tourist = ' . json_encode([
    'localization' => [
        'buttonTexts' => [
            'prevButton' => t('Prev'),
            'nextButton' => t('Next'),
            'pauseButton' => t('Pause'),
            'resumeButton' => t('Resume'),
            'endTourButton' => t('End Tour'),
        ],
    ],
    'template' => implode('', [
        '<div class="popover ccm-help-tour" role="tooltip">',
        '<div class="popover-arrow"></div>',
        '<a class="ccm-help-tour-close fas fa-times" href="#" data-role="end"></a>',
        '<div class="popover-body"></div>',
        '<div class="ccm-help-tour-footer d-flex justify-content-between">',
        '<div class="ccm-help-tour-position">',
        t(/*i18n: %1$s and %2$s are two numbers*/'%1$s of %2$s', '<span class="ccm-help-tour-position-index"></span>', '<span class="ccm-help-tour-position-count"></span>'),
        '</div>',
        '<div class="popover-navigation text-end">',
        '<div class="btn-group">',
        '<a href="#" data-role="prev">&lt; ' . t('Prev') . '</a>',
        '<a href="#" data-role="next">' . t('Next') . ' &gt;</a>',
        '<a href="#" data-role="pause-resume" data-pause-text="' . t('Pause') . '" data-resume-text="' . t('Resume') . '">' . t('Pause') . '</a>',
        '</div>',
        '</div>',
        '</div>',
        '<div>',
    ]),
]) . ';

var ccmi18n_styleCustomizer = ' . json_encode([
    'addCustomCSS' => t('Add Custom CSS'),
    'applyCustomizations' => t('Apply customizations to:'),
    'cancel' => t('Cancel'),
    'choose' => t('Choose'),
    'chooseImageText' => t('Choose Image'),
    'clearColorSelection' => t('Clear Color Selection'),
    'confirm' => t('Confirm'),
    'confirmSkinDeletion' => t('Are you sure you want to delete this custom skin? This cannot be undone.'),
    'create' => t('Create'),
    'currentlyUsingText' => t('Currently using %s'),
    'customCSS' => t('Custom CSS'),
    'delete' => t('Delete'),
    'editCSS' => t('Edit CSS'),
    'entireSite' => t('Entire Site'),
    'fontFamily' => t('Font Family'),
    'fontSize' => t('Size'),
    'fontStyle' => t('Font Style'),
    'fontWeight' => t('Font Weight'),
    'fontWeights' => [
        'normal' => tc(/*i18n: see https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight*/'FontWeight', 'Normal'),
        'bold' => tc(/*i18n: see https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight*/'FontWeight', 'Bold'),
        'bolder' => tc(/*i18n: see https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight*/'FontWeight', 'Bolder'),
        'light' => tc(/*i18n: see https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight*/'FontWeight', 'Light'),
    ],
    'loading' => t('Loading...'),
    'noColorSelected' => t('No Color Selected'),
    'save' => t('Save'),
    'skinName' => t('Skin Name'),
    'textDecoration' => t('Text Decoration'),
    'textTransform' => t('Text Transform'),
    'textTransforms' => [
        'none' => tc(/*i18n: see https://developer.mozilla.org/en-US/docs/Web/CSS/text-transform*/'TextTransform', 'None'),
        'uppercase' => tc(/*i18n: see https://developer.mozilla.org/en-US/docs/Web/CSS/text-transform*/'TextTransform', 'Uppercase'),
        'lowercase' => tc(/*i18n: see https://developer.mozilla.org/en-US/docs/Web/CSS/text-transform*/'TextTransform', 'Lowercase'),
        'capitalize' => tc(/*i18n: see https://developer.mozilla.org/en-US/docs/Web/CSS/text-transform*/'TextTransform', 'Capitalize'),
        'full-width' => tc(/*i18n: see https://developer.mozilla.org/en-US/docs/Web/CSS/text-transform*/'TextTransform', 'Full Width'),
        'full-width-kana' => tc(/*i18n: see https://developer.mozilla.org/en-US/docs/Web/CSS/text-transform*/'TextTransform', 'Full Width Kana'),
    ],
    'thisPage' => t('This Page'),
    'togglePaletteLess' => t('Less'),
    'togglePaletteMore' => t('More'),
]) . ';

var ccmi18n_helpGuides = ' . json_encode([
    'add-page' => [
        ['title' => t('Pages Panel'), 'text' => t('The pages is where you go to add a new page to your site, or jump between existing pages. To open the pages panel, click the icon.')],
        ['title' => t('Page Types'), 'text' => t('This is your list of page types. Click any of them to add a page.')],
        ['title' => t('Sitemap'), 'text' => t('This is your sitemap. Use it to easily navigate your site.')],
    ],
    'change-content-edit-mode' => [
        ['title' => t('Edit Mode Active'), 'text' => t('The highlighted button makes it obvious you\'re in edit mode.')],
        ['title' => t('Edit the Block'), 'text' => t('Just roll over any content on the page. Click or tap to get the edit menu for that block.')],
        ['title' => t('Edit Menu'), 'text' => t('Use this menu to edit a block\'s contents, change its display, or remove it entirely.')],
        ['title' => t('Save Changes'), 'text' => t("When you're done editing you can Save Changes for other editors to see, or Publish Changes to make your changes live immediately.")],
    ],
    'add-content-edit-mode' => [
        ['title' => t('Add Mode Active'), 'text' => t('The highlighted button makes it obvious you\'re in Add Content mode.')],
        ['title' => t('Add Panel'), 'text' => t('This is the Add Content Panel.')],
        ['title' => t('Content Selector'), 'text' => t('Click here to choose between adding blocks, clipboard items, stacks and stack contents.')],
        ['title' => t('Search Blocks'), 'text' => t('You can easily filter the blocks in the panel by searching here.')],
        ['title' => t('Add Blocks'), 'text' => t('Click and drag blocks from the add panel into the page to add them.')],
    ],
    'change-content' => [
        ['title' => t('Enter Edit Mode'), 'text' => t('First, click the "Edit Page" button. This will enter edit mode for this page.')],
    ],
    'add-content' => [
        ['title' => t('Enter Edit Mode'), 'text' => t('Click the "Add Content" button to enter edit mode, with the Add Content panel active.')],
    ],
    'dashboard' => [
        ['title' => t('Dashboard Panel'), 'text' => t('The dashboard is where you go to manage aspects of your site that have to do with more than the content on just one page. Click the sliders icon.')],
        ['title' => t('Sitemap'), 'text' => t("The sitemap lets manage the structure of your website. You can delete pages you don't need, or drag them around the tree to suit your needs.")],
    ],
    'location-panel' => [
        ['title' => t('Choose Location'), 'text' => t('Click this button to choose the location of the page in your sitemap. If saved, the page will be moved to this location.')],
        ['title' => t('Page URLs'), 'text' => t('Control the URLs used to access your page here. Non-canonical URLs will redirect to your page; canonical URLs can be either generated or automatically or overridden. Sub-pages to this page start with canonical URLs by default.')],
    ],
    'personalize' => [
        ['title' => t('Properties Panel'), 'text' => t('The properties panel controls data and details about the current page including design customizations. To open the properties panel, click the gear icon.')],
        ['title' => t('Page Design'), 'text' => t('From here you can change your page template and customize your page\'s styles.')],
        ['title' => t('Customize'), 'text' => t('Click here to load the theme customizer for the page.')],
    ],
    'toolbar' => [
        ['title' => t('Edit Mode'), 'text' => t('Edit anything on this page by clicking the pencil icon.')],
        ['title' => t('Settings'), 'text' => t('Change the general look and options like SEO and permissions. Delete the page or roll versions back from here as well.')],
        ['title' => t('Add Content'), 'text' => t('Place a new block on the page. Copy one using the clipboard, or try a reusable stack.')],
        ['title' => t('Intelligent Search'), 'text' => t('At a loss? Try searching here. You can find anything from pages in your site to settings and how-to documentation.')],
        ['title' => t('Add Page'), 'text' => t('Add a new page to your site, or quickly jump around your sitemap.')],
        ['title' => t('Dashboard'), 'text' => t('Anything that isn\'t specific to this page happens here. Manage users, files, reporting data, and site-wide settings.')],
    ],
]) . ';

var ccmi18n_gallery = ' . json_encode([
    'addImages' => t('Add Images'),
    'customAttributes' => t('Custom Attributes'),
    'displayOptions' => t('Display Options'),
    'editAttributes' => t('Edit Attributes'),
    'images' => t('Images'),
    'includeOriginalDownloadLink' => t('Include link to download original'),
    'removeFromGallery' => t('Remove from Gallery'),
    'settings' => t('Settings'),
]) . ';

var ccmi18n_users = ' . json_encode([
    'cancel' => t('Cancel'),
    'choose' => t('Choose'),
    'date' => t('Date'),
    'email' => t('Email'),
    'initialUserSearchChooserTip' => t("Let's get some info on what you're looking for."),
    'numLogins' => t('# Logins'),
    'search' => t('Search'),
    'status' => t('Status'),
    'username' => t('Username'),
]) . ';

var ccmi18n_boards = ' . json_encode([
    'dateAndTime' => t('Date & Time'),
    'name' => t('Name'),
    'noStartDate' => t('No Start Date'),
    'preview' => t('Preview'),
    'slot' => t('Slot'),
    'unpublishedRules' => t('Unpublished Rules'),
]) . ';

var ccmi18n_processes = ' . json_encode([
    'name' => t('Name'),
    'dateStarted' => t('Date Started'),
    'dateCompleted' => t('Date Completed'),
    'loading' => t('Loading...'),
    'deleteProcess' => t('Delete Process'),
    'confirmDeletion' => t('Delete this process log entry? The record of the process along with any logs will be removed.'),
    'close' => t('Close'),
    'delete' => t('Delete'),
]) . ';
        ';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSelect2Javascript()
    {
        $locale = str_replace('_', '-', Localization::activeLocale());
        if ($locale === 'en-US') {
            $content = "/* select2: no needs to translate {$locale} */\n";
        } else {
            $env = Environment::get();
            // @var $env \Concrete\Core\Foundation\Environment
            $language = Localization::activeLanguage();
            $alternatives = [$locale];
            if (strcmp($locale, $language) !== 0) {
                $alternatives[] = $language;
            }
            $found = null;
            foreach ($alternatives as $alternative) {
                $r = $env->getRecord(DIRNAME_JAVASCRIPT . "/i18n/select2_locale_{$alternative}.js");
                if (is_file($r->file)) {
                    $found = $r->file;
                    break;
                }
            }
            if (isset($found)) {
                $content = @file_get_contents($found);
                if ($content === false) {
                    $content = "/* select2: failed to read translations for {$alternative} */";
                }
            } else {
                $content = '/* select2: no translations for ' . implode(', ', $alternatives) . ' */';
            }
        }

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRedactorJavascript()
    {
        $locale = Localization::activeLocale();
        $content = '
jQuery.Redactor.opts.langs[' . json_encode($locale) . '] = ' . json_encode([
    'html' => t('HTML'),
    'video' => t('Insert Video'),
    'image' => t('Insert Image'),
    'table' => t('Table'),
    'link' => t('Link'),
    'link_insert' => t('Insert link'),
    'link_edit' => t('Edit link'),
    'unlink' => t('Unlink'),
    'formatting' => t('Formatting'),
    'paragraph' => t('Normal text'),
    'quote' => t('Quote'),
    'code' => t('Code'),
    'header1' => t('Header 1'),
    'header2' => t('Header 2'),
    'header3' => t('Header 3'),
    'header4' => t('Header 4'),
    'header5' => t('Header 5'),
    'bold' => t('Bold'),
    'italic' => t('Italic'),
    'fontcolor' => t('Font Color'),
    'backcolor' => t('Back Color'),
    'unorderedlist' => t('Unordered List'),
    'orderedlist' => t('Ordered List'),
    'outdent' => t('Outdent'),
    'indent' => t('Indent'),
    'cancel' => t('Cancel'),
    'insert' => t('Insert'),
    'save' => t('Save'),
    '_delete' => t('Delete'),
    'insert_table' => t('Insert Table'),
    'insert_row_above' => t('Add Row Above'),
    'insert_row_below' => t('Add Row Below'),
    'insert_column_left' => t('Add Column Left'),
    'insert_column_right' => t('Add Column Right'),
    'delete_column' => t('Delete Column'),
    'delete_row' => t('Delete Row'),
    'delete_table' => t('Delete Table'),
    'rows' => t('Rows'),
    'columns' => t('Columns'),
    'add_head' => t('Add Head'),
    'delete_head' => t('Delete Head'),
    'title' => t('Title'),
    'image_position' => t('Position'),
    'none' => t('None'),
    'left' => t('Left'),
    'right' => t('Right'),
    'center' => t('Center'),
    'image_web_link' => t('Image Web Link'),
    'text' => t('Text'),
    'mailto' => t('Email'),
    'web' => t('URL'),
    'video_html_code' => t('Video Embed Code or Youtube/Vimeo Link'),
    'file' => t('Insert File'),
    'upload' => t('Upload'),
    'download' => t('Download'),
    'choose' => t('Choose'),
    'or_choose' => t('Or choose'),
    'drop_file_here' => t('Drop file here'),
    'align_left' => t('Align text to the left'),
    'align_center' => t('Center text'),
    'align_right' => t('Align text to the right'),
    'align_justify' => t('Justify text'),
    'horizontalrule' => t('Insert Horizontal Rule'),
    'deleted' => t('Deleted'),
    'anchor' => t('Anchor'),
    'open_link' => t('Open Link'),
    'link_new_tab' => t('Open link in new tab'),
    // concrete
    'link_same_window' => t('Open link in same window'),
    'in_lightbox' => t('Open link in Lightbox'),
    'lightbox_link_type' => t('Link Type'),
    'lightbox_link_type_iframe' => t('Web Page'),
    'lightbox_link_type_image' => t('Image'),
    'lightbox_link_type_iframe_options' => t('Frame Options'),
    'lightbox_link_type_iframe_width' => t('Width'),
    'lightbox_link_type_iframe_height' => t('Height'),
    'customStyles' => t('Custom Styles'),
    'remove_font' => t('Remove font'),
    'change_font_family' => t('Change Font Family'),
    'remove_style' => t('Remove Style'),
    'insert_character' => t('Insert Character'),
    'undo' => t('Undo'),
    'redo' => t('Redo'),
    'remove_font_family' => t('Remove Font Family'),
    'remove_font_size' => t('Remove Font Size'),
    'change_font_size' => t('Change Font Size'),
    // end concrete
    'underline' => t('Underline'),
    'alignment' => t('Alignment'),
    'filename' => t('Name (optional)'),
    'edit' => t('Edit'),
    'upload_label' => t('Drop file here or '),
]) . ';

jQuery.Redactor.opts.lang = ' . json_encode($locale) . ';

jQuery.each(jQuery.Redactor.opts.langs.en, function(key, value) {
  if(!(key in jQuery.Redactor.opts.langs[' . json_encode($locale) . '])) {
    jQuery.Redactor.opts.langs[' . json_encode($locale) . '][key] = value;
  }
});
        ';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFancytreeJavascript()
    {
        $content = '
jQuery.ui.fancytree.prototype.options.strings.loading = ' . json_encode(t('Loading...')) . ';
jQuery.ui.fancytree.prototype.options.strings.loadError = ' . json_encode(t('Load error!')) . ';
        ';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getImageEditorJavascript()
    {
        $content =
            'var ccmi18n_imageeditor = ' . json_encode([
                'loadingControlSets' => t('Loading Control Sets...'),
                'loadingComponents' => t('Loading Components...'),
                'loadingFilters' => t('Loading Filters...'),
                'loadingImage' => t('Loading Image...'),
                'areYouSure' => t('Are you sure?'),
            ]) . ';
';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getJQueryUIJavascript()
    {
        $env = Environment::get();
        // @var $env \Concrete\Core\Foundation\Environment
        $alternatives = [Localization::activeLocale()];
        if (Localization::activeLocale() !== Localization::activeLanguage()) {
            $alternatives[] = Localization::activeLanguage();
        }
        $found = null;
        foreach ($alternatives as $alternative) {
            $r = $env->getRecord(DIRNAME_JAVASCRIPT . '/i18n/jquery-ui/datepicker-' . str_replace('_', '-', $alternative) . '.js');
            if (is_file($r->file)) {
                $found = $r->file;
                break;
            }
        }
        if (isset($found)) {
            $content = @file_get_contents($found);
            if ($content === false) {
                $content = "/* jQueryUI: failed to read translations for {$alternative} */";
            }
        } else {
            $content = '/* jQueryUI: no translations for ' . implode(', ', $alternatives) . ' */';
        }

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTranslatorJavascript()
    {
        $content = '
ccmTranslator.setI18NDictionart(' . json_encode([
    'Approved' => tc('Translation', 'Approved'),
    'Approve_and_Continue' => t('Approve & Continue'),
    'AskDiscardDirtyTranslation' => t("The current item has changed.\nIf you proceed you will lose your changes.\n\nDo you want to proceed anyway?"),
    'Comments' => t('Comments'),
    'Context' => t('Context'),
    'ExamplePH' => t('Example: %s'),
    'Filter' => t('Filter'),
    'Keystroke_ctrl_return' => t('[CTRL]+[RETURN]'),
    'Keystroke_ctrl_shift_return' => t('[CTRL]+[SHIFT]+[RETURN]'),
    'No_newlines_in_translations_please' => t('Please don\'t use new lines in translations (there\'s no new line in the source string)'),
    'Original_String' => t('Original String'),
    'Please_fill_in_all_plurals' => t('Please fill-in all plural forms'),
    'Plural_Original_String' => t('Plural Original String'),
    'References' => t('References'),
    'Save_and_Continue' => t('Save & Continue'),
    'Search_for_' => t('Search for...'),
    'Search_in_contexts' => t('Search in contexts'),
    'Search_in_originals' => t('Search in originals'),
    'Search_in_translations' => t('Search in translations'),
    'Show_approved' => t('Show approved'),
    'Show_translated' => t('Show translated'),
    'Show_unapproved' => t('Show unapproved'),
    'Show_untranslated' => t('Show untranslated'),
    'Singular_Original_String' => t('Singular Original String'),
    'Toggle_Dropdown' => t('Toggle Dropdown'),
    'Translate' => t('Translate'),
    'Translation' => t('Translation'),
    'TranslationIsApproved_WillNeedApproval' => t('This translation is approved: your changes will need approval.'),
    'TranslationIsNotApproved' => t('This translation is not approved.'),
    'PluralNames' => [
        'zero' => tc('PluralCase', 'Zero'),
        'one' => tc('PluralCase', 'One'),
        'two' => tc('PluralCase', 'Two'),
        'few' => tc('PluralCase', 'Few'),
        'many' => tc('PluralCase', 'Many'),
        'other' => tc('PluralCase', 'Other'),
    ],
]) . ');
        ';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDropzoneJavascript()
    {
        $options = $this->app->make(Dropzone::class)->getLocalizationOptions();

        $content = '';
        foreach ($options as $optionKey => $optionValue) {
            $content .= 'Dropzone.prototype.defaultOptions[' . json_encode($optionKey) . '] = ' . json_encode($optionValue) . ";\n";
        }

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getConversationsJavascript()
    {
        $content = '
jQuery.fn.concreteConversation.localize(' . json_encode([
    'Confirm_remove_message' => t('Remove this message? Replies to it will not be removed'),
    'Confirm_mark_as_spam' => t('Are you sure you want to flag this message as spam?'),
    'Warn_currently_editing' => t('Please complete or cancel the current message editing session before editing this message.'),
    'Unspecified_error_occurred' => t('An unspecified error occurred.'),
    'Error_deleting_message' => t('Something went wrong while deleting this message, please refresh and try again.'),
    'Error_flagging_message' => t('Something went wrong while flagging this message, please refresh and try again.'),
]) . ');

jQuery.fn.concreteConversationAttachments.localize(' . json_encode([
    'Too_many_files' => t('Too many files'),
    'Invalid_file_extension' => t('Invalid file extension'),
    'Max_file_size_exceeded' => t('Max file size exceeded'),
    'Error_deleting_attachment' => t('Something went wrong while deleting this attachment, please refresh and try again.'),
    'Confirm_remove_attachment' => t('Remove this attachment?'),
]) . ');
        ';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMomentJavascript()
    {
        $localeParts = explode('-', str_replace('_', '-', strtolower(Localization::activeLocale())));
        $alternatives = [];
        if (isset($localeParts[1])) {
            $alternatives[] = "{$localeParts[0]}-{$localeParts[1]}";
        }
        $alternatives[] = $localeParts[0];
        $locator = $this->app->make(FileLocator::class);
        $found = false;
        foreach ($alternatives as $alternative) {
            foreach ($alternatives as $alternative) {
                $r = $locator->getRecord(DIRNAME_JAVASCRIPT . "/i18n/moment/{$alternative}.js");
                if ($r->exists()) {
                    $found = true;
                    $content = file_get_contents($r->getFile()) . ";\n;moment.locale(" . json_encode($alternative) . ");\n";
                    break;
                }
            }
        }
        if ($found === false) {
            $content = '/* moment: no translations for ' . implode(', ', $alternatives) . ' */';
        }

        return $this->createJavascriptResponse($content);
    }

    /**
     * @param string $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function createJavascriptResponse($content)
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);

        return $rf->create(
            $content,
            200,
            [
                'Content-Type' => 'application/javascript; charset=' . APP_CHARSET,
            ]
        );
    }
}
