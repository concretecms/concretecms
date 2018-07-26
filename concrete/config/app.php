<?php

use Concrete\Core\Asset\Asset;
use Concrete\Core\File\Type\Type as FileType;

return [
    'debug' => false,
    'namespace' => 'Application',

    /*
     * Core Aliases
     */
    'aliases' => [
        'Area' => '\Concrete\Core\Area\Area',
        'Asset' => '\Concrete\Core\Asset\Asset',
        'AssetList' => '\Concrete\Core\Asset\AssetList',
        'AttributeSet' => '\Concrete\Core\Attribute\Set',
        'AuthenticationType' => '\Concrete\Core\Authentication\AuthenticationType',
        'Block' => '\Concrete\Core\Block\Block',
        'BlockType' => '\Concrete\Core\Block\BlockType\BlockType',
        'BlockTypeList' => '\Concrete\Core\Block\BlockType\BlockTypeList',
        'BlockTypeSet' => '\Concrete\Core\Block\BlockType\Set',
        'Cache' => '\Concrete\Core\Cache\Cache',
        'Request' => '\Concrete\Core\Http\Request',
        'CacheLocal' => '\Concrete\Core\Cache\CacheLocal',
        'Collection' => '\Concrete\Core\Page\Collection\Collection',
        'CollectionAttributeKey' => '\Concrete\Core\Attribute\Key\CollectionKey',
        'CollectionVersion' => '\Concrete\Core\Page\Collection\Version\Version',
        'ConcreteAuthenticationTypeController' => '\Concrete\Authentication\Concrete\Controller',
        'Controller' => '\Concrete\Core\Controller\Controller',
        'Conversation' => '\Concrete\Core\Conversation\Conversation',
        'ConversationEditor' => '\Concrete\Core\Conversation\Editor\Editor',
        'ConversationFlagType' => '\Concrete\Core\Conversation\FlagType\FlagType',
        'ConversationMessage' => '\Concrete\Core\Conversation\Message\Message',
        'ConversationRatingType' => '\Concrete\Core\Conversation\Rating\Type',
        'Cookie' => '\Concrete\Core\Cookie\Cookie',
        'Environment' => '\Concrete\Core\Foundation\Environment',
        'FacebookAuthenticationTypeController' => '\Concrete\Authentication\Facebook\Controller',
        'File' => '\Concrete\Core\File\File',
        'FileAttributeKey' => '\Concrete\Core\Attribute\Key\FileKey',
        'FileImporter' => '\Concrete\Core\File\Importer',
        'FileList' => '\Concrete\Core\File\FileList',
        'FilePermissions' => '\Concrete\Core\Legacy\FilePermissions',
        'FileSet' => '\Concrete\Core\File\Set\Set',
        'GlobalArea' => '\Concrete\Core\Area\GlobalArea',
        'Group' => '\Concrete\Core\User\Group\Group',
        'GroupList' => '\Concrete\Core\User\Group\GroupList',
        'GroupSet' => '\Concrete\Core\User\Group\GroupSet',
        'GroupSetList' => '\Concrete\Core\User\Group\GroupSetList',
        'GroupTree' => '\Concrete\Core\Tree\Type\Group',
        'GroupTreeNode' => '\Concrete\Core\Tree\Node\Type\Group',
        'Job' => '\Concrete\Core\Job\Job',
        'JobSet' => '\Concrete\Core\Job\Set',
        'Loader' => '\Concrete\Core\Legacy\Loader',
        'Localization' => '\Concrete\Core\Localization\Localization',
        'Marketplace' => '\Concrete\Core\Marketplace\Marketplace',
        'Package' => '\Concrete\Core\Package\Package',
        'Page' => '\Concrete\Core\Page\Page',
        'PageCache' => '\Concrete\Core\Cache\Page\PageCache',
        'PageController' => '\Concrete\Core\Page\Controller\PageController',
        'PageEditResponse' => '\Concrete\Core\Page\EditResponse',
        'PageList' => '\Concrete\Core\Page\PageList',
        'PageTemplate' => '\Concrete\Core\Page\Template',
        'PageTheme' => '\Concrete\Core\Page\Theme\Theme',
        'PageType' => '\Concrete\Core\Page\Type\Type',
        'PermissionAccess' => '\Concrete\Core\Permission\Access\Access',
        'PermissionKey' => '\Concrete\Core\Permission\Key\Key',
        'PermissionKeyCategory' => '\Concrete\Core\Permission\Category',
        'Permissions' => '\Concrete\Core\Permission\Checker',
        'Queue' => '\Concrete\Core\Foundation\Queue\Queue',
        'QueueableJob' => '\Concrete\Core\Job\QueueableJob',
        'Redirect' => '\Concrete\Core\Routing\Redirect',
        'RedirectResponse' => '\Concrete\Core\Routing\RedirectResponse',
        'Response' => '\Concrete\Core\Http\Response',
        'Router' => '\Concrete\Core\Routing\Router',
        'SinglePage' => '\Concrete\Core\Page\Single',
        'Stack' => '\Concrete\Core\Page\Stack\Stack',
        'StackList' => '\Concrete\Core\Page\Stack\StackList',
        'StartingPointPackage' => '\Concrete\Core\Package\StartingPointPackage',
        'TaskPermission' => '\Concrete\Core\Legacy\TaskPermission',
        'User' => '\Concrete\Core\User\User',
        'UserAttributeKey' => '\Concrete\Core\Attribute\Key\UserKey',
        'UserList' => '\Concrete\Core\User\UserList',
        'View' => '\Concrete\Core\View\View',
        'Workflow' => '\Concrete\Core\Workflow\Workflow',
    ],

    /*
     * Core Providers
     */
    'providers' => [
        // Router service provider
        'core_router' => 'Concrete\Core\Routing\RoutingServiceProvider',

        'core_file' => '\Concrete\Core\File\FileServiceProvider',
        'core_encryption' => '\Concrete\Core\Encryption\EncryptionServiceProvider',
        'core_validation' => '\Concrete\Core\Validation\ValidationServiceProvider',
        'core_localization' => '\Concrete\Core\Localization\LocalizationServiceProvider',
        'core_exporter' => '\Concrete\Core\Export\ExportServiceProvider',
        'core_multilingual' => '\Concrete\Core\Multilingual\MultilingualServiceProvider',
        'core_feed' => '\Concrete\Core\Feed\FeedServiceProvider',
        'core_html' => '\Concrete\Core\Html\HtmlServiceProvider',
        'core_editor' => '\Concrete\Core\Editor\EditorServiceProvider',
        'core_mail' => '\Concrete\Core\Mail\MailServiceProvider',
        'core_application' => '\Concrete\Core\Application\ApplicationServiceProvider',
        'core_utility' => '\Concrete\Core\Utility\UtilityServiceProvider',
        'core_content_importer' => '\Concrete\Core\Backup\ContentImporter\ContentImporterServiceProvider',
        'core_manager_grid_framework' => '\Concrete\Core\Page\Theme\GridFramework\ManagerServiceProvider',
        'core_manager_pagination_view' => '\Concrete\Core\Search\Pagination\View\ManagerServiceProvider',
        'core_manager_page_type' => '\Concrete\Core\Page\Type\ManagerServiceProvider',
        'core_manager_layout_preset_provider' => '\Concrete\Core\Area\Layout\Preset\Provider\ManagerServiceProvider',
        'core_manager_search_fields' => '\Concrete\Core\Search\Field\ManagerServiceProvider',
        'core_permissions' => '\Concrete\Core\Permission\PermissionServiceProvider',
        'core_database' => '\Concrete\Core\Database\DatabaseServiceProvider',
        'core_form' => '\Concrete\Core\Form\FormServiceProvider',
        'core_session' => '\Concrete\Core\Session\SessionServiceProvider',
        'core_system' => '\Concrete\Core\System\SystemServiceProvider',
        'core_cookie' => '\Concrete\Core\Cookie\CookieServiceProvider',
        'core_http' => '\Concrete\Core\Http\HttpServiceProvider',
        'core_events' => '\Concrete\Core\Events\EventsServiceProvider',
        'core_whoops' => '\Concrete\Core\Error\Provider\WhoopsServiceProvider',
        'core_logging' => '\Concrete\Core\Logging\LoggingServiceProvider',
        'core_element' => '\Concrete\Core\Filesystem\FilesystemServiceProvider',
        'core_notification' => '\Concrete\Core\Notification\NotificationServiceProvider',
        'core_cache' => '\Concrete\Core\Cache\CacheServiceProvider',
        'core_url' => '\Concrete\Core\Url\UrlServiceProvider',
        'core_devices' => '\Concrete\Core\Device\DeviceServiceProvider',
        'core_imageeditor' => '\Concrete\Core\ImageEditor\EditorServiceProvider',
        'core_user' => '\Concrete\Core\User\UserServiceProvider',
        'core_service_manager' => '\Concrete\Core\Service\Manager\ServiceManagerServiceProvider',
        'core_site' => '\Concrete\Core\Site\ServiceProvider',
        'core_search' => \Concrete\Core\Search\SearchServiceProvider::class,
        'core_geolocator' => 'Concrete\Core\Geolocator\GeolocatorServiceProvider',
        'core_calendar' => 'Concrete\Core\Calendar\CalendarServiceProvider',

        // Authentication
        'core_oauth' => '\Concrete\Core\Authentication\Type\OAuth\ServiceProvider',
        'core_auth_community' => '\Concrete\Core\Authentication\Type\Community\ServiceProvider',
        'core_auth_google' => '\Concrete\Core\Authentication\Type\Google\ServiceProvider',

        // Validator
        'core_validator' => '\Concrete\Core\Validator\ValidatorServiceProvider',
        'core_validator_password' => '\Concrete\Core\Validator\PasswordValidatorServiceProvider',
        'core_validator_user_name' => '\Concrete\Core\Validator\UserNameValidatorServiceProvider',
        'core_validator_user_email' => '\Concrete\Core\Validator\UserEmailValidatorServiceProvider',

        // Express
        'core_attribute' => '\Concrete\Core\Attribute\AttributeServiceProvider',
        'core_express' => '\Concrete\Core\Express\ExpressServiceProvider',

        // Tracker
        'core_usagetracker' => '\Concrete\Core\Statistics\UsageTracker\ServiceProvider',
    ],

    /*
     * Core Facades
     */
    'facades' => [
        'Core' => '\Concrete\Core\Support\Facade\Application',
        'Session' => '\Concrete\Core\Support\Facade\Session',
        'Cookie' => '\Concrete\Core\Support\Facade\Cookie',
        'Database' => '\Concrete\Core\Support\Facade\Database',
        'ORM' => '\Concrete\Core\Support\Facade\DatabaseORM',
        'Events' => '\Concrete\Core\Support\Facade\Events',
        'Express' => '\Concrete\Core\Support\Facade\Express',
        'Route' => '\Concrete\Core\Support\Facade\Route',
        'Site' => '\Concrete\Core\Support\Facade\Site',
        'UserInfo' => '\Concrete\Core\Support\Facade\UserInfo',
        'Element' => '\Concrete\Core\Support\Facade\Element',
        'Log' => '\Concrete\Core\Support\Facade\Log',
        'Image' => '\Concrete\Core\Support\Facade\Image',
        'Config' => '\Concrete\Core\Support\Facade\Config',
        'URL' => '\Concrete\Core\Support\Facade\Url',
    ],

    'entity_namespaces' => [
        'calendar' => 'Concrete\Core\Entity\Calendar',
    ],

    'package_items' => [
        'antispam_library',
        'attribute_key_category',
        'attribute_key',
        'attribute_set',
        'attribute_type',
        'authentication_type',
        'block_type',
        'block_type_set',
        'express_entity',
        'captcha_library',
        'content_editor_snippet',
        'conversation_rating_type',
        'geolocator_library',
        'group',
        'group_set',
        'job',
        'mail_importer',
        'permission_access_entity_type',
        'permission_key',
        'permission_key_category',
        'page_template',
        'site_type',
        'page_type',
        'page_type_composer_control_type',
        'page_type_publish_target_type',
        'single_page',
        'storage_location_type',
        'theme',
        'user_point_action',
        'workflow_progress_category',
        'workflow_type',
        'workflow',
    ],

    'importer_routines' => [
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSiteTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGroupsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSinglePageStructureRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportStacksStructureRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportBlockTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportBlockTypeSetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportConversationEditorsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportConversationRatingTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportConversationFlagTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypePublishTargetTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypeComposerControlTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportBannedWordsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSocialLinksRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportTreesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportFileImportantThumbnailTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportFeaturesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportFeatureCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGatheringDataSourcesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGatheringItemTemplateTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGatheringItemTemplatesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributeCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributeTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportWorkflowTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportWorkflowProgressCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportWorkflowsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributeSetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressEntitiesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressAssociationsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressFormsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressRelationsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportThemesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPermissionKeyCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPermissionAccessEntityTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPermissionsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportJobsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportJobSetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTemplatesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypesBaseRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageStructureRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageFeedsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypeTargetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypeDefaultsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSinglePageContentRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportStacksContentRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageContentRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPackagesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportConfigValuesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSystemCaptchaLibrariesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSystemContentEditorSnippetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGeolocatorsRoutine',
    ],

    /*
     * Core Routes
     */
    'routes' => [
        /*
         * Dialogs
         */
        '/ccm/system/dialogs/area/design/' => ['\Concrete\Controller\Dialog\Area\Design::view'],
        '/ccm/system/dialogs/area/design/reset' => ['\Concrete\Controller\Dialog\Area\Design::reset'],
        '/ccm/system/dialogs/area/design/submit' => ['\Concrete\Controller\Dialog\Area\Design::submit'],
        '/ccm/system/dialogs/area/layout/presets/manage/' => ['\Concrete\Controller\Dialog\Area\Layout\Presets\Manage::viewPresets'],
        '/ccm/system/dialogs/area/layout/presets/manage/delete' => ['\Concrete\Controller\Dialog\Area\Layout\Presets\Manage::delete'],
        '/ccm/system/dialogs/area/layout/presets/{arLayoutID}' => ['\Concrete\Controller\Dialog\Area\Layout\Presets::view'],
        '/ccm/system/dialogs/area/layout/presets/{arLayoutID}/submit' => ['\Concrete\Controller\Dialog\Area\Layout\Presets::submit'],
        '/ccm/system/dialogs/area/layout/presets/get/{cID}/{arLayoutPresetID}' => ['\Concrete\Controller\Dialog\Area\Layout\Presets::getPresetData'],

        '/ccm/system/dialogs/block/aliasing/' => ['\Concrete\Controller\Dialog\Block\Aliasing::view'],
        '/ccm/system/dialogs/block/aliasing/submit' => ['\Concrete\Controller\Dialog\Block\Aliasing::submit'],
        '/ccm/system/dialogs/block/edit/' => ['\Concrete\Controller\Dialog\Block\Edit::view'],
        '/ccm/system/dialogs/block/edit/submit/' => ['\Concrete\Controller\Dialog\Block\Edit::submit'],
        '/ccm/system/dialogs/block/cache/' => ['\Concrete\Controller\Dialog\Block\Cache::view'],
        '/ccm/system/dialogs/block/cache/submit' => ['\Concrete\Controller\Dialog\Block\Cache::submit'],
        '/ccm/system/dialogs/block/design/' => ['\Concrete\Controller\Dialog\Block\Design::view'],
        '/ccm/system/dialogs/block/design/reset' => ['\Concrete\Controller\Dialog\Block\Design::reset'],
        '/ccm/system/dialogs/block/design/submit' => ['\Concrete\Controller\Dialog\Block\Design::submit'],
        '/ccm/system/dialogs/block/permissions/detail/' => ['\Concrete\Controller\Dialog\Block\Permissions::viewDetail'],
        '/ccm/system/dialogs/block/permissions/guest_access/' => ['\Concrete\Controller\Dialog\Block\Permissions\GuestAccess::__construct'],
        '/ccm/system/dialogs/block/permissions/list/' => ['\Concrete\Controller\Dialog\Block\Permissions::viewList'],
        '/ccm/system/dialogs/block/delete/' => ['\Concrete\Controller\Dialog\Block\Delete::view'],
        '/ccm/system/dialogs/block/delete/submit/' => ['\Concrete\Controller\Dialog\Block\Delete::submit'],
        '/ccm/system/dialogs/block/delete/submit_all/' => ['\Concrete\Controller\Dialog\Block\Delete::submit_all'],

        '/ccm/system/dialogs/file/upload_complete' => ['\Concrete\Controller\Dialog\File\UploadComplete::view'],
        '/ccm/system/dialogs/file/bulk/delete' => ['\Concrete\Controller\Dialog\File\Bulk\Delete::view'],
        '/ccm/system/dialogs/file/bulk/delete/delete_files' => ['\Concrete\Controller\Dialog\File\Bulk\Delete::deleteFiles'],
        '/ccm/system/dialogs/file/bulk/properties' => ['\Concrete\Controller\Dialog\File\Bulk\Properties::view'],
        '/ccm/system/dialogs/file/bulk/sets' => ['\Concrete\Controller\Dialog\File\Bulk\Sets::view'],
        '/ccm/system/dialogs/file/bulk/sets/submit' => ['\Concrete\Controller\Dialog\File\Bulk\Sets::submit'],
        '/ccm/system/dialogs/file/bulk/folder' => ['\Concrete\Controller\Dialog\File\Bulk\Folder::view'],
        '/ccm/system/dialogs/file/bulk/folder/submit' => ['\Concrete\Controller\Dialog\File\Bulk\Folder::submit'],
        '/ccm/system/dialogs/file/bulk/properties/clear_attribute' => ['\Concrete\Controller\Dialog\File\Bulk\Properties::clearAttribute'],
        '/ccm/system/dialogs/file/bulk/properties/update_attribute' => ['\Concrete\Controller\Dialog\File\Bulk\Properties::updateAttribute'],
        '/ccm/system/dialogs/file/bulk/storage' => ['\Concrete\Controller\Dialog\File\Bulk\Storage::view'],
        '/ccm/system/dialogs/file/bulk/storage/submit' => ['\Concrete\Controller\Dialog\File\Bulk\Storage::submit'],
        '/ccm/system/dialogs/file/sets' => ['\Concrete\Controller\Dialog\File\Sets::view'],
        '/ccm/system/dialogs/file/sets/submit' => ['\Concrete\Controller\Dialog\File\Sets::submit'],
        '/ccm/system/dialogs/file/folder' => ['\Concrete\Controller\Dialog\File\Folder::view'],
        '/ccm/system/dialogs/file/folder/submit' => ['\Concrete\Controller\Dialog\File\Folder::submit'],
        '/ccm/system/dialogs/file/properties' => ['\Concrete\Controller\Dialog\File\Properties::view'],
        '/ccm/system/dialogs/file/advanced_search' => ['\Concrete\Controller\Dialog\File\AdvancedSearch::view'],
        '/ccm/system/dialogs/file/advanced_search/add_field' => ['\Concrete\Controller\Dialog\File\AdvancedSearch::addField'],
        '/ccm/system/dialogs/file/advanced_search/submit' => ['\Concrete\Controller\Dialog\File\AdvancedSearch::submit'],
        '/ccm/system/dialogs/file/advanced_search/save_preset' => ['\Concrete\Controller\Dialog\File\AdvancedSearch::savePreset'],
        '/ccm/system/dialogs/file/advanced_search/preset/edit' => ['\Concrete\Controller\Dialog\File\Preset\Edit::view'],
        '/ccm/system/dialogs/file/advanced_search/preset/edit/edit_search_preset' => ['\Concrete\Controller\Dialog\File\Preset\Edit::edit_search_preset'],
        '/ccm/system/dialogs/file/advanced_search/preset/delete' => ['\Concrete\Controller\Dialog\File\Preset\Delete::view'],
        '/ccm/system/dialogs/file/advanced_search/preset/delete/remove_search_preset' => ['\Concrete\Controller\Dialog\File\Preset\Delete::remove_search_preset'],
        '/ccm/system/dialogs/file/properties/clear_attribute' => ['\Concrete\Controller\Dialog\File\Properties::clear_attribute'],
        '/ccm/system/dialogs/file/properties/save' => ['\Concrete\Controller\Dialog\File\Properties::save'],
        '/ccm/system/dialogs/file/properties/update_attribute' => ['\Concrete\Controller\Dialog\File\Properties::update_attribute'],
        '/ccm/system/dialogs/file/search' => ['\Concrete\Controller\Dialog\File\Search::view'],
        '/ccm/system/dialogs/file/jump_to_folder' => ['\Concrete\Controller\Dialog\File\JumpToFolder::view'],
        '/ccm/system/dialogs/file/thumbnails' => ['\Concrete\Controller\Dialog\File\Thumbnails::view'],
        '/ccm/system/dialogs/file/thumbnails/edit' => ['\Concrete\Controller\Dialog\File\Thumbnails\Edit::view'],
        '/ccm/system/dialogs/file/usage/{fID}' => ['\Concrete\Controller\Dialog\File\Usage::view'],

        '/ccm/system/dialogs/group/search' => ['\Concrete\Controller\Dialog\Group\Search::view'],

        '/ccm/system/dialogs/page/add' => ['\Concrete\Controller\Dialog\Page\Add::view'],
        '/ccm/system/dialogs/page/add_block' => ['\Concrete\Controller\Dialog\Page\AddBlock::view'],
        '/ccm/system/dialogs/page/add_block/submit' => ['\Concrete\Controller\Dialog\Page\AddBlock::submit'],
        '/ccm/system/dialogs/page/add_block_list' => ['\Concrete\Controller\Dialog\Page\AddBlockList::view'],
        '/ccm/system/dialogs/page/add_external' => ['\Concrete\Controller\Dialog\Page\AddExternal::view'],
        '/ccm/system/dialogs/page/add_external/submit' => ['\Concrete\Controller\Dialog\Page\AddExternal::submit'],
        '/ccm/system/dialogs/page/add/compose/{ptID}/{cParentID}' => ['\Concrete\Controller\Dialog\Page\Add\Compose::view'],
        '/ccm/system/dialogs/page/add/compose/submit' => ['\Concrete\Controller\Dialog\Page\Add\Compose::submit'],
        '/ccm/system/dialogs/page/attributes' => ['\Concrete\Controller\Dialog\Page\Attributes::view'],
        '/ccm/system/dialogs/page/bulk/properties' => ['\Concrete\Controller\Dialog\Page\Bulk\Properties::view'],
        '/ccm/system/dialogs/page/bulk/properties/clear_attribute' => ['\Concrete\Controller\Dialog\Page\Bulk\Properties::clearAttribute'],
        '/ccm/system/dialogs/page/bulk/properties/update_attribute' => ['\Concrete\Controller\Dialog\Page\Bulk\Properties::updateAttribute'],
        '/ccm/system/dialogs/page/clipboard' => ['\Concrete\Controller\Dialog\Page\Clipboard::view'],
        '/ccm/system/dialogs/page/delete' => ['\Concrete\Controller\Dialog\Page\Delete::view'],
        '/ccm/system/dialogs/page/delete/submit' => ['\Concrete\Controller\Dialog\Page\Delete::submit'],
        '/ccm/system/dialogs/page/delete_alias' => ['\Concrete\Controller\Dialog\Page\DeleteAlias::view'],
        '/ccm/system/dialogs/page/delete_alias/submit' => ['\Concrete\Controller\Dialog\Page\DeleteAlias::submit'],
        '/ccm/system/dialogs/page/delete_from_sitemap' => ['\Concrete\Controller\Dialog\Page\Delete::viewFromSitemap'],
        '/ccm/system/dialogs/page/design' => ['\Concrete\Controller\Dialog\Page\Design::view'],
        '/ccm/system/dialogs/page/design/submit' => ['\Concrete\Controller\Dialog\Page\Design::submit'],
        '/ccm/system/dialogs/page/design/css' => ['\Concrete\Controller\Dialog\Page\Design\Css::view'],
        '/ccm/system/dialogs/page/design/css/submit' => ['\Concrete\Controller\Dialog\Page\Design\Css::submit'],
        '/ccm/system/dialogs/page/edit_external' => ['\Concrete\Controller\Dialog\Page\EditExternal::view'],
        '/ccm/system/dialogs/page/edit_external/submit' => ['\Concrete\Controller\Dialog\Page\EditExternal::submit'],
        '/ccm/system/dialogs/page/location' => ['\Concrete\Controller\Dialog\Page\Location::view'],
        '/ccm/system/dialogs/page/search' => ['\Concrete\Controller\Dialog\Page\Search::view'],
        '/ccm/system/dialogs/page/seo' => ['\Concrete\Controller\Dialog\Page\Seo::view'],

        '/ccm/system/dialogs/page/advanced_search' => ['\Concrete\Controller\Dialog\Page\AdvancedSearch::view'],
        '/ccm/system/dialogs/page/advanced_search/add_field' => ['\Concrete\Controller\Dialog\Page\AdvancedSearch::addField'],
        '/ccm/system/dialogs/page/advanced_search/submit' => ['\Concrete\Controller\Dialog\Page\AdvancedSearch::submit'],
        '/ccm/system/dialogs/page/advanced_search/save_preset' => ['\Concrete\Controller\Dialog\Page\AdvancedSearch::savePreset'],
        '/ccm/system/dialogs/page/advanced_search/preset/edit' => ['\Concrete\Controller\Dialog\Page\Preset\Edit::view'],
        '/ccm/system/dialogs/page/advanced_search/preset/edit/edit_search_preset' => ['\Concrete\Controller\Dialog\Page\Preset\Edit::edit_search_preset'],
        '/ccm/system/dialogs/page/advanced_search/preset/delete' => ['\Concrete\Controller\Dialog\Page\Preset\Delete::view'],
        '/ccm/system/dialogs/page/advanced_search/preset/delete/remove_search_preset' => ['\Concrete\Controller\Dialog\Page\Preset\Delete::remove_search_preset'],

        '/ccm/system/dialogs/user/bulk/properties' => ['\Concrete\Controller\Dialog\User\Bulk\Properties::view'],
        '/ccm/system/dialogs/user/bulk/properties/clear_attribute' => ['\Concrete\Controller\Dialog\User\Bulk\Properties::clearAttribute'],
        '/ccm/system/dialogs/user/bulk/properties/update_attribute' => ['\Concrete\Controller\Dialog\User\Bulk\Properties::updateAttribute'],
		'/ccm/system/dialogs/user/bulk/groupadd' => ['\Concrete\Controller\Dialog\User\Bulk\Group::groupadd'],
		'/ccm/system/dialogs/user/bulk/groupadd/submit' => ['\Concrete\Controller\Dialog\User\Bulk\Group::groupaddsubmit'],
		'/ccm/system/dialogs/user/bulk/groupremove' => ['\Concrete\Controller\Dialog\User\Bulk\Group::groupremove'],
		'/ccm/system/dialogs/user/bulk/groupremove/submit' => ['\Concrete\Controller\Dialog\User\Bulk\Group::groupremovesubmit'],
		'/ccm/system/dialogs/user/bulk/delete' => ['\Concrete\Controller\Dialog\User\Bulk\Delete::view'],
		'/ccm/system/dialogs/user/bulk/delete/submit' => ['\Concrete\Controller\Dialog\User\Bulk\Delete::submit'],
		'/ccm/system/dialogs/user/bulk/activate' => ['\Concrete\Controller\Dialog\User\Bulk\Activate::activate'],
		'/ccm/system/dialogs/user/bulk/deactivate' => ['\Concrete\Controller\Dialog\User\Bulk\Activate::deactivate'],
		'/ccm/system/dialogs/user/bulk/activate/submit' => ['\Concrete\Controller\Dialog\User\Bulk\Activate::activatesubmit'],
		'/ccm/system/dialogs/user/bulk/deactivate/submit' => ['\Concrete\Controller\Dialog\User\Bulk\Activate::deactivatesubmit'],

        '/ccm/system/dialogs/user/search' => ['\Concrete\Controller\Dialog\User\Search::view'],

        '/ccm/system/dialogs/user/advanced_search' => ['\Concrete\Controller\Dialog\User\AdvancedSearch::view'],
        '/ccm/system/dialogs/user/advanced_search/add_field' => ['\Concrete\Controller\Dialog\User\AdvancedSearch::addField'],
        '/ccm/system/dialogs/user/advanced_search/submit' => ['\Concrete\Controller\Dialog\User\AdvancedSearch::submit'],
        '/ccm/system/dialogs/user/advanced_search/save_preset' => ['\Concrete\Controller\Dialog\User\AdvancedSearch::savePreset'],
        '/ccm/system/dialogs/user/advanced_search/preset/edit' => ['\Concrete\Controller\Dialog\User\Preset\Edit::view'],
        '/ccm/system/dialogs/user/advanced_search/preset/edit/edit_search_preset' => ['\Concrete\Controller\Dialog\User\Preset\Edit::edit_search_preset'],
        '/ccm/system/dialogs/user/advanced_search/preset/delete' => ['\Concrete\Controller\Dialog\User\Preset\Delete::view'],
        '/ccm/system/dialogs/user/advanced_search/preset/delete/remove_search_preset' => ['\Concrete\Controller\Dialog\User\Preset\Delete::remove_search_preset'],

        '/ccm/system/dialogs/type/update_from_type/{ptID}/{pTemplateID}' => ['\Concrete\Controller\Dialog\Type\UpdateFromType::view'],
        '/ccm/system/dialogs/type/update_from_type/{ptID}/{pTemplateID}/submit' => ['\Concrete\Controller\Dialog\Type\UpdateFromType::submit'],

        '/ccm/system/dialogs/express/advanced_search/' => ['\Concrete\Controller\Dialog\Express\AdvancedSearch::view'],
        '/ccm/system/dialogs/express/advanced_search/add_field/' => ['\Concrete\Controller\Dialog\Express\AdvancedSearch::addField'],
        '/ccm/system/dialogs/express/advanced_search/submit' => ['\Concrete\Controller\Dialog\Express\AdvancedSearch::submit'],
        '/ccm/system/dialogs/express/advanced_search/save_preset' => ['\Concrete\Controller\Dialog\Express\AdvancedSearch::savePreset'],
        '/ccm/system/dialogs/express/advanced_search/preset/edit' => ['\Concrete\Controller\Dialog\Express\Preset\Edit::view'],
        '/ccm/system/dialogs/express/advanced_search/preset/edit/edit_search_preset' => ['\Concrete\Controller\Dialog\Express\Preset\Edit::edit_search_preset'],
        '/ccm/system/dialogs/express/advanced_search/preset/delete' => ['\Concrete\Controller\Dialog\Express\Preset\Delete::view'],
        '/ccm/system/dialogs/express/advanced_search/preset/delete/remove_search_preset' => ['\Concrete\Controller\Dialog\Express\Preset\Delete::remove_search_preset'],

        '/ccm/system/dialogs/editor/settings/preview' => ['\Concrete\Controller\Dialog\Editor\Settings\Preview::view'],

        /*
         * Conversations
         */
        '/ccm/system/dialogs/conversation/subscribe/{cnvID}' => ['\Concrete\Controller\Dialog\Conversation\Subscribe::view'],
        '/ccm/system/dialogs/conversation/subscribe/subscribe/{cnvID}' => ['\Concrete\Controller\Dialog\Conversation\Subscribe::subscribe'],
        '/ccm/system/dialogs/conversation/subscribe/unsubscribe/{cnvID}' => ['\Concrete\Controller\Dialog\Conversation\Subscribe::unsubscribe'],

        /*
         * Help
         */
        '/ccm/system/dialogs/help/introduction/' => ['\Concrete\Controller\Dialog\Help\Introduction::view'],

        /*
         * Files
         */
        '/ccm/system/file/approve_version' => ['\Concrete\Controller\Backend\File::approveVersion'],
        '/ccm/system/file/delete_version' => ['\Concrete\Controller\Backend\File::deleteVersion'],
        '/ccm/system/file/duplicate' => ['\Concrete\Controller\Backend\File::duplicate'],
        '/ccm/system/file/get_json' => ['\Concrete\Controller\Backend\File::getJSON'],
        '/ccm/system/file/rescan' => ['\Concrete\Controller\Backend\File::rescan'],
        '/ccm/system/file/rescan_multiple' => ['\Concrete\Controller\Backend\File::rescanMultiple'],
        '/ccm/system/file/star' => ['\Concrete\Controller\Backend\File::star'],
        '/ccm/system/file/upload' => ['\Concrete\Controller\Backend\File::upload'],
        '/ccm/system/file/folder/add' => ['\Concrete\Controller\Backend\File\Folder::add'],
        '/ccm/system/file/folder/contents' => ['\Concrete\Controller\Search\FileFolder::submit'],
        '/ccm/system/file/thumbnailer' => ['\Concrete\Controller\Backend\File\Thumbnailer::generate'],

        /*
         * Users
         */
        '/ccm/system/user/add_group' => ['\Concrete\Controller\Backend\User::addGroup'],
        '/ccm/system/user/remove_group' => ['\Concrete\Controller\Backend\User::removeGroup'],
        '/ccm/system/user/get_json' => ['\Concrete\Controller\Backend\User::getJSON'],

        /*
         * Page actions - non UI
         */
        '/ccm/system/page/arrange_blocks/' => ['\Concrete\Controller\Backend\Page\ArrangeBlocks::arrange'],
        '/ccm/system/page/check_in/{cID}/{token}' => ['\Concrete\Controller\Backend\Page::exitEditMode'],
        '/ccm/system/page/create/{ptID}' => ['\Concrete\Controller\Backend\Page::create'],
        '/ccm/system/page/create/{ptID}/{parentID}' => ['\Concrete\Controller\Backend\Page::create'],
        '/ccm/system/page/get_json' => ['\Concrete\Controller\Backend\Page::getJSON'],
        '/ccm/system/page/multilingual/assign' => ['\Concrete\Controller\Backend\Page\Multilingual::assign'],
        '/ccm/system/page/multilingual/create_new' => ['\Concrete\Controller\Backend\Page\Multilingual::create_new'],
        '/ccm/system/page/multilingual/ignore' => ['\Concrete\Controller\Backend\Page\Multilingual::ignore'],
        '/ccm/system/page/multilingual/unmap' => ['\Concrete\Controller\Backend\Page\Multilingual::unmap'],
        '/ccm/system/page/select_sitemap' => ['\Concrete\Controller\Backend\Page\SitemapSelector::view'],
        '/ccm/system/page/sitemap_data' => ['\Concrete\Controller\Backend\Page\SitemapData::view'],

        /*
         * Block actions - non UI
         */
        '/ccm/system/block/render/' => ['\Concrete\Controller\Backend\Block::render'],
        '/ccm/system/block/action/add/{cID}/{arHandle}/{btID}/{action}' => ['\Concrete\Controller\Backend\Block\Action::add'],
        '/ccm/system/block/action/edit/{cID}/{arHandle}/{bID}/{action}' => ['\Concrete\Controller\Backend\Block\Action::edit'],
        '/ccm/system/block/action/add_composer/{ptComposerFormLayoutSetControlID}/{action}' => ['\Concrete\Controller\Backend\Block\Action::add_composer'],
        '/ccm/system/block/action/edit_composer/{cID}/{arHandle}/{ptComposerFormLayoutSetControlID}/{action}' => ['\Concrete\Controller\Backend\Block\Action::edit_composer'],

        /*
         * Misc
         */
        '/ccm/system/css/layout/{arLayoutID}' => ['\Concrete\Controller\Frontend\Stylesheet::layout'],
        '/ccm/system/css/page/{cID}/{stylesheet}/{cvID}' => ['\Concrete\Controller\Frontend\Stylesheet::page_version'],
        '/ccm/system/css/page/{cID}/{stylesheet}' => ['\Concrete\Controller\Frontend\Stylesheet::page'],
        '/ccm/system/backend/editor_data/' => ['\Concrete\Controller\Backend\EditorData::view'],
        '/ccm/system/backend/get_remote_help/' => ['\Concrete\Controller\Backend\GetRemoteHelp::view'],
        '/ccm/system/backend/intelligent_search/' => ['\Concrete\Controller\Backend\IntelligentSearch::view'],
        '/ccm/system/jobs' => ['\Concrete\Controller\Frontend\Jobs::view'],
        '/ccm/system/jobs/run_single' => ['\Concrete\Controller\Frontend\Jobs::run_single'],
        '/ccm/system/jobs/check_queue' => ['\Concrete\Controller\Frontend\Jobs::check_queue'],
        // @TODO remove the line below
        '/tools/required/jobs' => ['\Concrete\Controller\Frontend\Jobs::view'],
        '/tools/required/jobs/check_queue' => ['\Concrete\Controller\Frontend\Jobs::check_queue'],
        '/tools/required/jobs/run_single' => ['\Concrete\Controller\Frontend\Jobs::run_single'],
        // end removing lines
        '/ccm/system/upgrade/' => ['\Concrete\Controller\Upgrade::view'],
        '/ccm/system/upgrade/submit' => ['\Concrete\Controller\Upgrade::submit'],
        '/ccm/system/country-stateprovince-link/get_stateprovinces' => ['\Concrete\Controller\Frontend\CountryStateprovinceLink::getStateprovinces'],

        /*
         * Notification
         */
        '/ccm/system/notification/alert/archive/' => ['\Concrete\Controller\Backend\Notification\Alert::archive'],

        /*
         * General Attribute
         */
        '/ccm/system/attribute/action/{action}' => [
            '\Concrete\Controller\Backend\Attribute\Action::dispatch',
            'attribute_action',
            ['action' => '.+'],
        ],
        '/ccm/system/attribute/attribute_sort/set' => ['\Concrete\Controller\Backend\Attributes::sortInSet'],
        '/ccm/system/attribute/attribute_sort/user' => ['\Concrete\Controller\Backend\Attributes::sortUser'],

        /*
         * Trees
         */
        '/ccm/system/tree/load' => ['\Concrete\Controller\Backend\Tree::load'],
        '/ccm/system/tree/node/load' => ['\Concrete\Controller\Backend\Tree\Node::load'],
        '/ccm/system/tree/node/load_starting' => ['\Concrete\Controller\Backend\Tree\Node::load_starting'],
        '/ccm/system/tree/node/drag_request' => ['\Concrete\Controller\Backend\Tree\Node\DragRequest::execute'],
        '/ccm/system/tree/node/duplicate' => ['\Concrete\Controller\Backend\Tree\Node\Duplicate::execute'],
        '/ccm/system/tree/node/update_order' => ['\Concrete\Controller\Backend\Tree\Node\DragRequest::updateChildren'],

        '/ccm/system/dialogs/tree/node/add/category' => ['\Concrete\Controller\Dialog\Tree\Node\Category\Add::view'],
        '/ccm/system/dialogs/tree/node/add/category/add_category_node' => ['\Concrete\Controller\Dialog\Tree\Node\Category\Add::add_category_node'],

        '/ccm/system/dialogs/tree/node/add/topic' => ['\Concrete\Controller\Dialog\Tree\Node\Topic\Add::view'],
        '/ccm/system/dialogs/tree/node/add/topic/add_topic_node' => ['\Concrete\Controller\Dialog\Tree\Node\Topic\Add::add_topic_node'],

        '/ccm/system/dialogs/tree/node/edit/topic' => ['\Concrete\Controller\Dialog\Tree\Node\Topic\Edit::view'],
        '/ccm/system/dialogs/tree/node/edit/topic/update_topic_node' => ['\Concrete\Controller\Dialog\Tree\Node\Topic\Edit::update_topic_node'],

        '/ccm/system/dialogs/tree/node/edit/category' => ['\Concrete\Controller\Dialog\Tree\Node\Category\Edit::view'],
        '/ccm/system/dialogs/tree/node/edit/category/update_category_node' => ['\Concrete\Controller\Dialog\Tree\Node\Category\Edit::update_category_node'],

        '/ccm/system/dialogs/tree/node/delete' => ['\Concrete\Controller\Dialog\Tree\Node\Delete::view'],
        '/ccm/system/dialogs/tree/node/delete/remove_tree_node' => ['\Concrete\Controller\Dialog\Tree\Node\Delete::remove_tree_node'],
        '/ccm/system/dialogs/tree/node/permissions' => ['\Concrete\Controller\Dialog\Tree\Node\Permissions::view'],
        '/ccm/system/dialogs/tree/node/category/delete_express' => ['\Concrete\Controller\Dialog\Tree\Node\Category\DeleteExpress::view'],
        '/ccm/system/dialogs/tree/node/category/delete_express/remove_tree_node' => ['\Concrete\Controller\Dialog\Tree\Node\Category\DeleteExpress::remove_tree_node'],

        /*
         * Marketplace
         */
        '/ccm/system/dialogs/marketplace/checkout' => ['\Concrete\Controller\Dialog\Marketplace\Checkout::view'],
        '/ccm/system/dialogs/marketplace/download' => ['\Concrete\Controller\Dialog\Marketplace\Download::view'],
        '/ccm/system/marketplace/connect' => ['\Concrete\Controller\Backend\Marketplace\Connect::view'],
        '/ccm/system/marketplace/search' => ['\Concrete\Controller\Backend\Marketplace\Search::view'],

        /*
         * Express
         */
        '/ccm/system/dialogs/express/entry/search' => ['\Concrete\Controller\Dialog\Express\Search::entries'],
        '/ccm/system/search/express/entries/submit/{entityID}' => ['\Concrete\Controller\Search\Express\Entries::submit'],
        '/ccm/system/express/entry/get_json' => ['\Concrete\Controller\Backend\Express\Entry::getJSON'],

        /*
         * Search Routes
         */
        '/ccm/system/search/files/basic' => ['\Concrete\Controller\Search\Files::searchBasic'],
        '/ccm/system/search/files/current' => ['\Concrete\Controller\Search\Files::searchCurrent'],
        '/ccm/system/search/files/preset/{presetID}' => ['\Concrete\Controller\Search\Files::searchPreset'],
        '/ccm/system/search/files/clear' => ['\Concrete\Controller\Search\Files::clearSearch'],

        '/ccm/system/search/pages/basic' => ['\Concrete\Controller\Search\Pages::searchBasic'],
        '/ccm/system/search/pages/current' => ['\Concrete\Controller\Search\Pages::searchCurrent'],
        '/ccm/system/search/pages/preset/{presetID}' => ['\Concrete\Controller\Search\Pages::searchPreset'],
        '/ccm/system/search/pages/clear' => ['\Concrete\Controller\Search\Pages::clearSearch'],

        '/ccm/system/search/users/basic' => ['\Concrete\Controller\Search\Users::searchBasic'],
        '/ccm/system/search/users/current' => ['\Concrete\Controller\Search\Users::searchCurrent'],
        '/ccm/system/search/users/preset/{presetID}' => ['\Concrete\Controller\Search\Users::searchPreset'],
        '/ccm/system/search/users/clear' => ['\Concrete\Controller\Search\Users::clearSearch'],

        '/ccm/system/search/express/basic' => ['\Concrete\Controller\Search\Express::searchBasic'],
        '/ccm/system/search/express/current' => ['\Concrete\Controller\Search\Express::searchCurrent'],
        '/ccm/system/search/express/preset/{entityID}/{presetID}' => ['\Concrete\Controller\Search\Express::expressSearchPreset'],
        '/ccm/system/search/express/clear' => ['\Concrete\Controller\Search\Express::clearSearch'],

        '/ccm/system/search/groups/submit' => ['\Concrete\Controller\Search\Groups::submit'],

        /*
         * Panels - top level
         */
        '/ccm/system/panels/add' => ['\Concrete\Controller\Panel\Add::view'],
        '/ccm/system/panels/dashboard' => ['\Concrete\Controller\Panel\Dashboard::view'],
        '/ccm/system/panels/dashboard/add_favorite' => ['\Concrete\Controller\Panel\Dashboard::addFavorite'],
        '/ccm/system/panels/dashboard/remove_favorite' => ['\Concrete\Controller\Panel\Dashboard::removeFavorite'],
        '/ccm/system/panels/page/relations' => ['\Concrete\Controller\Panel\PageRelations::view'],
        '/ccm/system/panels/page' => ['\Concrete\Controller\Panel\Page::view'],
        '/ccm/system/panels/page/attributes' => ['\Concrete\Controller\Panel\Page\Attributes::view'],
        '/ccm/system/panels/page/check_in' => ['\Concrete\Controller\Panel\Page\CheckIn::__construct'],
        '/ccm/system/panels/page/check_in/submit' => ['\Concrete\Controller\Panel\Page\CheckIn::submit'],
        '/ccm/system/panels/page/design' => ['\Concrete\Controller\Panel\Page\Design::view'],
        '/ccm/system/panels/page/design/customize/reset_page_customizations' => ['\Concrete\Controller\Panel\Page\Design\Customize::reset_page_customizations'],
        '/ccm/system/panels/page/design/customize/apply_to_page/{pThemeID}' => ['\Concrete\Controller\Panel\Page\Design\Customize::apply_to_page'],
        '/ccm/system/panels/page/design/customize/apply_to_site/{pThemeID}' => ['\Concrete\Controller\Panel\Page\Design\Customize::apply_to_site'],
        '/ccm/system/panels/page/design/customize/preview/{pThemeID}' => ['\Concrete\Controller\Panel\Page\Design\Customize::preview'],
        '/ccm/system/panels/page/design/customize/reset_site_customizations/{pThemeID}' => ['\Concrete\Controller\Panel\Page\Design\Customize::reset_site_customizations'],
        '/ccm/system/panels/page/design/customize/{pThemeID}' => ['\Concrete\Controller\Panel\Page\Design\Customize::view'],
        '/ccm/system/panels/page/design/preview_contents' => ['\Concrete\Controller\Panel\Page\Design::preview_contents'],
        '/ccm/system/panels/page/design/submit' => ['\Concrete\Controller\Panel\Page\Design::submit'],
        '/ccm/system/panels/page/preview_as_user' => ['\Concrete\Controller\Panel\Page\PreviewAsUser::view'],
        '/ccm/system/panels/page/preview_as_user/preview' => ['\Concrete\Controller\Panel\Page\PreviewAsUser::frame_page'],
        '/ccm/system/panels/page/preview_as_user/render' => ['\Concrete\Controller\Panel\Page\PreviewAsUser::preview_page'],
        '/ccm/system/panels/page/versions' => ['\Concrete\Controller\Panel\Page\Versions::view'],
        '/ccm/system/panels/page/versions/get_json' => ['\Concrete\Controller\Panel\Page\Versions::get_json'],
        '/ccm/system/panels/page/versions/duplicate' => ['\Concrete\Controller\Panel\Page\Versions::duplicate'],
        '/ccm/system/panels/page/versions/new_page' => ['\Concrete\Controller\Panel\Page\Versions::new_page'],
        '/ccm/system/panels/page/versions/delete' => ['\Concrete\Controller\Panel\Page\Versions::delete'],
        '/ccm/system/panels/page/versions/approve' => ['\Concrete\Controller\Panel\Page\Versions::approve'],
		'/ccm/system/panels/page/versions/unapprove' => ['\Concrete\Controller\Panel\Page\Versions::unapprove'],
        '/ccm/system/panels/page/devices' => ['\Concrete\Controller\Panel\Page\Devices::view'],
        '/ccm/system/panels/page/devices/preview' => ['\Concrete\Controller\Panel\Page\Devices::preview'],
        '/ccm/system/panels/sitemap' => ['\Concrete\Controller\Panel\Sitemap::view'],

        /*
         * Panel Details
         */
        '/ccm/system/panels/details/page/attributes' => ['\Concrete\Controller\Panel\Detail\Page\Attributes::view'],
        '/ccm/system/panels/details/page/attributes/add_attribute' => ['\Concrete\Controller\Panel\Detail\Page\Attributes::add_attribute'],
        '/ccm/system/panels/details/page/attributes/submit' => ['\Concrete\Controller\Panel\Detail\Page\Attributes::submit'],
        '/ccm/system/panels/details/page/caching' => ['\Concrete\Controller\Panel\Detail\Page\Caching::view'],
        '/ccm/system/panels/details/page/caching/purge' => ['\Concrete\Controller\Panel\Detail\Page\Caching::purge'],
        '/ccm/system/panels/details/page/caching/submit' => ['\Concrete\Controller\Panel\Detail\Page\Caching::submit'],
        '/ccm/system/panels/details/page/composer' => ['\Concrete\Controller\Panel\Detail\Page\Composer::view'],
        '/ccm/system/panels/details/page/composer/autosave' => ['\Concrete\Controller\Panel\Detail\Page\Composer::autosave'],
        '/ccm/system/panels/details/page/composer/discard' => ['\Concrete\Controller\Panel\Detail\Page\Composer::discard'],
        '/ccm/system/panels/details/page/composer/publish' => ['\Concrete\Controller\Panel\Detail\Page\Composer::publish'],
        '/ccm/system/panels/details/page/composer/save_and_exit' => ['\Concrete\Controller\Panel\Detail\Page\Composer::saveAndExit'],
        '/ccm/system/panels/details/page/location' => ['\Concrete\Controller\Panel\Detail\Page\Location::view'],
        '/ccm/system/panels/details/page/location/submit' => ['\Concrete\Controller\Panel\Detail\Page\Location::submit'],
        '/ccm/system/panels/details/page/permissions' => ['\Concrete\Controller\Panel\Detail\Page\Permissions::view'],
        '/ccm/system/panels/details/page/permissions/save_simple' => ['\Concrete\Controller\Panel\Detail\Page\Permissions::save_simple'],
        '/ccm/system/panels/details/page/preview' => ['\Concrete\Controller\Panel\Page\Design::preview'],
        '/ccm/system/panels/details/page/seo' => ['\Concrete\Controller\Panel\Detail\Page\Seo::view'],
        '/ccm/system/panels/details/page/seo/submit' => ['\Concrete\Controller\Panel\Detail\Page\Seo::submit'],
        '/ccm/system/panels/details/page/versions' => ['\Concrete\Controller\Panel\Detail\Page\Versions::view'],
        '/ccm/system/panels/details/page/devices' => ['\Concrete\Controller\Panel\Page\Devices::detail'],

        /*
         * RSS Feeds
         */
        '/rss/{identifier}' => [
            '\Concrete\Controller\Feed::output',
            'rss',
            ['identifier' => '[A-Za-z0-9_/.]+'],
        ],

        /*
         * Special Dashboard
         */
        '/dashboard/blocks/stacks/list' => ['\Concrete\Controller\SinglePage\Dashboard\Blocks\Stacks::list_page'],

        /*
         * Assets localization
         */
        '/ccm/assets/localization/core/js' => ['\Concrete\Controller\Frontend\AssetsLocalization::getCoreJavascript'],
        '/ccm/assets/localization/select2/js' => ['\Concrete\Controller\Frontend\AssetsLocalization::getSelect2Javascript'],
        '/ccm/assets/localization/redactor/js' => ['\Concrete\Controller\Frontend\AssetsLocalization::getRedactorJavascript'],
        '/ccm/assets/localization/fancytree/js' => ['\Concrete\Controller\Frontend\AssetsLocalization::getFancytreeJavascript'],
        '/ccm/assets/localization/imageeditor/js' => ['\Concrete\Controller\Frontend\AssetsLocalization::getImageEditorJavascript'],
        '/ccm/assets/localization/jquery/ui/js' => ['\Concrete\Controller\Frontend\AssetsLocalization::getJQueryUIJavascript'],
        '/ccm/assets/localization/translator/js' => ['\Concrete\Controller\Frontend\AssetsLocalization::getTranslatorJavascript'],
        '/ccm/assets/localization/dropzone/js' => ['\Concrete\Controller\Frontend\AssetsLocalization::getDropzoneJavascript'],
        '/ccm/assets/localization/conversations/js' => ['\Concrete\Controller\Frontend\AssetsLocalization::getConversationsJavascript'],

        /*
         * Languages
         */
        '/ccm/system/dialogs/language/update/details' => ['\Concrete\Controller\Dialog\Language\Update\Details::view'],

		/*
		 * Privacy Policy
		 */
		'/ccm/system/accept_privacy_policy' => ['\Concrete\Controller\Backend\PrivacyPolicy::acceptPrivacyPolicy'],

		/*
         * Captcha images
         */
        '/ccm/system/captcha/picture' => ['\Concrete\Core\Captcha\CaptchaWithPictureInterface::displayCaptchaPicture'],


        /*
         * Calendar
         */
        '/ccm/calendar/dialogs/event/edit' => ['\Concrete\Controller\Dialog\Event\Edit::edit'],
        '/ccm/calendar/dialogs/event/add' => ['\Concrete\Controller\Dialog\Event\Edit::add'],
        '/ccm/calendar/dialogs/event/add/save' => ['\Concrete\Controller\Dialog\Event\Edit::addEvent'],
        '/ccm/calendar/dialogs/event/edit/save' => ['\Concrete\Controller\Dialog\Event\Edit::updateEvent'],
        '/ccm/calendar/dialogs/event/duplicate' => ['\Concrete\Controller\Dialog\Event\Duplicate::view'],
        '/ccm/calendar/dialogs/event/duplicate/submit' => ['\Concrete\Controller\Dialog\Event\Duplicate::submit'],
        '/ccm/calendar/dialogs/event/delete' => ['\Concrete\Controller\Dialog\Event\Delete::view'],
        '/ccm/calendar/dialogs/event/delete_occurrence' => ['\Concrete\Controller\Dialog\Event\DeleteOccurrence::view'],
        '/ccm/calendar/dialogs/event/delete/submit' => ['\Concrete\Controller\Dialog\Event\Delete::submit'],
        '/ccm/calendar/dialogs/event/delete_occurrence/submit' => ['\Concrete\Controller\Dialog\Event\DeleteOccurrence::submit'],
        '/ccm/calendar/dialogs/event/versions' => ['\Concrete\Controller\Dialog\Event\Versions::view'],
        '/ccm/calendar/dialogs/event/version/view' => ['\Concrete\Controller\Dialog\Event\ViewVersion::view'],
        '/ccm/calendar/event/version/delete' => ['\Concrete\Controller\Event\EventVersion::delete'],
        '/ccm/calendar/event/version/approve' => ['\Concrete\Controller\Event\EventVersion::approve'],
        '/ccm/calendar/event/version/unapprove_all' => ['\Concrete\Controller\Event\Event::unapprove'],
        '/ccm/calendar/view_event/{bID}/{occurrence_id}' => [
            '\Concrete\Controller\Dialog\Frontend\Event::view',
            'view_event_occurrence',
            ['occurrence_id' => '[0-9]+'],
        ],
        '/ccm/calendar/feed/{calendar_id}' => [
            '\Concrete\Controller\CalendarFeed::view',
            'calendar_rss',
            ['identifier' => '[0-9]+'],
        ],
        '/ccm/calendar/dialogs/event/occurrence' => ['\Concrete\Controller\Dialog\EventOccurrence::view'],
        '/ccm/calendar/dialogs/choose_event' => ['\Concrete\Controller\Dialog\ChooseEvent::view'],
        '/ccm/calendar/dialogs/choose_event/get_events' => ['\Concrete\Controller\Dialog\ChooseEvent::getEvents'],
        '/ccm/calendar/event/get_json' => ['\Concrete\Controller\Event\Event::getJSON'],
        '/ccm/calendar/dialogs/permissions/{pkCategoryHandle}' => ['\Concrete\Controller\Dialog\Calendar\Permissions::view'],

        /* Permissions Tools Hack */
        '/tools/required/permissions/categories/calendar_admin' => ['\Concrete\Controller\Event\Permissions::process'],
        '/tools/required/permissions/categories/calendar' => ['\Concrete\Controller\Event\Permissions::processCalendar'],
    ],

/*
 * Route themes
 */
    'theme_paths' => [
        '/dashboard' => 'dashboard',
        '/dashboard/*' => 'dashboard',
        '/account' => VIEW_CORE_THEME,
        '/account/*' => VIEW_CORE_THEME,
        '/install' => VIEW_CORE_THEME,
        '/login' => [
            VIEW_CORE_THEME,
            VIEW_CORE_THEME_TEMPLATE_BACKGROUND_IMAGE,
        ],
        '/register' => VIEW_CORE_THEME,
        '/frontend/maintenance_mode' => VIEW_CORE_THEME,
        '/upgrade' => VIEW_CORE_THEME,
    ],

    /*
     * File Types
     */
    'file_types' => [
        'JPEG' => ['jpg,jpeg,jpe', FileType::T_IMAGE, 'image', 'image', 'image'],
        'GIF' => ['gif', FileType::T_IMAGE, 'image', 'image', 'image'],
        'PNG' => ['png', FileType::T_IMAGE, 'image', 'image', 'image'],
        'Windows Bitmap' => ['bmp', FileType::T_IMAGE, 'image'],
        'TIFF' => ['tif,tiff', FileType::T_IMAGE, 'image'],
        'HTML' => ['htm,html', FileType::T_IMAGE],
        'Flash' => ['swf', FileType::T_IMAGE, 'image'],
        'Icon' => ['ico', FileType::T_IMAGE],
        'SVG' => ['svg', FileType::T_IMAGE],
        'Windows Video' => ['asf,wmv', FileType::T_VIDEO, false, 'video'],
        'Quicktime' => ['mov,qt', FileType::T_VIDEO, false, 'video'],
        'AVI' => ['avi', FileType::T_VIDEO, false, 'video'],
        '3GP' => ['3gp', FileType::T_VIDEO, false, 'video'],
        'Plain Text' => ['txt', FileType::T_TEXT, false, 'text'],
        'CSV' => ['csv', FileType::T_TEXT, false, 'text'],
        'XML' => ['xml', FileType::T_TEXT],
        'PHP' => ['php', FileType::T_TEXT],
        'MS Word' => ['doc,docx', FileType::T_DOCUMENT],
        'Stylesheet' => ['css', FileType::T_TEXT],
        'MP4' => ['mp4', FileType::T_VIDEO, false, 'video'],
        'FLV' => ['flv', FileType::T_VIDEO, 'flv'],
        'MP3' => ['mp3', FileType::T_AUDIO, false, 'audio'],
        'MP4 Audio' => ['m4a', FileType::T_AUDIO, false, 'audio'],
        'Realaudio' => ['ra,ram', FileType::T_AUDIO],
        'Windows Audio' => ['wma', FileType::T_AUDIO],
        'Rich Text' => ['rtf', FileType::T_DOCUMENT],
        'JavaScript' => ['js', FileType::T_TEXT],
        'PDF' => ['pdf', FileType::T_DOCUMENT],
        'Photoshop' => ['psd', FileType::T_IMAGE],
        'MPEG' => ['mpeg,mpg', FileType::T_VIDEO],
        'MS Excel' => ['xla,xls,xlsx,xlt,xlw', FileType::T_DOCUMENT],
        'MS Powerpoint' => ['pps,ppt,pptx,pot', FileType::T_DOCUMENT],
        'TAR Archive' => ['tar', FileType::T_APPLICATION],
        'Zip Archive' => ['zip', FileType::T_APPLICATION],
        'GZip Archive' => ['gz,gzip', FileType::T_APPLICATION],
        'OGG' => ['ogg', FileType::T_AUDIO, false, 'audio'],
        'OGG Video' => ['ogv', FileType::T_VIDEO, false, 'video'],
        'WebM' => ['webm', FileType::T_VIDEO, false, 'video'],
    ],

    /*
     * Importer Attributes
     */
    'importer_attributes' => [
        'width' => ['Width', 'NUMBER', false],
        'height' => ['Height', 'NUMBER', false],
        'duration' => ['Duration', 'NUMBER', false],
    ],

    /*
     * Assets
     */
    'assets' => [
        'google-charts' => [
            [
                'javascript',
                'https://www.gstatic.com/charts/loader.js',
                ['local' => false],
            ],
        ],

        'jquery' => [
            [
                'javascript',
                'js/jquery.js',
                ['position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false],
            ],
        ],
        'jquery/ui' => [
            ['javascript', 'js/jquery-ui.js', ['minify' => false, 'combine' => false]],
            ['javascript-localized', '/ccm/assets/localization/jquery/ui/js'],
            ['css', 'css/jquery-ui.css', ['minify' => false]],
        ],
        'jquery/visualize' => [
            ['javascript', 'js/jquery-visualize.js', ['minify' => false, 'combine' => false]],
            ['css', 'css/jquery-visualize.css', ['minify' => false]],
        ],
        'jquery/touch-punch' => [
            ['javascript', 'js/jquery-ui-touch-punch.js'],
        ],
        'jquery/tristate' => [
            ['javascript', 'js/jquery-tristate.js'],
        ],
        'select2' => [
            ['javascript', 'js/select2.js', ['minify' => false, 'combine' => false]],
            ['javascript-localized', '/ccm/assets/localization/select2/js'],
            ['css', 'css/select2.css', ['minify' => false]],
        ],
        'selectize' => [
            ['javascript', 'js/selectize.js', ['minify' => false, 'combine' => false]],
            ['css', 'css/selectize.css', ['minify' => false]],
        ],
        'underscore' => [
            ['javascript', 'js/underscore.js', ['minify' => false]],
        ],
        'backbone' => [
            ['javascript', 'js/backbone.js', ['minify' => false]],
        ],
        'dropzone' => [
            ['javascript', 'js/dropzone.js'],
            ['javascript-localized', '/ccm/assets/localization/dropzone/js'],
            ['css', 'css/dropzone.css', ['minify' => false]],
        ],
        'jquery/form' => [
            ['javascript', 'js/jquery-form.js'],
        ],
        'picturefill' => [
            ['javascript', 'js/picturefill.js', ['minify' => false]],
        ],
        'responsive-slides' => [
            ['javascript', 'js/responsive-slides.js', ['minify' => false]],
            ['css', 'css/responsive-slides.css', ['minify' => false]],
        ],
        'fullcalendar' => [
            ['javascript', 'js/fullcalendar/fullcalendar.js', ['minify' => false, 'combine' => false]],
            ['css', 'js/fullcalendar/fullcalendar.css', ['minify' => false]],
        ],
        'fullcalendar/localization' => [
            ['javascript', 'js/fullcalendar/locale-all.js', ['minify' => false, 'combine' => false]],
        ],
        'fullcalendar/print' => [
            ['css', 'js/fullcalendar/fullcalendar.print.css', ['minify' => false]],
        ],

        'html5-shiv' => [
            [
                'javascript-conditional',
                'js/ie/html5-shiv.js',
                ['conditional' => 'lt IE 9'],
            ],
        ],
        'respond' => [
            [
                'javascript-conditional',
                'js/ie/respond.js',
                ['conditional' => 'lt IE 9'],
            ],
        ],
        'spectrum' => [
            ['javascript', 'js/spectrum.js', ['minify' => false]],
            ['css', 'css/spectrum.css', ['minify' => false]],
        ],
        'core/composer-save-coordinator' => [
            ['javascript', 'js/composer-save-coordinator.js', ['minify' => false]],
        ],
        'font-awesome' => [
            ['css', 'css/font-awesome.css', ['minify' => false]],
        ],
        'core/events' => [
            ['javascript', 'js/events.js', ['minify' => false]],
        ],
        'core/asset-loader' => [
            ['javascript', 'js/asset-loader.js', ['minify' => false]],
        ],
        'core/style-customizer' => [
            ['javascript', 'js/style-customizer.js', ['minify' => false]],
            ['css', 'css/style-customizer.css', ['minify' => false]],
        ],
        'core/localization' => [
            ['javascript-localized', '/ccm/assets/localization/core/js'],
        ],
        'core/frontend/parallax-image' => [
            ['javascript', 'js/frontend/parallax-image.js', ['minify' => false]],
        ],
        'core/frontend/thumbnail-builder' => [
            ['javascript', 'js/frontend/thumbnail-builder.js'],
        ],
        'core/imageeditor/control/position' => [
            ['css', 'css/image-editor/controls/position.css'],
            ['javascript', 'js/image-editor/controls/position.js'],
        ],
        'core/duration' => [
            ['javascript', 'js/duration.js'],
            ['css', 'css/duration.css'],
        ],
        'core/imageeditor/control/filter' => [
            ['css', 'css/image-editor/controls/filter.css'],
            ['javascript', 'js/image-editor/controls/filter.js'],
        ],
        'core/imageeditor/filter/gaussian_blur' => [
            ['css', 'css/image-editor/filters/gaussian_blur.css'],
            ['javascript', 'js/image-editor/filters/gaussian_blur.js'],
        ],
        'core/imageeditor/filter/none' => [
            ['css', 'css/image-editor/filters/none.css'],
            ['javascript', 'js/image-editor/filters/none.js'],
        ],
        'core/imageeditor/filter/sepia' => [
            ['css', 'css/image-editor/filters/sepia.css'],
            ['javascript', 'js/image-editor/filters/sepia.js'],
        ],
        'core/imageeditor/filter/vignette' => [
            ['css', 'css/image-editor/filters/vignette.css'],
            ['javascript', 'js/image-editor/filters/vignette.js'],
        ],
        'core/imageeditor/filter/grayscale' => [
            ['css', 'css/image-editor/filters/grayscale.css'],
            ['javascript', 'js/image-editor/filters/grayscale.js'],
        ],
        'jquery/awesome-rating' => [
            ['javascript', 'js/jquery-awesome-rating.js', ['minify' => false]],
            ['css', 'css/jquery-awesome-rating.css', ['minify' => false]],
        ],
        'jquery/fileupload' => [
            ['javascript', 'js/jquery-fileupload.js'],
        ],
        'jquery/textcounter' => [
            ['javascript', 'js/textcounter.js'],
        ],
        'swfobject' => [
            ['javascript', 'js/swfobject.js'],
        ],
        'redactor' => [
            ['javascript', 'js/redactor.js', ['minify' => false]],
            ['javascript-localized', '/ccm/assets/localization/redactor/js'],
            ['css', 'css/redactor.css'],
        ],
        'ace' => [
            ['javascript', 'js/ace/ace.js', ['minify' => false]],
        ],
        'backstretch' => [
            ['javascript', 'js/backstretch.js'],
        ],
        'background-check' => [
            ['javascript', 'js/background-check.js'],
        ],
        /*
        'dynatree' => array(
            array('javascript', 'js/dynatree.js', array('minify' => false)),
            array('javascript-localized', '/ccm/assets/localization/dynatree/js', array('minify' => false)),
            array('css', 'css/dynatree.css', array('minify' => false)),
        ),
        */
        'fancytree' => [
            ['javascript', 'js/fancytree.js', ['minify' => false, 'version' => '2.18.0']],
            ['javascript-localized', '/ccm/assets/localization/fancytree/js', ['minify' => false]],
            ['css', 'css/fancytree.css', ['minify' => false]],
        ],
        'moment' => [
            ['javascript', 'js/moment.js', ['minify' => false, 'version' => '2.18.1']],
        ],
        'moment-timezone' => [
            ['javascript', 'js/moment-timezone.js', ['minify' => false, 'version' => '0.5.13']],
        ],
        'moment-timezone-data' => [
            ['javascript', 'js/moment-timezone-data.js', ['minify' => false, 'version' => '0.5.13']],
        ],
        'bootstrap/dropdown' => [
            ['javascript', 'js/bootstrap/dropdown.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/tooltip' => [
            ['javascript', 'js/bootstrap/tooltip.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/popover' => [
            ['javascript', 'js/bootstrap/popover.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/collapse' => [
            ['javascript', 'js/bootstrap/collapse.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/alert' => [
            ['javascript', 'js/bootstrap/alert.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/button' => [
            ['javascript', 'js/bootstrap/button.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/transition' => [
            ['javascript', 'js/bootstrap/transition.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap' => [
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'core/app' => [
            ['javascript', 'js/app.js', ['minify' => false, 'combine' => false]],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap-editable' => [
            ['javascript', 'js/bootstrap-editable.js', ['minify' => false]],
        ],
        'core/app/editable-fields' => [
            ['css', 'css/editable-fields.css', ['minify' => false]],
        ],
        'kinetic' => [
            ['javascript', 'js/kinetic.js'],
        ],
        'core/imageeditor' => [
            ['javascript', 'js/image-editor.js'],
            ['javascript-localized', '/ccm/assets/localization/imageeditor/js'],
            ['css', 'css/image-editor.css'],
        ],
        'dashboard' => [
            ['javascript', 'js/dashboard.js'],
        ],
        'core/frontend/captcha' => [
            ['css', 'css/frontend/captcha.css'],
        ],
        'core/frontend/pagination' => [
            ['css', 'css/frontend/pagination.css'],
        ],
        'core/frontend/errors' => [
            ['css', 'css/frontend/errors.css'],
        ],
        'core/file-manager' => [
            ['javascript', 'js/file-manager.js', ['minify' => false]],
            ['css', 'css/file-manager.css', ['minify' => false]],
        ],
        'core/express' => [
            ['javascript', 'js/express.js', ['minify' => false]],
        ],
        'core/sitemap' => [
            ['javascript', 'js/sitemap.js', ['minify' => false]],
            ['css', 'css/sitemap.css', ['minify' => false]],
        ],
        'core/users' => [
            ['javascript', 'js/users.js', ['minify' => false]],
        ],
        'core/calendar/event-selector' => [
            ['javascript', 'js/calendar/event-selector.js', ['minify' => false]],
        ],
        'core/calendar/admin' => [
            ['javascript', 'js/calendar/admin.js', ['minify' => false]],
        ],
		'core/avatar' => [
			['javascript', 'js/components/avatar.bundle.js', ['minify' => false]],
		],
        'core/notification' => [
            ['javascript', 'js/notification.js', ['minify' => false]],
        ],
        'core/tree' => [
            ['javascript', 'js/tree.js', ['minify' => false]],
        ],
        'core/groups' => [
            ['javascript', 'js/groups.js', ['minify' => false]],
        ],
        'core/gathering' => [
            ['javascript', 'js/gathering.js'],
        ],
        'core/gathering/display' => [
            ['css', 'css/gathering/display.css'],
        ],
        'core/gathering/base' => [
            ['css', 'css/gathering/base.css'],
        ],
        'core/conversation' => [
            ['javascript', 'js/conversations.js'],
            ['javascript-localized', '/ccm/assets/localization/conversations/js'],
            ['css', 'css/conversations.css'],
        ],
        'core/lightbox' => [
            ['javascript', 'js/jquery-magnific-popup.js'],
            ['css', 'css/jquery-magnific-popup.css'],
        ],
        'core/lightbox/launcher' => [
            ['javascript', 'js/lightbox.js'],
        ],
        'core/account' => [
            ['javascript', 'js/account.js'],
            ['css', 'css/account.css'],
        ],
        'core/translator' => [
            ['javascript', 'js/translator.js', ['minify' => false]],
            ['javascript-localized', '/ccm/assets/localization/translator/js'],
            ['css', 'css/translator.css', ['minify' => false]],
        ],
        'core/country-stateprovince-link' => [
            ['javascript', 'js/country-stateprovince-link.js', ['minify' => false]],
        ],
    ],
    'asset_groups' => [
        'jquery/ui' => [
            [
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['css', 'jquery/ui'],
            ],
        ],
        'jquery/visualize' => [
            [
                ['javascript', 'jquery/visualize'],
                ['css', 'jquery/visualize'],
            ],
        ],
        /*
         * @deprecated
         */
        'select2' => [
            [
                ['javascript', 'select2'],
                ['javascript-localized', 'select2'],
                ['css', 'select2'],
            ],
        ],
        'selectize' => [
            [
                ['javascript', 'selectize'],
                ['css', 'selectize'],
            ],
        ],
        'fullcalendar' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'moment'],
                ['javascript', 'fullcalendar'],
                ['javascript', 'fullcalendar/localization'],
                ['css', 'fullcalendar']
            ],
        ],
        'dropzone' => [
            [
                ['javascript', 'dropzone'],
                ['javascript-localized', 'dropzone'],
                ['css', 'dropzone'],
            ],
        ],
        'responsive-slides' => [
            [
                ['javascript', 'responsive-slides'],
                ['css', 'responsive-slides'],
            ],
        ],
        'ace' => [
            [
                ['javascript', 'ace'],
            ],
        ],
		'core/avatar' => [
			[
				['javascript', 'core/avatar'],
			],
		],
        'core/notification' => [
            [
                ['javascript', 'core/notification'],
            ],
        ],
        'core/colorpicker' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'core/events'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'spectrum'],
                ['css', 'spectrum'],
            ],
        ],
        'font-awesome' => [
            [
                ['css', 'font-awesome'],
            ],
        ],
        'core/rating' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'jquery/awesome-rating'],
                ['css', 'font-awesome'],
                ['css', 'jquery/awesome-rating'],
            ],
        ],
        'core/frontend/thumbnail-builder' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'underscore'],
                ['javascript', 'core/frontend/thumbnail-builder'],
            ],
        ],
        'core/style-customizer' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'core/events'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'core/colorpicker'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['javascript', 'jquery/fileupload'],
                ['javascript', 'core/file-manager'],
                ['javascript', 'core/style-customizer'],
                ['css', 'core/app'],
                ['css', 'core/file-manager'],
                ['css', 'jquery/ui'],
                ['css', 'core/colorpicker'],
                ['css', 'core/style-customizer'],
            ],
        ],
        'jquery/fileupload' => [
            [
                ['javascript', 'jquery/fileupload'],
            ],
        ],
        'swfobject' => [
            [
                ['javascript', 'swfobject'],
            ],
        ],
        'redactor' => [
            [
                ['javascript', 'redactor'],
                ['javascript-localized', 'redactor'],
                ['css', 'redactor'],
                ['css', 'font-awesome'],
            ],
        ],
        'moment' => [
            [
                ['javascript', 'moment'],
                ['javascript', 'moment-timezone'],
                ['javascript', 'moment-timezone-data'],
            ],
        ],
        'fancytree' => [
            [
                ['javascript', 'fancytree'],
                ['javascript-localized', 'fancytree'],
                ['css', 'fancytree'],
            ],
        ],
        'core/app' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'core/events'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'bootstrap/dropdown'],
                ['javascript', 'bootstrap/tooltip'],
                ['javascript', 'bootstrap/popover'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['css', 'core/app'],
                ['css', 'font-awesome'],
                ['css', 'jquery/ui'],
            ],
        ],
        'core/app/editable-fields' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'bootstrap/dropdown'],
                ['javascript', 'bootstrap/tooltip'],
                ['javascript', 'bootstrap/popover'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'core/events'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['javascript', 'bootstrap-editable'],
                ['css', 'core/app/editable-fields'],
                ['javascript', 'jquery/fileupload'],
            ],
        ],
        'core/imageeditor' => [
            [
                ['javascript', 'kinetic'],
                ['javascript-localized', 'core/imageeditor'],
                ['javascript', 'core/imageeditor'],
                ['css', 'core/imageeditor'],
            ],
        ],
        'dashboard' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'jquery/touch-punch'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'dashboard'],
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'bootstrap/dropdown'],
                ['javascript', 'bootstrap/tooltip'],
                ['javascript', 'bootstrap/collapse'],
                ['javascript', 'bootstrap/popover'],
                ['javascript', 'bootstrap/transition'],
                ['javascript', 'bootstrap/alert'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['javascript-conditional', 'respond'],
                ['javascript-conditional', 'html5-shiv'],
                ['css', 'core/app'],
                ['css', 'jquery/ui'],
                ['css', 'font-awesome'],
            ],
        ],
        'core/file-manager' => [
            [
                ['css', 'core/app'],
                ['css', 'jquery/ui'],
                ['css', 'core/file-manager'],
                ['css', 'selectize'],
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'bootstrap/tooltip'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'selectize'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['javascript', 'jquery/fileupload'],
                ['javascript', 'core/tree'],
                ['javascript', 'core/file-manager'],
            ],
        ],
        'core/duration' => [
            [
                ['css', 'selectize'],
                ['css', 'jquery/ui'],
                ['css', 'core/duration'],
                ['javascript', 'selectize'],
                ['javascript', 'moment'],
                ['javascript', 'moment-timezone'],
                ['javascript', 'moment-timezone-data'],
                ['javascript', 'core/duration'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
            ],
        ],

        'core/file-folder-selector' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'fancytree'],
                ['javascript-localized', 'fancytree'],
                ['javascript', 'core/tree'],
                ['css', 'fancytree'],
            ],
        ],

        'core/sitemap' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'fancytree'],
                ['javascript', 'selectize'],
                ['javascript-localized', 'fancytree'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['javascript', 'core/sitemap'],
                ['css', 'fancytree'],
                ['css', 'selectize'],
                ['css', 'core/sitemap'],
            ],
        ],
        'core/users' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'core/users'],
            ],
        ],
        'core/calendar/event-selector' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'core/events'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'core/calendar/event-selector'],
                ['css', 'core/app'],
                ['css', 'jquery/ui'],
            ],
        ],
        'core/calendar/admin' => [
            [
                ['javascript-localized', 'core/localization'],
                ['javascript', 'jquery'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'core/events'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'core/app'],
                ['javascript', 'core/calendar/admin'],
                ['css', 'core/app'],
                ['css', 'jquery/ui'],
            ],
        ],
        'core/express' => [
            [
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'bootstrap/tooltip'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'core/localization'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'core/app'],
                ['javascript', 'core/express'],
                ['css', 'core/app'],
                ['css', 'core/express'],
            ],
        ],
        'core/topics' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'fancytree'],
                ['javascript-localized', 'fancytree'],
                ['javascript', 'core/tree'],
                ['css', 'fancytree'],
            ],
        ],
        'core/tree' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'fancytree'],
                ['javascript-localized', 'fancytree'],
                ['javascript', 'core/tree'],
                ['css', 'fancytree'],
            ],
        ],
        'core/groups' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'fancytree'],
                ['javascript-localized', 'fancytree'],
                ['javascript', 'core/tree'],
                ['css', 'fancytree'],
            ],
        ],
        'core/gathering' => [
            [
                ['javascript', 'core/gathering'],
                ['javascript', 'redactor'],
                ['javascript-localized', 'redactor'],
                ['css', 'core/gathering/base'],
                ['css', 'core/conversation'],
                ['css', 'core/gathering/display'],
                ['css', 'redactor'],
            ],
        ],
        'core/conversation' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'underscore'],
                ['javascript', 'core/lightbox'],
                ['javascript', 'dropzone'],
                ['javascript-localized', 'dropzone'],
                ['javascript', 'bootstrap/dropdown'],
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'core/conversation'],
                ['javascript-localized', 'core/conversation'],
                ['css', 'core/conversation'],
                ['css', 'core/frontend/errors'],
                ['css', 'font-awesome'],
                ['css', 'bootstrap/dropdown'],
                ['css', 'core/lightbox'],
                ['css', 'jquery/ui'],
            ],
            true,
        ],
        'core/lightbox' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'core/lightbox'],
                ['javascript', 'core/lightbox/launcher'],
                ['css', 'core/lightbox'],
            ],
        ],
        'core/account' => [
            [
                ['javascript', 'core/account'],
                ['javascript', 'bootstrap/dropdown'],
                ['css', 'bootstrap/dropdown'],
                ['css', 'core/account'],
            ],
        ],
        'core/translator' => [
            [
                ['javascript', 'core/translator'],
                ['javascript-localized', 'core/translator'],
                ['css', 'core/translator'],
            ],
        ],
        'core/country-stateprovince-link' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'core/country-stateprovince-link'],
            ]
        ],
        /* @deprecated keeping this around because certain themes reference it and we don't want to break them. */
        'core/legacy' => [
            [
            ],
        ],
    ],
    // HTTP Client options
    'http_client' => [
        // FALSE to stop from verifying the peer's certificate.
        'sslverifypeer' => true,
        // FALSE to stop from verifying the peer's name (used only with Socket connections, not with cURL ones).
        'sslverifypeername' => false,
        // The name of a file holding one or more certificates to verify the peer with (used only if sslverifypeer is not falsy).
        'sslcafile' => null,
        // A directory that holds multiple CA certificates to verify the peer with (used only if sslverifypeer is not falsy).
        'sslcapath' => null,
        // The number of seconds to wait while trying to connect.
        'connecttimeout' => 5,
        // The maximum number of seconds to allow response from remote server.
        'timeout' => 60,
        // Whether to enable keep-alive connections with the server. Useful and might improve performance if several consecutive requests to the same server are performed.
        'keepalive' => false,
        // Maximum number of redirections to follow (0 = none).
        'maxredirects' => 5,
        // Whether to strictly adhere to RFC 3986 (in practice, this means replacing "+" with "%20").
        'rfc3986strict' => false,
        // Path to a PEM encoded SSL certificate.
        'sslcert' => null,
        // Passphrase for the SSL certificate file.
        'sslpassphrase' => null,
        // Whether to store last response for later retrieval with getLastResponse(). If set to FALSE, getLastResponse() will return NULL.
        'storeresponse' => true,
        // Directory where to store temporary streams by default (if empty, we'll use the default concrete5 temporry directory).
        'streamtmpdir' => null,
        // Whether to strictly follow the RFC when redirecting (see https://framework.zend.com/manual/2.4/en/modules/zend.http.client.advanced.html#http-redirections )
        'strictredirects' => false,
        // User agent identifier string.
        'useragent' => 'concrete5 CMS',
        // Whether to pass the cookie value through urlencode/urldecode. Enabling this breaks support with some web servers. Disabling this limits the range of values the cookies can contain.
        'encodecookies' => true,
        // HTTP protocol version (usually '1.1' or '1.0').
        'httpversion' => '1.1',
        // SSL transport layer ['ssl', 'sslv2', 'sslv3', 'tls'] (applicable only to Socket adapters).
        'ssltransport' => 'tls',
        // Whether to allow self-signed certificates (applicable only to Socket adapters).
        'sslallowselfsigned' => false,
        // Whether to use persistent TCP connections (applicable only to Socket adapters).
        'persistent' => false,
        // The name of a class that implements Psr\Log\LoggerInterface
        'logger' => null,
    ],

    // HTTP middleware for processing http requests
    'middleware' => [
        [
            'priority' => 1,
            'class' => \Concrete\Core\Http\Middleware\ApplicationMiddleware::class,
        ],
        'core_cookie' => \Concrete\Core\Http\Middleware\CookieMiddleware::class,
        'core_xframeoptions' => \Concrete\Core\Http\Middleware\FrameOptionsMiddleware::class,
        'core_thumbnails' => '\Concrete\Core\Http\Middleware\ThumbnailMiddleware',
    ],
];
