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
        // Note, the order of these first few is important - we need events early for other service providers, but it depends on some things.
        'core_system' => '\Concrete\Core\System\SystemServiceProvider',
        'core_events' => '\Concrete\Core\Events\EventsServiceProvider',
        'core_logging' => '\Concrete\Core\Logging\LoggingServiceProvider',
        'core_router' => 'Concrete\Core\Routing\RoutingServiceProvider',
        'core_database' => '\Concrete\Core\Database\DatabaseServiceProvider',
        'core_messenger' => '\Concrete\Core\Messenger\MessengerServiceProvider',
        'core_cache' => '\Concrete\Core\Cache\CacheServiceProvider', // needs to come before api
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
        'core_automation' => '\Concrete\Core\Command\Task\ServiceProvider',
        'core_api' => 'Concrete\Core\Api\ApiServiceProvider',
        'core_form' => '\Concrete\Core\Form\FormServiceProvider',
        'core_session' => '\Concrete\Core\Session\SessionServiceProvider',
        'core_cookie' => '\Concrete\Core\Cookie\CookieServiceProvider',
        'core_http' => '\Concrete\Core\Http\HttpServiceProvider',
        'core_whoops' => '\Concrete\Core\Error\Provider\WhoopsServiceProvider',
        'core_element' => '\Concrete\Core\Filesystem\FilesystemServiceProvider',
        'core_notification' => '\Concrete\Core\Notification\NotificationServiceProvider',
        'core_mercure' => '\Concrete\Core\Notification\Events\MercureServiceProvider',
        'core_package' => '\Concrete\Core\Package\PackageServiceProvider',
        'core_url' => '\Concrete\Core\Url\UrlServiceProvider',
        'core_devices' => '\Concrete\Core\Device\DeviceServiceProvider',

        'core_user' => '\Concrete\Core\User\UserServiceProvider',
        'core_service_manager' => '\Concrete\Core\Service\Manager\ServiceManagerServiceProvider',
        'core_site' => '\Concrete\Core\Site\ServiceProvider',
        'core_search' => \Concrete\Core\Search\SearchServiceProvider::class,
        'core_geolocator' => 'Concrete\Core\Geolocator\GeolocatorServiceProvider',
        'core_calendar' => 'Concrete\Core\Calendar\CalendarServiceProvider',
        'core_summary' => '\Concrete\Core\Summary\ServiceProvider',
        'core_boards' => '\Concrete\Core\Board\ServiceProvider',
        'core_page' => \Concrete\Core\Page\PageServiceProvider::class,

        // Authentication
        'core_oauth' => '\Concrete\Core\Authentication\Type\OAuth\ServiceProvider',
        'core_auth_community' => '\Concrete\Core\Authentication\Type\Community\ServiceProvider',
        'core_auth_google' => '\Concrete\Core\Authentication\Type\Google\ServiceProvider',
        'core_auth_external_concrete' => '\Concrete\Core\Authentication\Type\ExternalConcrete\ServiceProvider',

        // Validator
        'core_validator' => '\Concrete\Core\Validator\ValidatorServiceProvider',
        'core_validator_password' => '\Concrete\Core\Validator\PasswordValidatorServiceProvider',
        'core_validator_user_name' => '\Concrete\Core\Validator\UserNameValidatorServiceProvider',
        'core_validator_user_email' => '\Concrete\Core\Validator\UserEmailValidatorServiceProvider',

        // Express
        'core_attribute' => '\Concrete\Core\Attribute\AttributeServiceProvider',
        'core_express' => '\Concrete\Core\Express\ExpressServiceProvider',

        //
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
        'container',
        'content_editor_snippet',
        'conversation_rating_type',
        'geolocator_library',
        'group',
        'group_set',
        'ip_access_control_category',
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
        'workflow',
        'workflow_progress_category',
        'workflow_type',
        'external_file_provider_type',
        'image_editor',
        'task',
        'task_set',
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
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportDesignTagsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportTreesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportFileImportantThumbnailTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportBoardDataSourcesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportBoardTemplatesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportBoardSlotTemplatesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributeCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributeTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportWorkflowTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportWorkflowProgressCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportWorkflowsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressEntitiesRoutine',
        // if we want the express entity attribute to work we need this to be first.
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributeSetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressAssociationsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressFormsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressRelationsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportThemesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPermissionKeyCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPermissionAccessEntityTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPermissionsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportTasksRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportTaskSetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportJobsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportJobSetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTemplatesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportContainersRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSummaryCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSummaryFieldsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSummaryTemplatesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypesBaseRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageStructureRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportBoardsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageFeedsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypeTargetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypeDefaultsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSiteTypeSkeletonsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSinglePageContentRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportStacksContentRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageContentRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPackagesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportConfigValuesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSystemCaptchaLibrariesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSystemContentEditorSnippetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGeolocatorsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportIpAccessControlCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\PopulateBoardInstancesRoutine',
    ],

    /*
     * Core Routes - no longer used in the core in this way. Look to the routes/ directories instead.
     */
    'routes' => [
    ],

    /*
     * Route themes
     */
    'theme_paths' => [
        '/dashboard' => 'dashboard',
        '/dashboard/*' => 'dashboard',
        '/frontend/install' => VIEW_CORE_THEME,
        '/login' => VIEW_CORE_THEME,
        '/oauth/authorize' => VIEW_CORE_THEME,
        '/register' => VIEW_CORE_THEME,
        '/frontend/maintenance_mode' => VIEW_CORE_THEME,
        '/upgrade' => VIEW_CORE_THEME,
    ],

    /*
     * File Types
     * Keys are the type name
     * Values are arrays with:
     * - comma-separated extensions
     * - file type
     * - handle of an importer (or false)
     * - handle of the inline viewer (of false)
     * - handle of the editor
     * - handle of the package
     */
    'file_types' => [
        'JPEG' => ['jpg,jpeg,jpe', FileType::T_IMAGE, 'image', 'image', 'image'],
        'GIF' => ['gif', FileType::T_IMAGE, 'image', 'image', 'image'],
        'PNG' => ['png', FileType::T_IMAGE, 'image', 'image', 'image'],
        'WebP' => ['webp', FileType::T_IMAGE, 'image', 'image', 'image'],
        'Windows Bitmap' => ['bmp', FileType::T_IMAGE, 'image'],
        'TIFF' => ['tif,tiff', FileType::T_IMAGE, 'image'],
        'HTML' => ['htm,html', FileType::T_DOCUMENT],
        'Flash' => ['swf', FileType::T_IMAGE, 'image'],
        'Icon' => ['ico', FileType::T_IMAGE],
        'SVG' => ['svg', FileType::T_IMAGE, false, 'image'],
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
     * Importer processors
     */
    'import_processors' => [
        'ccm.file.exists' => Concrete\Core\File\Import\Processor\FileExistingValidator::class,
        'ccm.file.extension' => Concrete\Core\File\Import\Processor\FileExtensionValidator::class,
        'ccm.image.autorotate' => Concrete\Core\File\Import\Processor\ImageAutorotator::class,
        'ccm.image.svg' => Concrete\Core\File\Import\Processor\SvgProcessor::class,
        'ccm.image.resize' => Concrete\Core\File\Import\Processor\ImageSizeConstrain::class,
        'ccm.image.thumbnails' => Concrete\Core\File\Import\Processor\ThumbnailGenerator::class,
        'ccm.image.exif_data' => Concrete\Core\File\Import\Processor\ExifDataExtractor::class,
    ],

    /*
     * Assets
     */
    'assets' => [
        // External vendor libraries required to run concrete or our themes at a fundamental level that can't
        // or shouldn't be bundled with our own SCSS/JS files.

        'jquery' => [
            [
                'javascript',
                'js/jquery.js',
                ['position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false],
            ],
        ],

        'vue' => [
            [
                'javascript',
                'js/vue.js',
                ['minify' => false, 'combine' => false]
            ],
        ],

        'bootstrap' => [
            [
                'javascript',
                'js/bootstrap.js',
                [
                    'position' => Asset::ASSET_POSITION_FOOTER,
                    'minify' => false,
                    'combine' => false,
                    'version' => '5.0.0'
                ]
            ]
        ],

        'moment' => [
            ['javascript', 'js/moment.js', ['minify' => false, 'combine' => false, 'version' => '2.24.0']],
            ['javascript-localized', '/ccm/assets/localization/moment/js'],
        ],

        // This is the base CKEditor library from CKEditor
        'ckeditor' => [
            [
                'javascript',
                'js/ckeditor/ckeditor.js',
                ['minify' => false, 'combine' => false]
            ],
        ],

        // These are our CKEditor extensions, including custom plugins, and the jQuery adapter.
        'ckeditor/concrete' => [
            ['javascript', 'js/ckeditor/concrete.js', ['minify' => false, 'combine' => false]],
            ['css', 'css/ckeditor/concrete.css'],
        ],

        'fullcalendar' => [
            ['javascript', 'js/fullcalendar.js', ['minify' => false, 'combine' => false]],
            ['css', 'css/fullcalendar.css'],
        ],

        'font-awesome' => [
            [
                'css',
                'css/fontawesome/all.css',
                ['minify' => false, 'combine' => false]
            ],
        ],

        'google-charts' => [
            [
                'javascript',
                'https://www.gstatic.com/charts/loader.js',
                ['local' => false],
            ],
        ],

        // Foundational Assets
        'core/cms' => [
            ['javascript', 'js/cms.js', ['minify' => false, 'combine' => false]],
            ['javascript-localized', '/ccm/assets/localization/core/js'],
            ['css', 'css/cms.css', ['minify' => false, 'combine' => false]],
        ],

        // Fallback/minimal assets for accessory features
        'feature/accordions/frontend' => [
            ['javascript', 'js/features/accordions/frontend.js', ['minify' => false]],
            ['css', 'css/features/accordions/frontend.css', ['minify' => false]],
        ],

        'feature/account/frontend' => [
            ['javascript', 'js/features/account/frontend.js', ['minify' => false]],
            ['css', 'css/features/account/frontend.css', ['minify' => false]],
        ],

        'feature/profile/frontend' => [
            ['css', 'css/features/profile/frontend.css', ['minify' => false]],
        ],

        'feature/desktop/frontend' => [
            ['javascript', 'js/features/desktop/frontend.js', ['minify' => false]],
            ['css', 'css/features/desktop/frontend.css', ['minify' => false]],
        ],

        'feature/boards/frontend' => [
            ['javascript', 'js/features/boards/frontend.js', ['minify' => false]],
            ['css', 'css/features/boards/frontend.css', ['minify' => false]],
        ],

        'feature/calendar/frontend' => [
            ['javascript', 'js/features/calendar/frontend.js', ['minify' => false]],
            ['css', 'css/features/calendar/frontend.css', ['minify' => false]],
        ],

        'feature/conversations/frontend' => [
            ['javascript', 'js/features/conversations/frontend.js', ['minify' => false]],
            ['css', 'css/features/conversations/frontend.css', ['minify' => false]],
        ],
        'ace' => [
            ['javascript', 'js/ace/ace.js', ['minify' => false, 'combine' => false]],
        ],
        'feature/documents/frontend' => [
            ['javascript', 'js/features/documents/frontend.js', ['minify' => false]],
            ['css', 'css/features/documents/frontend.css', ['minify' => false]],
        ],

        'feature/testimonials/frontend' => [
            ['css', 'css/features/testimonials/frontend.css', ['minify' => false]],
        ],

        'feature/faq/frontend' => [
            ['css', 'css/features/faq/frontend.css', ['minify' => false]],
        ],

        'feature/basics/frontend' => [
            ['css', 'css/features/basics/frontend.css', ['minify' => false]],
        ],

        'feature/navigation/frontend' => [
            ['javascript', 'js/features/navigation/frontend.js', ['minify' => false]],
            ['css', 'css/features/navigation/frontend.css', ['minify' => false]],
        ],

        'feature/imagery/frontend' => [
            ['javascript', 'js/features/imagery/frontend.js', ['minify' => false]],
            ['css', 'css/features/imagery/frontend.css', ['minify' => false]],
        ],

        'feature/express/frontend' => [
            ['javascript', 'js/features/express/frontend.js', ['minify' => false]],
            ['css', 'css/features/express/frontend.css', ['minify' => false]],
        ],

        'feature/search/frontend' => [
            ['css', 'css/features/search/frontend.css', ['minify' => false]],
        ],

        'feature/social/frontend' => [
            ['css', 'css/features/social/frontend.css', ['minify' => false]],
        ],

        'feature/video/frontend' => [
            ['css', 'css/features/video/frontend.css', ['minify' => false]],
        ],

        'feature/taxonomy/frontend' => [
            ['css', 'css/features/taxonomy/frontend.css', ['minify' => false]],
        ],

        'feature/maps/frontend' => [
            ['javascript', 'js/features/maps/frontend.js', ['minify' => false]],
            ['css', 'css/features/maps/frontend.css', ['minify' => false]],
        ],

        'feature/multilingual/frontend' => [
            ['javascript', 'js/features/multilingual/frontend.js', ['minify' => false]],
            ['css', 'css/features/multilingual/frontend.css', ['minify' => false]],
        ],

        'tui-image-editor' => [
            [
                'css',
                'css/tui-image-editor.css',
            ],
            [
                'javascript',
                'js/tui-image-editor.js',
                ['minify' => false, 'combine' => false]
            ],
        ],
        'core/translator' => [
            ['javascript', 'js/translator.js', ['minify' => false]],
            ['javascript-localized', '/ccm/assets/localization/translator/js'],
            ['css', 'css/translator.css', ['minify' => false]],
        ],

        'htmldiff' => [
            ['css', 'css/htmldiff.css'],
        ],
    ],
    'asset_groups' => [
        'jquery' => [
            [
                ['javascript', 'jquery'],
            ],
        ],

        'bootstrap' => [
            [
                ['javascript', 'bootstrap']
            ],
        ],

        'moment' => [
            [
                ['javascript', 'moment'],
                ['javascript-localized', 'moment'],
            ],
        ],

        'vue' => [
            [
                ['javascript', 'vue'],
            ],
        ],

        'font-awesome' => [
            [
                ['css', 'font-awesome'],
            ],
        ],

        'ckeditor' => [
            [
                ['javascript', 'ckeditor'],
                ['javascript', 'ckeditor/concrete'],
                ['css', 'ckeditor/concrete'],
            ],
        ],
        'ace' => [
            [
                ['javascript', 'ace'],
            ],
        ],
        'core/cms' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'bootstrap'],
                ['javascript', 'moment'],
                ['javascript', 'vue'],
                ['css', 'font-awesome'],
                ['javascript', 'core/cms'],
                ['javascript-localized', 'core/cms'],
                ['css', 'core/cms'],
            ],
        ],
        'fullcalendar' => [
            [
                ['javascript', 'fullcalendar'],
                ['css', 'fullcalendar'],
            ],
        ],

        'tui-image-editor' => [
            [
                ['css', 'tui-image-editor'],
                ['javascript', 'tui-image-editor'],
            ],
        ],

        'core/translator' => [
            [
                ['css', 'core/translator'],
                ['javascript', 'core/translator'],
                ['javascript-localized', 'core/translator'],
            ],
        ],

        // Fallback/minimal assets groups
        'feature/accordions/frontend' => [
            [
                ['javascript', 'feature/accordions/frontend'],
                ['css', 'feature/accordions/frontend'],
            ],
        ],

        'feature/account/frontend' => [
            [
                ['javascript', 'feature/account/frontend'],
                ['css', 'feature/account/frontend'],
            ],
        ],

        'feature/profile/frontend' => [
            [
                ['css', 'feature/profile/frontend'],
            ],
        ],

        'feature/desktop/frontend' => [
            [
                ['javascript', 'feature/desktop/frontend'],
                ['css', 'feature/desktop/frontend'],
            ],
        ],

        'feature/calendar/frontend' => [
            [
                ['javascript', 'moment'],
                ['javascript', 'feature/calendar/frontend'],
                ['css', 'feature/calendar/frontend'],
            ],
        ],

        'feature/conversations/frontend' => [
            [
                ['javascript', 'feature/conversations/frontend'],
                ['css', 'feature/conversations/frontend'],
            ],
        ],

        'feature/documents/frontend' => [
            [
                ['javascript', 'feature/documents/frontend'],
                ['javascript-localized', 'core/cms'],
                ['css', 'feature/documents/frontend'],
            ],
        ],

        'feature/faq/frontend' => [
            [
                ['css', 'feature/faq/frontend'],
            ],
        ],

        'feature/imagery/frontend' => [
            [
                ['javascript', 'feature/imagery/frontend'],
                ['css', 'feature/imagery/frontend'],
            ],
        ],

        'feature/navigation/frontend' => [
            [
                ['javascript', 'feature/navigation/frontend'],
                ['css', 'feature/navigation/frontend'],
            ],
        ],

        'feature/video/frontend' => [
            [
                ['css', 'feature/video/frontend'],
            ],
        ],

        'feature/social/frontend' => [
            [
                ['css', 'feature/social/frontend'],
            ],
        ],

        'feature/express/frontend' => [
            [
                ['javascript', 'feature/express/frontend'],
                ['css', 'feature/express/frontend'],
            ],
        ],

        'feature/maps/frontend' => [
            [
                ['javascript', 'feature/maps/frontend'],
                ['css', 'feature/maps/frontend'],
            ],
        ],

        'feature/multilingual/frontend' => [
            [
                ['javascript', 'feature/multilingual/frontend'],
                ['css', 'feature/multilingual/frontend'],
            ],
        ],

        'feature/search/frontend' => [
            [
                ['css', 'feature/search/frontend'],
            ],
        ],

        'feature/taxonomy/frontend' => [
            [
                ['css', 'feature/taxonomy/frontend'],
            ],
        ],

        'feature/testimonials/frontend' => [
            [
                ['css', 'feature/testimonials/frontend'],
            ],
        ],

        'feature/basics/frontend' => [
            [
                ['css', 'feature/basics/frontend'],
            ],
        ],
        'core/conversation' => [
            [
            ],
            true,
        ],
        'htmldiff' => [
            [
                ['css', 'htmldiff'],
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
        // Directory where to store temporary streams by default (if empty, we'll use the default concrete temporry directory).
        'streamtmpdir' => null,
        // Whether to strictly follow the RFC when redirecting (see https://framework.zend.com/manual/2.4/en/modules/zend.http.client.advanced.html#http-redirections )
        'strictredirects' => false,
        // User agent identifier string.
        'useragent' => 'Concrete CMS',
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
        'core_csp' => \Concrete\Core\Http\Middleware\ContentSecurityPolicyMiddleware::class,
        'core_hsts' => \Concrete\Core\Http\Middleware\StrictTransportSecurityMiddleware::class,
        'core_xframeoptions' => \Concrete\Core\Http\Middleware\FrameOptionsMiddleware::class
    ],

    'command_handlers' => [

    ],

];
