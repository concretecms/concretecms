<?php
use Concrete\Core\Asset\Asset;
use Concrete\Core\File\Type\Type as FileType;

return array(

    'debug'               => false,
    'namespace'           => 'Application',

    /**
     * Core Aliases
     */
    'aliases'             => array(
        'Area'                                 => '\Concrete\Core\Area\Area',
        'Asset'                                => '\Concrete\Core\Asset\Asset',
        'AssetList'                            => '\Concrete\Core\Asset\AssetList',
        'AttributeSet'                         => '\Concrete\Core\Attribute\Set',
        'AuthenticationType'                   => '\Concrete\Core\Authentication\AuthenticationType',
        'Block'                                => '\Concrete\Core\Block\Block',
        'BlockType'                            => '\Concrete\Core\Block\BlockType\BlockType',
        'BlockTypeList'                        => '\Concrete\Core\Block\BlockType\BlockTypeList',
        'BlockTypeSet'                         => '\Concrete\Core\Block\BlockType\Set',
        'Cache'                                => '\Concrete\Core\Cache\Cache',
        'Request'                              => '\Concrete\Core\Http\Request',
        'CacheLocal'                           => '\Concrete\Core\Cache\CacheLocal',
        'Collection'                           => '\Concrete\Core\Page\Collection\Collection',
        'CollectionAttributeKey'               => '\Concrete\Core\Attribute\Key\CollectionKey',
        'CollectionVersion'                    => '\Concrete\Core\Page\Collection\Version\Version',
        'ConcreteAuthenticationTypeController' => '\Concrete\Authentication\Concrete\Controller',
        'Controller'                           => '\Concrete\Core\Controller\Controller',
        'Conversation'                         => '\Concrete\Core\Conversation\Conversation',
        'ConversationEditor'                   => '\Concrete\Core\Conversation\Editor\Editor',
        'ConversationFlagType'                 => '\Concrete\Core\Conversation\FlagType\FlagType',
        'ConversationMessage'                  => '\Concrete\Core\Conversation\Message\Message',
        'ConversationRatingType'               => '\Concrete\Core\Conversation\Rating\Type',
        'Cookie'                               => '\Concrete\Core\Cookie\Cookie',
        'Environment'                          => '\Concrete\Core\Foundation\Environment',
        'FacebookAuthenticationTypeController' => '\Concrete\Authentication\Facebook\Controller',
        'File'                                 => '\Concrete\Core\File\File',
        'FileAttributeKey'                     => '\Concrete\Core\Attribute\Key\FileKey',
        'FileImporter'                         => '\Concrete\Core\File\Importer',
        'FileList'                             => '\Concrete\Core\File\FileList',
        'FilePermissions'                      => '\Concrete\Core\Legacy\FilePermissions',
        'FileSet'                              => '\Concrete\Core\File\Set\Set',
        'FileVersion'                          => '\Concrete\Core\File\Version',
        'GlobalArea'                           => '\Concrete\Core\Area\GlobalArea',
        'Group'                                => '\Concrete\Core\User\Group\Group',
        'GroupList'                            => '\Concrete\Core\User\Group\GroupList',
        'GroupSet'                             => '\Concrete\Core\User\Group\GroupSet',
        'GroupSetList'                         => '\Concrete\Core\User\Group\GroupSetList',
        'GroupTree'                            => '\Concrete\Core\Tree\Type\Group',
        'GroupTreeNode'                        => '\Concrete\Core\Tree\Node\Type\Group',
        'Job'                                  => '\Concrete\Core\Job\Job',
        'JobSet'                               => '\Concrete\Core\Job\Set',
        'Loader'                               => '\Concrete\Core\Legacy\Loader',
        'Localization'                         => '\Concrete\Core\Localization\Localization',
        'Marketplace'                          => '\Concrete\Core\Marketplace\Marketplace',
        'Package'                              => '\Concrete\Core\Package\Package',
        'Page'                                 => '\Concrete\Core\Page\Page',
        'PageCache'                            => '\Concrete\Core\Cache\Page\PageCache',
        'PageController'                       => '\Concrete\Core\Page\Controller\PageController',
        'PageEditResponse'                     => '\Concrete\Core\Page\EditResponse',
        'PageList'                             => '\Concrete\Core\Page\PageList',
        'PageTemplate'                         => '\Concrete\Core\Page\Template',
        'PageTheme'                            => '\Concrete\Core\Page\Theme\Theme',
        'PageType'                             => '\Concrete\Core\Page\Type\Type',
        'PermissionAccess'                     => '\Concrete\Core\Permission\Access\Access',
        'PermissionKey'                        => '\Concrete\Core\Permission\Key\Key',
        'PermissionKeyCategory'                => '\Concrete\Core\Permission\Category',
        'Permissions'                          => '\Concrete\Core\Permission\Checker',
        'Queue'                                => '\Concrete\Core\Foundation\Queue\Queue',
        'QueueableJob'                         => '\Concrete\Core\Job\QueueableJob',
        'Redirect'                             => '\Concrete\Core\Routing\Redirect',
        'RedirectResponse'                     => '\Concrete\Core\Routing\RedirectResponse',
        'Response'                             => '\Concrete\Core\Http\Response',
        'Router'                               => '\Concrete\Core\Routing\Router',
        'SinglePage'                           => '\Concrete\Core\Page\Single',
        'Stack'                                => '\Concrete\Core\Page\Stack\Stack',
        'StackList'                            => '\Concrete\Core\Page\Stack\StackList',
        'StartingPointPackage'                 => '\Concrete\Core\Package\StartingPointPackage',
        'TaskPermission'                       => '\Concrete\Core\Legacy\TaskPermission',
        'User'                                 => '\Concrete\Core\User\User',
        'UserAttributeKey'                     => '\Concrete\Core\Attribute\Key\UserKey',
        'UserInfo'                             => '\Concrete\Core\User\UserInfo',
        'UserList'                             => '\Concrete\Core\User\UserList',
        'View'                                 => '\Concrete\Core\View\View',
        'Workflow'                             => '\Concrete\Core\Workflow\Workflow'
    ),

    /**
     * Core Providers
     */
    'providers'           => array(
        'core_file'                   => '\Concrete\Core\File\FileServiceProvider',
        'core_encryption'             => '\Concrete\Core\Encryption\EncryptionServiceProvider',
        'core_validation'             => '\Concrete\Core\Validation\ValidationServiceProvider',
        'core_localization'           => '\Concrete\Core\Localization\LocalizationServiceProvider',
        'core_multilingual'           => '\Concrete\Core\Multilingual\MultilingualServiceProvider',
        'core_feed'                   => '\Concrete\Core\Feed\FeedServiceProvider',
        'core_html'                   => '\Concrete\Core\Html\HtmlServiceProvider',
        'core_editor'                 => '\Concrete\Core\Editor\EditorServiceProvider',
        'core_mail'                   => '\Concrete\Core\Mail\MailServiceProvider',
        'core_application'            => '\Concrete\Core\Application\ApplicationServiceProvider',
        'core_utility'                => '\Concrete\Core\Utility\UtilityServiceProvider',
        'core_manager_grid_framework' => '\Concrete\Core\Page\Theme\GridFramework\ManagerServiceProvider',
        'core_manager_pagination_view' => '\Concrete\Core\Search\Pagination\View\ManagerServiceProvider',
        'core_manager_page_type_validator' => '\Concrete\Core\Page\Type\Validator\ManagerServiceProvider',
        'core_database'     => '\Concrete\Core\Database\DatabaseServiceProvider',
        'core_form'         => '\Concrete\Core\Form\FormServiceProvider',
        'core_session'      => '\Concrete\Core\Session\SessionServiceProvider',
        'core_cookie'       => '\Concrete\Core\Cookie\CookieServiceProvider',
        'core_http'         => '\Concrete\Core\Http\HttpServiceProvider',
        'core_events'       => '\Concrete\Core\Events\EventsServiceProvider',
        'core_whoops'       => '\Concrete\Core\Error\Provider\WhoopsServiceProvider',
        'core_logging'      => '\Concrete\Core\Logging\LoggingServiceProvider',
        'core_cache'        => '\Concrete\Core\Cache\CacheServiceProvider',
        'core_url'          => '\Concrete\Core\Url\UrlServiceProvider',

        // Authentication
        'core_oauth'          => '\Concrete\Core\Authentication\Type\OAuth\ServiceProvider',
        'core_auth_community' => '\Concrete\Core\Authentication\Type\Community\ServiceProvider',
        'core_auth_facebook'  => '\Concrete\Core\Authentication\Type\Facebook\ServiceProvider',
        'core_auth_google'    => '\Concrete\Core\Authentication\Type\Google\ServiceProvider',
        'core_auth_twitter'   => '\Concrete\Core\Authentication\Type\Twitter\ServiceProvider'
    ),

    /**
     * Core Facades
     */
    'facades'             => array(
        'Core'     => '\Concrete\Core\Support\Facade\Application',
        'Session'  => '\Concrete\Core\Support\Facade\Session',
        'Cookie'   => '\Concrete\Core\Support\Facade\Cookie',
        'Database' => '\Concrete\Core\Support\Facade\Database',
        'ORM'      => '\Concrete\Core\Support\Facade\DatabaseORM',
        'Events'   => '\Concrete\Core\Support\Facade\Events',
        'Route'    => '\Concrete\Core\Support\Facade\Route',
        'Log'      => '\Concrete\Core\Support\Facade\Log',
        'Image'    => '\Concrete\Core\Support\Facade\Image',
        'Config'   => '\Concrete\Core\Support\Facade\Config',
        'URL'      => '\Concrete\Core\Support\Facade\Url'
    ),

    /**
     * Core Routes
     */
    'routes'              => array(

        /**
         * Dialogs
         */
        "/ccm/system/dialogs/area/design/"                                              => array('\Concrete\Controller\Dialog\Area\Design::view'),
        "/ccm/system/dialogs/area/design/reset"                                         => array('\Concrete\Controller\Dialog\Area\Design::reset'),
        "/ccm/system/dialogs/area/design/submit"                                        => array('\Concrete\Controller\Dialog\Area\Design::submit'),
        "/ccm/system/dialogs/area/layout/presets/{arLayoutID}"                          => array('\Concrete\Controller\Dialog\Area\Layout\Presets::view'),
        "/ccm/system/dialogs/area/layout/presets/{arLayoutID}/submit"                   => array('\Concrete\Controller\Dialog\Area\Layout\Presets::submit'),
        "/ccm/system/dialogs/area/layout/presets/get/{arLayoutPresetID}"                => array('\Concrete\Controller\Dialog\Area\Layout\Presets::getPresetData'),
        "/ccm/system/dialogs/area/layout/presets/manage/"                               => array('\Concrete\Controller\Dialog\Area\Layout\Presets\Manage::view'),
        "/ccm/system/dialogs/area/layout/presets/manage/delete"                         => array('\Concrete\Controller\Dialog\Area\Layout\Presets\Manage::delete'),

        "/ccm/system/dialogs/block/aliasing/"                                           => array('\Concrete\Controller\Dialog\Block\Aliasing::view'),
        "/ccm/system/dialogs/block/aliasing/submit"                                     => array('\Concrete\Controller\Dialog\Block\Aliasing::submit'),
        "/ccm/system/dialogs/block/edit/"                                               => array('\Concrete\Controller\Dialog\Block\Edit::view'),
        "/ccm/system/dialogs/block/edit/submit/"                                        => array('\Concrete\Controller\Dialog\Block\Edit::submit'),
        "/ccm/system/dialogs/block/cache/"                                              => array('\Concrete\Controller\Dialog\Block\Cache::view'),
        "/ccm/system/dialogs/block/cache/submit"                                        => array('\Concrete\Controller\Dialog\Block\Cache::submit'),
        "/ccm/system/dialogs/block/design/"                                             => array('\Concrete\Controller\Dialog\Block\Design::view'),
        "/ccm/system/dialogs/block/design/reset"                                        => array('\Concrete\Controller\Dialog\Block\Design::reset'),
        "/ccm/system/dialogs/block/design/submit"                                       => array('\Concrete\Controller\Dialog\Block\Design::submit'),
        "/ccm/system/dialogs/block/permissions/detail/"                                 => array('\Concrete\Controller\Dialog\Block\Permissions::viewDetail'),
        "/ccm/system/dialogs/block/permissions/guest_access/"                           => array('\Concrete\Controller\Dialog\Block\Permissions\GuestAccess::__construct'),
        "/ccm/system/dialogs/block/permissions/list/"                                   => array('\Concrete\Controller\Dialog\Block\Permissions::viewList'),

        "/ccm/system/dialogs/file/upload_complete"                                      => array('\Concrete\Controller\Dialog\File\UploadComplete::view'),
        "/ccm/system/dialogs/file/bulk/delete"                                          => array('\Concrete\Controller\Dialog\File\Bulk\Delete::view'),
        "/ccm/system/dialogs/file/bulk/delete/delete_files"                             => array('\Concrete\Controller\Dialog\File\Bulk\Delete::deleteFiles'),
        "/ccm/system/dialogs/file/bulk/properties"                                      => array('\Concrete\Controller\Dialog\File\Bulk\Properties::view'),
        "/ccm/system/dialogs/file/bulk/sets"                                            => array('\Concrete\Controller\Dialog\File\Bulk\Sets::view'),
        "/ccm/system/dialogs/file/bulk/sets/submit"                                     => array('\Concrete\Controller\Dialog\File\Bulk\Sets::submit'),
        "/ccm/system/dialogs/file/bulk/properties/clear_attribute"                      => array('\Concrete\Controller\Dialog\File\Bulk\Properties::clearAttribute'),
        "/ccm/system/dialogs/file/bulk/properties/update_attribute"                     => array('\Concrete\Controller\Dialog\File\Bulk\Properties::updateAttribute'),
        "/ccm/system/dialogs/file/sets"                                                 => array('\Concrete\Controller\Dialog\File\Sets::view'),
        "/ccm/system/dialogs/file/sets/submit"                                          => array('\Concrete\Controller\Dialog\File\Sets::submit'),
        "/ccm/system/dialogs/file/properties"                                           => array('\Concrete\Controller\Dialog\File\Properties::view'),
        "/ccm/system/dialogs/file/properties/clear_attribute"                           => array('\Concrete\Controller\Dialog\File\Properties::clear_attribute'),
        "/ccm/system/dialogs/file/properties/save"                                      => array('\Concrete\Controller\Dialog\File\Properties::save'),
        "/ccm/system/dialogs/file/properties/update_attribute"                          => array('\Concrete\Controller\Dialog\File\Properties::update_attribute'),
        "/ccm/system/dialogs/file/search"                                               => array('\Concrete\Controller\Dialog\File\Search::view'),
        "/ccm/system/dialogs/file/search/customize"                                     => array('\Concrete\Controller\Dialog\File\Search\Customize::view'),
        "/ccm/system/dialogs/file/search/customize/submit"                              => array('\Concrete\Controller\Dialog\File\Search\Customize::submit'),
        "/ccm/system/dialogs/file/thumbnails"                                           => array('\Concrete\Controller\Dialog\File\Thumbnails::view'),
        "/ccm/system/dialogs/file/thumbnails/edit"                                      => array('\Concrete\Controller\Dialog\File\Thumbnails\Edit::view'),

        "/ccm/system/dialogs/group/search"                                              => array('\Concrete\Controller\Dialog\Group\Search::view'),

        "/ccm/system/dialogs/page/add"                                                  => array('\Concrete\Controller\Dialog\Page\Add::view'),
        "/ccm/system/dialogs/page/add_block"                                            => array('\Concrete\Controller\Dialog\Page\AddBlock::view'),
        "/ccm/system/dialogs/page/add_block/submit"                                     => array('\Concrete\Controller\Dialog\Page\AddBlock::submit'),
        "/ccm/system/dialogs/page/add_block_list"                                       => array('\Concrete\Controller\Dialog\Page\AddBlockList::view'),
        "/ccm/system/dialogs/page/add_external"                                         => array('\Concrete\Controller\Dialog\Page\AddExternal::view'),
        "/ccm/system/dialogs/page/add_external/submit"                                  => array('\Concrete\Controller\Dialog\Page\AddExternal::submit'),
        "/ccm/system/dialogs/page/add/compose/{ptID}/{cParentID}"                       => array('\Concrete\Controller\Dialog\Page\Add\Compose::view'),
        "/ccm/system/dialogs/page/add/compose/submit"                                   => array('\Concrete\Controller\Dialog\Page\Add\Compose::submit'),
        "/ccm/system/dialogs/page/add/submit"                                           => array('\Concrete\Controller\Dialog\Page\Add::submit'),
        "/ccm/system/dialogs/page/attributes"                                           => array('\Concrete\Controller\Dialog\Page\Attributes::view'),
        "/ccm/system/dialogs/page/bulk/properties"                                      => array('\Concrete\Controller\Dialog\Page\Bulk\Properties::view'),
        "/ccm/system/dialogs/page/bulk/properties/clear_attribute"                      => array('\Concrete\Controller\Dialog\Page\Bulk\Properties::clearAttribute'),
        "/ccm/system/dialogs/page/bulk/properties/update_attribute"                     => array('\Concrete\Controller\Dialog\Page\Bulk\Properties::updateAttribute'),
        "/ccm/system/dialogs/page/clipboard"                                            => array('\Concrete\Controller\Dialog\Page\Clipboard::view'),
        "/ccm/system/dialogs/page/delete"                                               => array('\Concrete\Controller\Dialog\Page\Delete::view'),
        "/ccm/system/dialogs/page/delete/submit"                                        => array('\Concrete\Controller\Dialog\Page\Delete::submit'),
        "/ccm/system/dialogs/page/delete_alias"                                         => array('\Concrete\Controller\Dialog\Page\DeleteAlias::view'),
        "/ccm/system/dialogs/page/delete_alias/submit"                                  => array('\Concrete\Controller\Dialog\Page\DeleteAlias::submit'),
        "/ccm/system/dialogs/page/delete_from_sitemap"                                  => array('\Concrete\Controller\Dialog\Page\Delete::viewFromSitemap'),
        "/ccm/system/dialogs/page/design"                                               => array('\Concrete\Controller\Dialog\Page\Design::view'),
        "/ccm/system/dialogs/page/design/submit"                                        => array('\Concrete\Controller\Dialog\Page\Design::submit'),
        "/ccm/system/dialogs/page/design/css"                                           => array('\Concrete\Controller\Dialog\Page\Design\Css::view'),
        "/ccm/system/dialogs/page/design/css/submit"                                    => array('\Concrete\Controller\Dialog\Page\Design\Css::submit'),
        "/ccm/system/dialogs/page/edit_external"                                        => array('\Concrete\Controller\Dialog\Page\EditExternal::view'),
        "/ccm/system/dialogs/page/edit_external/submit"                                 => array('\Concrete\Controller\Dialog\Page\EditExternal::submit'),
        "/ccm/system/dialogs/page/location"                                             => array('\Concrete\Controller\Dialog\Page\Location::view'),
        "/ccm/system/dialogs/page/search"                                               => array('\Concrete\Controller\Dialog\Page\Search::view'),
        "/ccm/system/dialogs/page/search/customize"                                     => array('\Concrete\Controller\Dialog\Page\Search\Customize::view'),
        "/ccm/system/dialogs/page/search/customize/submit"                              => array('\Concrete\Controller\Dialog\Page\Search\Customize::submit'),
        "/ccm/system/dialogs/page/seo"                                                  => array('\Concrete\Controller\Dialog\Page\Seo::view'),

        "/ccm/system/dialogs/user/bulk/properties"                                      => array('\Concrete\Controller\Dialog\User\Bulk\Properties::view'),
        "/ccm/system/dialogs/user/bulk/properties/clear_attribute"                      => array('\Concrete\Controller\Dialog\User\Bulk\Properties::clearAttribute'),
        "/ccm/system/dialogs/user/bulk/properties/update_attribute"                     => array('\Concrete\Controller\Dialog\User\Bulk\Properties::updateAttribute'),
        "/ccm/system/dialogs/user/search"                                               => array('\Concrete\Controller\Dialog\User\Search::view'),
        "/ccm/system/dialogs/user/search/customize"                                     => array('\Concrete\Controller\Dialog\User\Search\Customize::view'),
        "/ccm/system/dialogs/user/search/customize/submit"                              => array('\Concrete\Controller\Dialog\User\Search\Customize::submit'),
        /**
         * Conversations
         */
        "/ccm/system/dialogs/conversation/subscribe/{cnvID}"                            => array('\Concrete\Controller\Dialog\Conversation\Subscribe::view'),
        "/ccm/system/dialogs/conversation/subscribe/subscribe/{cnvID}"                  => array('\Concrete\Controller\Dialog\Conversation\Subscribe::subscribe'),
        "/ccm/system/dialogs/conversation/subscribe/unsubscribe/{cnvID}"                => array('\Concrete\Controller\Dialog\Conversation\Subscribe::unsubscribe'),

        /**
         * Help
         */
        "/ccm/system/dialogs/help/introduction/"                                        => array('\Concrete\Controller\Dialog\Help\Introduction::view'),

        /**
         * Files
         */
        "/ccm/system/file/approve_version"                                              => array('\Concrete\Controller\Backend\File::approveVersion'),
        "/ccm/system/file/delete_version"                                               => array('\Concrete\Controller\Backend\File::deleteVersion'),
        "/ccm/system/file/duplicate"                                                    => array('\Concrete\Controller\Backend\File::duplicate'),
        "/ccm/system/file/get_json"                                                     => array('\Concrete\Controller\Backend\File::getJSON'),
        "/ccm/system/file/rescan"                                                       => array('\Concrete\Controller\Backend\File::rescan'),
        "/ccm/system/file/star"                                                         => array('\Concrete\Controller\Backend\File::star'),
        "/ccm/system/file/upload"                                                       => array('\Concrete\Controller\Backend\File::upload'),

        /**
         * Users
         */
        "/ccm/system/user/add_group"                                                    => array('\Concrete\Controller\Backend\User::addGroup'),
        "/ccm/system/user/remove_group"                                                 => array('\Concrete\Controller\Backend\User::removeGroup'),

        /**
         * Page actions - non UI
         */
        "/ccm/system/page/arrange_blocks/"                                              => array('\Concrete\Controller\Backend\Page\ArrangeBlocks::arrange'),
        "/ccm/system/page/check_in/{cID}/{token}"                                       => array('\Concrete\Controller\Backend\Page::exitEditMode'),
        "/ccm/system/page/create/{ptID}"                                                => array('\Concrete\Controller\Backend\Page::create'),
        "/ccm/system/page/create/{ptID}/{parentID}"                                     => array('\Concrete\Controller\Backend\Page::create'),
        "/ccm/system/page/get_json"                                                     => array('\Concrete\Controller\Backend\Page::getJSON'),
        "/ccm/system/page/multilingual/assign"                                          => array('\Concrete\Controller\Backend\Page\Multilingual::assign'),
        "/ccm/system/page/multilingual/create_new"                                      => array('\Concrete\Controller\Backend\Page\Multilingual::create_new'),
        "/ccm/system/page/multilingual/ignore"                                          => array('\Concrete\Controller\Backend\Page\Multilingual::ignore'),
        "/ccm/system/page/multilingual/unmap"                                           => array('\Concrete\Controller\Backend\Page\Multilingual::unmap'),

        /**
         * Block actions - non UI
         */
        "/ccm/system/block/render/"                                                     => array('\Concrete\Controller\Backend\Block::render'),

        /**
         * Misc
         */
        "/ccm/system/css/layout/{bID}"                                                  => array('\Concrete\Controller\Frontend\Stylesheet::layout'),
        "/ccm/system/css/page/{cID}/{stylesheet}/{cvID}"                                => array('\Concrete\Controller\Frontend\Stylesheet::page_version'),
        "/ccm/system/css/page/{cID}/{stylesheet}"                                       => array('\Concrete\Controller\Frontend\Stylesheet::page'),
        "/ccm/system/backend/editor_data/"                                              => array('\Concrete\Controller\Backend\EditorData::view'),
        "/ccm/system/backend/get_remote_help/"                                          => array('\Concrete\Controller\Backend\GetRemoteHelp::view'),
        "/ccm/system/backend/intelligent_search/"                                       => array('\Concrete\Controller\Backend\IntelligentSearch::view'),
        "/ccm/system/jobs"                                                              => array('\Concrete\Controller\Frontend\Jobs::view'),
        "/ccm/system/jobs/run_single"                                                   => array('\Concrete\Controller\Frontend\Jobs::run_single'),
        "/ccm/system/jobs/check_queue"                                                  => array('\Concrete\Controller\Frontend\Jobs::check_queue'),
        // @TODO remove the line below
        "/tools/required/jobs"                                                          => array('\Concrete\Controller\Frontend\Jobs::view'),
        "/tools/required/jobs/check_queue"                                              => array('\Concrete\Controller\Frontend\Jobs::check_queue'),
        "/tools/required/jobs/run_single"                                               => array('\Concrete\Controller\Frontend\Jobs::run_single'),
        // end removing lines
        "/ccm/system/upgrade/"                                                          => array('\Concrete\Controller\Upgrade::view'),
        "/ccm/system/upgrade/submit"                                                    => array('\Concrete\Controller\Upgrade::submit'),

        /**
         * General Attribute
         */
        "/ccm/system/attribute/attribute_sort/set"                                      => array('\Concrete\Controller\Backend\Attributes::sortInSet'),
        "/ccm/system/attribute/attribute_sort/user"                                     => array('\Concrete\Controller\Backend\Attributes::sortUser'),

        /**
         * Marketplace
         */
        "/ccm/system/dialogs/marketplace/checkout"                               => array('\Concrete\Controller\Dialog\Marketplace\Checkout::view'),
        "/ccm/system/dialogs/marketplace/download"                               => array('\Concrete\Controller\Dialog\Marketplace\Download::view'),
        "/ccm/system/marketplace/connect"                                        => array('\Concrete\Controller\Backend\Marketplace\Connect::view'),
        "/ccm/system/marketplace/search"                                        => array('\Concrete\Controller\Backend\Marketplace\Search::view'),

        /**
         * Search Routes
         */
        "/ccm/system/search/files/field/{field}"                                        => array('\Concrete\Controller\Search\Files::field'),
        "/ccm/system/search/files/submit"                                               => array('\Concrete\Controller\Search\Files::submit'),
        "/ccm/system/search/groups/submit"                                              => array('\Concrete\Controller\Search\Groups::submit'),
        "/ccm/system/search/pages/field/{field}"                                        => array('\Concrete\Controller\Search\Pages::field'),
        "/ccm/system/search/pages/submit"                                               => array('\Concrete\Controller\Search\Pages::submit'),
        "/ccm/system/search/users/field/{field}"                                        => array('\Concrete\Controller\Search\Users::field'),
        "/ccm/system/search/users/submit"                                               => array('\Concrete\Controller\Search\Users::submit'),

        /**
         * Panels - top level
         */
        "/ccm/system/panels/add"                                                        => array('\Concrete\Controller\Panel\Add::view'),
        "/ccm/system/panels/dashboard"                                                  => array('\Concrete\Controller\Panel\Dashboard::view'),
        "/ccm/system/panels/multilingual"                                               => array('\Concrete\Controller\Panel\Multilingual::view'),
        "/ccm/system/panels/page"                                                       => array('\Concrete\Controller\Panel\Page::view'),
        "/ccm/system/panels/page/attributes"                                            => array('\Concrete\Controller\Panel\Page\Attributes::view'),
        "/ccm/system/panels/page/check_in"                                              => array('\Concrete\Controller\Panel\Page\CheckIn::__construct'),
        "/ccm/system/panels/page/check_in/submit"                                       => array('\Concrete\Controller\Panel\Page\CheckIn::submit'),
        "/ccm/system/panels/page/design"                                                => array('\Concrete\Controller\Panel\Page\Design::view'),
        "/ccm/system/panels/page/design/customize/{pThemeID}"                           => array('\Concrete\Controller\Panel\Page\Design\Customize::view'),
        "/ccm/system/panels/page/design/customize/apply_to_page/{pThemeID}"             => array('\Concrete\Controller\Panel\Page\Design\Customize::apply_to_page'),
        "/ccm/system/panels/page/design/customize/apply_to_site/{pThemeID}"             => array('\Concrete\Controller\Panel\Page\Design\Customize::apply_to_site'),
        "/ccm/system/panels/page/design/customize/preview/{pThemeID}"                   => array('\Concrete\Controller\Panel\Page\Design\Customize::preview'),
        "/ccm/system/panels/page/design/customize/reset_page_customizations"            => array('\Concrete\Controller\Panel\Page\Design\Customize::reset_page_customizations'),
        "/ccm/system/panels/page/design/customize/reset_site_customizations/{pThemeID}" => array('\Concrete\Controller\Panel\Page\Design\Customize::reset_site_customizations'),
        "/ccm/system/panels/page/design/preview_contents"                               => array('\Concrete\Controller\Panel\Page\Design::preview_contents'),
        "/ccm/system/panels/page/design/submit"                                         => array('\Concrete\Controller\Panel\Page\Design::submit'),
        "/ccm/system/panels/page/preview_as_user"                                       => array('\Concrete\Controller\Panel\Page\PreviewAsUser::view'),
        "/ccm/system/panels/page/preview_as_user/preview"                               => array('\Concrete\Controller\Panel\Page\PreviewAsUser::frame_page'),
        "/ccm/system/panels/page/preview_as_user/render"                                => array('\Concrete\Controller\Panel\Page\PreviewAsUser::preview_page'),
        "/ccm/system/panels/page/versions"                                              => array('\Concrete\Controller\Panel\Page\Versions::view'),
        "/ccm/system/panels/page/versions/get_json"                                     => array('\Concrete\Controller\Panel\Page\Versions::get_json'),
        "/ccm/system/panels/page/versions/duplicate"                                    => array('\Concrete\Controller\Panel\Page\Versions::duplicate'),
        "/ccm/system/panels/page/versions/new_page"                                     => array('\Concrete\Controller\Panel\Page\Versions::new_page'),
        "/ccm/system/panels/page/versions/delete"                                       => array('\Concrete\Controller\Panel\Page\Versions::delete'),
        "/ccm/system/panels/page/versions/approve"                                      => array('\Concrete\Controller\Panel\Page\Versions::approve'),
        "/ccm/system/panels/sitemap"                                                    => array('\Concrete\Controller\Panel\Sitemap::view'),

        /**
         * Panel Details
         */
        "/ccm/system/panels/details/page/attributes"                                    => array('\Concrete\Controller\Panel\Detail\Page\Attributes::view'),
        "/ccm/system/panels/details/page/attributes/add_attribute"                      => array('\Concrete\Controller\Panel\Detail\Page\Attributes::add_attribute'),
        "/ccm/system/panels/details/page/attributes/submit"                             => array('\Concrete\Controller\Panel\Detail\Page\Attributes::submit'),
        "/ccm/system/panels/details/page/caching"                                       => array('\Concrete\Controller\Panel\Detail\Page\Caching::view'),
        "/ccm/system/panels/details/page/caching/purge"                                 => array('\Concrete\Controller\Panel\Detail\Page\Caching::purge'),
        "/ccm/system/panels/details/page/caching/submit"                                => array('\Concrete\Controller\Panel\Detail\Page\Caching::submit'),
        "/ccm/system/panels/details/page/composer"                                      => array('\Concrete\Controller\Panel\Detail\Page\Composer::view'),
        "/ccm/system/panels/details/page/composer/autosave"                             => array('\Concrete\Controller\Panel\Detail\Page\Composer::autosave'),
        "/ccm/system/panels/details/page/composer/discard"                              => array('\Concrete\Controller\Panel\Detail\Page\Composer::discard'),
        "/ccm/system/panels/details/page/composer/publish"                              => array('\Concrete\Controller\Panel\Detail\Page\Composer::publish'),
        "/ccm/system/panels/details/page/composer/save_and_exit"                        => array('\Concrete\Controller\Panel\Detail\Page\Composer::saveAndExit'),
        "/ccm/system/panels/details/page/location"                                      => array('\Concrete\Controller\Panel\Detail\Page\Location::view'),
        "/ccm/system/panels/details/page/location/submit"                               => array('\Concrete\Controller\Panel\Detail\Page\Location::submit'),
        "/ccm/system/panels/details/page/permissions"                                   => array('\Concrete\Controller\Panel\Detail\Page\Permissions::view'),
        "/ccm/system/panels/details/page/permissions/save_simple"                       => array('\Concrete\Controller\Panel\Detail\Page\Permissions::save_simple'),
        "/ccm/system/panels/details/page/preview"                                       => array('\Concrete\Controller\Panel\Page\Design::preview'),
        "/ccm/system/panels/details/page/seo"                                           => array('\Concrete\Controller\Panel\Detail\Page\Seo::view'),
        "/ccm/system/panels/details/page/seo/submit"                                    => array('\Concrete\Controller\Panel\Detail\Page\Seo::submit'),
        "/ccm/system/panels/details/page/versions"                                      => array('\Concrete\Controller\Panel\Detail\Page\Versions::view'),

        /**
         * RSS Feeds
         */
        "/rss/{identifier}"                                                             => array(
            '\Concrete\Controller\Feed::get',
            'rss',
            array('identifier' => '[A-Za-z0-9_/.]+')),

        /**
         * Special Dashboard
         */
        "/dashboard/blocks/stacks/list"                                                 => array('\Concrete\Controller\SinglePage\Dashboard\Blocks\Stacks::list_page'),

        /**
         * Tools - legacy
         */
        "/tools/blocks/{btHandle}/{tool}"                                               => array(
            '\Concrete\Core\Legacy\Controller\ToolController::displayBlock',
            'blockTool',
            array('tool' => '[A-Za-z0-9_/.]+')),
        "/tools/{tool}"                                                                 => array(
            '\Concrete\Core\Legacy\Controller\ToolController::display',
            'tool',
            array('tool' => '[A-Za-z0-9_/.]+')),

        /*
         * Assets localization
         */
        '/ccm/assets/localization/core/js'                                              => array('\Concrete\Controller\Frontend\AssetsLocalization::getCoreJavascript'),
        '/ccm/assets/localization/select2/js'                                           => array('\Concrete\Controller\Frontend\AssetsLocalization::getSelect2Javascript'),
        '/ccm/assets/localization/redactor/js'                                          => array('\Concrete\Controller\Frontend\AssetsLocalization::getRedactorJavascript'),
        '/ccm/assets/localization/dynatree/js'                                          => array('\Concrete\Controller\Frontend\AssetsLocalization::getDynatreeJavascript'),
        '/ccm/assets/localization/imageeditor/js'                                       => array('\Concrete\Controller\Frontend\AssetsLocalization::getImageEditorJavascript'),
        '/ccm/assets/localization/jquery/ui/js'                                         => array('\Concrete\Controller\Frontend\AssetsLocalization::getJQueryUIJavascript'),
        '/ccm/assets/localization/translator/js'                                        => array('\Concrete\Controller\Frontend\AssetsLocalization::getTranslatorJavascript'),
        '/ccm/assets/localization/dropzone/js'                                          => array('\Concrete\Controller\Frontend\AssetsLocalization::getDropzoneJavascript'),
        '/ccm/assets/localization/conversations/js'                                     => array('\Concrete\Controller\Frontend\AssetsLocalization::getConversationsJavascript'),
    ),

    /**
     * Route themes
     */
    'theme_paths'         => array(
        '/dashboard'        => 'dashboard',
        '/dashboard/*'      => 'dashboard',
        '/account'          => VIEW_CORE_THEME,
        '/account/*'        => VIEW_CORE_THEME,
        '/install'          => VIEW_CORE_THEME,
        '/login'            => VIEW_CORE_THEME,
        '/register'         => VIEW_CORE_THEME,
        '/maintenance_mode' => VIEW_CORE_THEME,
        '/upgrade'          => VIEW_CORE_THEME
    ),

    /**
     * File Types
     */
    'file_types'          => array(
        t('JPEG')           => array('jpg,jpeg,jpe', FileType::T_IMAGE, 'image', 'image', 'image'),
        t('GIF')            => array('gif', FileType::T_IMAGE, 'image', 'image', 'image'),
        t('PNG')            => array('png', FileType::T_IMAGE, 'image', 'image', 'image'),
        t('Windows Bitmap') => array('bmp', FileType::T_IMAGE, 'image'),
        t('TIFF')           => array('tif,tiff', FileType::T_IMAGE, 'image'),
        t('HTML')           => array('htm,html', FileType::T_IMAGE),
        t('Flash')          => array('swf', FileType::T_IMAGE, 'image'),
        t('Icon')           => array('ico', FileType::T_IMAGE),
        t('SVG')            => array('svg', FileType::T_IMAGE),
        t('Windows Video')  => array('asf,wmv', FileType::T_VIDEO, false, 'video'),
        t('Quicktime')      => array('mov,qt', FileType::T_VIDEO, false, 'video'),
        t('AVI')            => array('avi', FileType::T_VIDEO, false, 'video'),
        t('3GP')            => array('3gp', FileType::T_VIDEO, false, 'video'),
        t('Plain Text')     => array('txt', FileType::T_TEXT, false, 'text'),
        t('CSV')            => array('csv', FileType::T_TEXT, false, 'text'),
        t('XML')            => array('xml', FileType::T_TEXT),
        t('PHP')            => array('php', FileType::T_TEXT),
        t('MS Word')        => array('doc,docx', FileType::T_DOCUMENT),
        t('Stylesheet')     => array('css', FileType::T_TEXT),
        t('MP4')            => array('mp4', FileType::T_VIDEO),
        t('FLV')            => array('flv', FileType::T_VIDEO, 'flv'),
        t('MP3')            => array('mp3', FileType::T_AUDIO, false, 'audio'),
        t('MP4 Audio')            => array('m4a', FileType::T_AUDIO, false, 'audio'),
        t('Realaudio')      => array('ra,ram', FileType::T_AUDIO),
        t('Windows Audio')  => array('wma', FileType::T_AUDIO),
        t('Rich Text')      => array('rtf', FileType::T_DOCUMENT),
        t('JavaScript')     => array('js', FileType::T_TEXT),
        t('PDF')            => array('pdf', FileType::T_DOCUMENT),
        t('Photoshop')      => array('psd', FileType::T_IMAGE),
        t('MPEG')           => array('mpeg,mpg', FileType::T_VIDEO),
        t('MS Excel')       => array('xla,xls,xlsx,xlt,xlw', FileType::T_DOCUMENT),
        t('MS Powerpoint')  => array('pps,ppt,pptx,pot', FileType::T_DOCUMENT),
        t('TAR Archive')    => array('tar', FileType::T_APPLICATION),
        t('Zip Archive')    => array('zip', FileType::T_APPLICATION),
        t('GZip Archive')   => array('gz,gzip', FileType::T_APPLICATION),
        t('OGG')            => array('ogg', FileType::T_AUDIO),
        t('OGG Video')      => array('ogv', FileType::T_VIDEO),
        t('WebM')           => array('webm', FileType::T_VIDEO)
    ),

    /**
     * Importer Attributes
     */
    'importer_attributes' => array(
        'width'    => array('Width', 'NUMBER', false),
        'height'   => array('Height', 'NUMBER', false),
        'duration' => array('Duration', 'NUMBER', false)
    ),

    /**
     * Assets
     */
    'assets'              => array(

        'jquery'                   => array(
            array(
                'javascript',
                'js/jquery.js',
                array('position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false)
            )
        ),
        'jquery/ui'                => array(
            array('javascript', 'js/jquery-ui.js', array('minify' => false, 'combine' => false)),
            array('javascript-localized', '/ccm/assets/localization/jquery/ui/js'),
            array('css', 'css/jquery-ui.css', array('minify' => false))
        ),
        'jquery/visualize'                => array(
            array('javascript', 'js/jquery-visualize.js', array('minify' => false, 'combine' => false)),
            array('css', 'css/jquery-visualize.css', array('minify' => false))
        ),
        'jquery/touch-punch'              => array(
            array('javascript', 'js/jquery-ui-touch-punch.js')
        ),
        'jquery/tristate'              => array(
            array('javascript', 'js/jquery-tristate.js')
        ),
        'select2'                  => array(
            array('javascript', 'js/select2.js', array('minify' => false, 'combine' => false)),
            array('javascript-localized', '/ccm/assets/localization/select2/js'),
            array('css', 'css/select2.css', array('minify' => false))
        ),
        'underscore'               => array(
            array('javascript', 'js/underscore.js', array('minify' => false))
        ),
        'backbone'               => array(
            array('javascript', 'js/backbone.js', array('minify' => false))
        ),
        'dropzone'                 => array(
            array('javascript', 'js/dropzone.js'),
            array('javascript-localized', '/ccm/assets/localization/dropzone/js'),
        ),
        'jquery/form'              => array(
            array('javascript', 'js/jquery-form.js')
        ),
        'picturefill'              => array(
            array('javascript', 'js/picturefill.js', array('minify' => false))
        ),
        'html5-shiv'              => array(
            array('javascript-conditional', 'js/ie/html5-shiv.js',
                array('conditional' => 'lt IE 9')
            )
        ),
        'respond'                 => array(
            array('javascript-conditional', 'js/ie/respond.js',
                array('conditional' => 'lt IE 9')
            )
        ),
        'spectrum'                 => array(
            array('javascript', 'js/spectrum.js', array('minify' => false)),
            array('css', 'css/spectrum.css', array('minify' => false))
        ),
        'font-awesome'             => array(
            array('css', 'css/font-awesome.css', array('minify' => false))
        ),
        'core/events'              => array(
            array('javascript', 'js/events.js', array('minify' => false))
        ),
        'core/style-customizer'    => array(
            array('javascript', 'js/style-customizer.js', array('minify' => false)),
            array('css', 'css/style-customizer.css', array('minify' => false))
        ),
        'core/localization' => array(
            array('javascript-localized', '/ccm/assets/localization/core/js'),
        ),
        'jquery/awesome-rating'                 => array(
            array('javascript', 'js/jquery-awesome-rating.js', array('minify' => false)),
            array('css', 'css/jquery-awesome-rating.css', array('minify' => false))
        ),
        'jquery/fileupload'        => array(
            array('javascript', 'js/jquery-fileupload.js')
        ),
        'swfobject'                => array(
            array('javascript', 'js/swfobject.js')
        ),
        'redactor'                 => array(
            array('javascript', 'js/redactor.js', array('minify' => false)),
            array('javascript-localized', '/ccm/assets/localization/redactor/js'),
            array('css', 'css/redactor.css')
        ),
        'ace'                      => array(
            array('javascript', 'js/ace/ace.js', array('minify' => false))
        ),
        'backstretch'              => array(
            array('javascript', 'js/backstretch.js')
        ),
        'dynatree'                 => array(
            array('javascript', 'js/dynatree.js', array('minify' => false)),
            array('javascript-localized', '/ccm/assets/localization/dynatree/js', array('minify' => false)),
            array('css', 'css/dynatree.css', array('minify' => false))
        ),
        'bootstrap/dropdown'       => array(
            array('javascript', 'js/bootstrap/dropdown.js'),
            array('css', 'css/app.css', array('minify' => false))
        ),
        'bootstrap/tooltip'        => array(
            array('javascript', 'js/bootstrap/tooltip.js'),
            array('css', 'css/app.css', array('minify' => false))
        ),
        'bootstrap/popover'        => array(
            array('javascript', 'js/bootstrap/popover.js'),
            array('css', 'css/app.css', array('minify' => false))
        ),
        'bootstrap/alert'          => array(
            array('javascript', 'js/bootstrap/alert.js'),
            array('css', 'css/app.css', array('minify' => false))
        ),
        'bootstrap/button'         => array(
            array('javascript', 'js/bootstrap/button.js'),
            array('css', 'css/app.css', array('minify' => false))
        ),
        'bootstrap/transition'     => array(
            array('javascript', 'js/bootstrap/transition.js'),
            array('css', 'css/app.css', array('minify' => false))
        ),
        'bootstrap'                => array(
            array('css', 'css/app.css', array('minify' => false))
        ),
        'core/app'                 => array(
            array('javascript', 'js/app.js', array('minify' => false, 'combine' => false)),
            array('css', 'css/app.css', array('minify' => false))
        ),
        'bootstrap-editable'       => array(
            array('javascript', 'js/bootstrap-editable.js', array('minify' => false)),
        ),
        'core/app/editable-fields' => array(
            array('css', 'css/editable-fields.css', array('minify' => false))
        ),
        'kinetic'                  => array(
            array('javascript', 'js/kinetic.js')
        ),
        'core/imageeditor'         => array(
            array('javascript', 'js/image-editor.js'),
            array('javascript-localized', '/ccm/assets/localization/imageeditor/js'),
            array('css', 'css/image-editor.css')
        ),
        'dashboard'                => array(
            array('javascript', 'js/dashboard.js')
        ),
        'core/frontend/captcha'    => array(
            array('css', 'css/frontend/captcha.css')
        ),
        'core/frontend/pagination' => array(
            array('css', 'css/frontend/pagination.css')
        ),
        'core/frontend/errors'     => array(
            array('css', 'css/frontend/errors.css')
        ),
        'core/file-manager'        => array(
            array('javascript', 'js/file-manager.js', array('minify' => false)),
            array('css', 'css/file-manager.css', array('minify' => false))
        ),
        'core/sitemap'             => array(
            array('javascript', 'js/sitemap.js', array('minify' => false)),
            array('css', 'css/sitemap.css', array('minify' => false))
        ),
        'core/topics'              => array(
            array('javascript', 'js/topics.js', array('minify' => false)),
            array('css', 'css/topics.css', array('minify' => false))
        ),
        'core/groups'              => array(
            array('javascript', 'js/groups.js', array('minify' => false))
        ),
        'core/gathering'           => array(
            array('javascript', 'js/gathering.js')
        ),
        'core/gathering/display'   => array(
            array('css', 'css/gathering/display.css'),
        ),
        'core/gathering/base'      => array(
            array('css', 'css/gathering/base.css'),
        ),
        'core/conversation'        => array(
            array('javascript', 'js/conversations.js'),
            array('javascript-localized', '/ccm/assets/localization/conversations/js'),
            array('css', 'css/conversations.css')
        ),
        'core/lightbox'            => array(
            array('javascript', 'js/jquery-magnific-popup.js'),
            array('css', 'css/jquery-magnific-popup.css')
        ),
        'core/lightbox/launcher'   => array(
            array('javascript', 'js/lightbox.js')
        ),
        'core/account'             => array(
            array('javascript', 'js/account.js'),
            array('css', 'css/account.css')
        ),
        'core/translator' => array(
            array('javascript', 'js/translator.js', array('minify' => false)),
            array('javascript-localized', '/ccm/assets/localization/translator/js'),
            array('css', 'css/translator.css', array('minify' => false))
        )
    ),
    'asset_groups'        => array(

        'jquery/ui'                => array(
            array(
                array('javascript', 'jquery/ui'),
                array('javascript-localized', 'jquery/ui'),
                array('css', 'jquery/ui'),
            )
        ),
        'jquery/visualize'                => array(
            array(
                array('javascript', 'jquery/visualize'),
                array('css', 'jquery/visualize'),
            )
        ),
        'select2'                  => array(
            array(
                array('javascript', 'select2'),
                array('javascript-localized', 'select2'),
                array('css', 'select2')
            )
        ),
        'ace'         => array(
            array(
                array('javascript', 'ace')
            )
        ),
        'core/colorpicker'         => array(
            array(
                array('javascript', 'jquery'),
                array('javascript', 'core/events'),
                array('javascript-localized', 'core/localization'),
                array('javascript', 'spectrum'),
                array('css', 'spectrum')
            )
        ),
        'font-awesome'             => array(
            array(
                array('css', 'font-awesome')
            )
        ),
        'core/rating'            => array(
            array(
                array('javascript', 'jquery'),
                array('javascript', 'jquery/awesome-rating'),
                array('css', 'font-awesome'),
                array('css', 'jquery/awesome-rating')
            )
        ),
        'core/style-customizer'    => array(
            array(
                array('javascript', 'jquery'),
                array('javascript', 'jquery/ui'),
                array('javascript-localized', 'jquery/ui'),
                array('javascript', 'core/events'),
                array('javascript', 'underscore'),
                array('javascript', 'backbone'),
                array('javascript', 'core/colorpicker'),
                array('javascript-localized', 'core/localization'),
                array('javascript', 'core/app'),
                array('javascript', 'jquery/fileupload'),
                array('javascript', 'core/file-manager'),
                array('javascript', 'core/style-customizer'),
                array('css', 'core/app'),
                array('css', 'core/file-manager'),
                array('css', 'jquery/ui'),
                array('css', 'core/colorpicker'),
                array('css', 'core/style-customizer')
            )
        ),
        'jquery/fileupload'        => array(
            array(
                array('javascript', 'jquery/fileupload')
            )
        ),
        'swfobject'                => array(
            array(
                array('javascript', 'swfobject')
            )
        ),
        'redactor'                 => array(
            array(
                array('javascript', 'redactor'),
                array('javascript-localized', 'redactor'),
                array('css', 'redactor'),
                array('css', 'font-awesome')
            )
        ),
        'dynatree'                 => array(
            array(
                array('javascript', 'dynatree'),
                array('javascript-localized', 'dynatree'),
                array('css', 'dynatree')
            )
        ),
        'core/app'                 => array(
            array(
                array('javascript', 'jquery'),
                array('javascript', 'core/events'),
                array('javascript', 'underscore'),
                array('javascript', 'backbone'),
                array('javascript', 'bootstrap/dropdown'),
                array('javascript', 'bootstrap/tooltip'),
                array('javascript', 'bootstrap/popover'),
                array('javascript', 'jquery/ui'),
                array('javascript-localized', 'jquery/ui'),
                array('javascript-localized', 'core/localization'),
                array('javascript', 'core/app'),
                array('css', 'core/app'),
                array('css', 'font-awesome'),
                array('css', 'jquery/ui')
            )
        ),
        'core/app/editable-fields' => array(
            array(
                array('javascript', 'jquery'),
                array('javascript', 'bootstrap/dropdown'),
                array('javascript', 'bootstrap/tooltip'),
                array('javascript', 'bootstrap/popover'),
                array('javascript', 'jquery/ui'),
                array('javascript-localized', 'jquery/ui'),
                array('javascript', 'core/events'),
                array('javascript', 'underscore'),
                array('javascript', 'backbone'),
                array('javascript-localized', 'core/localization'),
                array('javascript', 'core/app'),
                array('javascript', 'bootstrap-editable'),
                array('css', 'core/app/editable-fields'),
                array('javascript', 'jquery/fileupload')
            )
        ),
        'core/imageeditor'         => array(
            array(
                array('javascript', 'kinetic'),
                array('javascript-localized', 'core/imageeditor'),
                array('javascript', 'core/imageeditor'),
                array('css', 'core/imageeditor')
            )
        ),
        'dashboard'                => array(
            array(
                array('javascript', 'jquery'),
                array('javascript', 'jquery/ui'),
                array('javascript-localized', 'jquery/ui'),
                array('javascript', 'jquery/touch-punch'),
                array('javascript', 'underscore'),
                array('javascript', 'backbone'),
                array('javascript', 'dashboard'),
                array('javascript', 'core/events'),
                array('javascript', 'bootstrap/dropdown'),
                array('javascript', 'bootstrap/tooltip'),
                array('javascript', 'bootstrap/popover'),
                array('javascript', 'bootstrap/transition'),
                array('javascript', 'bootstrap/alert'),
                array('javascript-localized', 'core/localization'),
                array('javascript', 'core/app'),
                array('javascript', 'redactor'),
                array('javascript-localized', 'redactor'),
                array('javascript-conditional', 'respond'),
                array('javascript-conditional', 'html5-shiv'),
                array('css', 'core/app'),
                array('css', 'redactor'),
                array('css', 'jquery/ui'),
                array('css', 'font-awesome')
            )
        ),
        'core/file-manager'        => array(
            array(
                array('css', 'core/app'),
                array('css', 'jquery/ui'),
                array('css', 'core/file-manager'),
                array('css', 'select2'),
                array('javascript', 'core/events'),
                array('javascript', 'bootstrap/tooltip'),
                array('javascript', 'underscore'),
                array('javascript', 'backbone'),
                array('javascript', 'jquery/ui'),
                array('javascript-localized', 'jquery/ui'),
                array('javascript', 'select2'),
                array('javascript-localized', 'select2'),
                array('javascript-localized', 'core/localization'),
                array('javascript', 'core/app'),
                array('javascript', 'jquery/fileupload'),
                array('javascript', 'core/file-manager')
            )
        ),
        'core/sitemap'             => array(
            array(
                array('javascript', 'core/events'),
                array('javascript', 'underscore'),
                array('javascript', 'backbone'),
                array('javascript', 'jquery/ui'),
                array('javascript-localized', 'jquery/ui'),
                array('javascript', 'dynatree'),
                array('javascript-localized', 'dynatree'),
                array('javascript-localized', 'core/localization'),
                array('javascript', 'core/app'),
                array('javascript', 'core/sitemap'),
                array('css', 'dynatree'),
                array('css', 'core/sitemap')
            )
        ),
        'core/topics'              => array(
            array(
                array('javascript', 'core/events'),
                array('javascript', 'jquery/ui'),
                array('javascript-localized', 'jquery/ui'),
                array('javascript', 'dynatree'),
                array('javascript-localized', 'dynatree'),
                array('javascript', 'core/topics'),
                array('css', 'dynatree'),
                array('css', 'core/topics')
            )
        ),
        'core/groups'              => array(
            array(
                array('javascript', 'core/events'),
                array('javascript', 'underscore'),
                array('javascript', 'jquery/ui'),
                array('javascript-localized', 'jquery/ui'),
                array('javascript', 'dynatree'),
                array('javascript-localized', 'dynatree'),
                array('javascript', 'core/groups'),
                array('css', 'dynatree')
            )
        ),
        'core/gathering'           => array(
            array(
                array('javascript', 'core/gathering'),
                array('javascript', 'redactor'),
                array('javascript-localized', 'redactor'),
                array('css', 'core/gathering/base'),
                array('css', 'core/conversation'),
                array('css', 'core/gathering/display'),
                array('css', 'redactor')
            )
        ),
        'core/conversation'        => array(
            array(
                array('javascript', 'jquery'),
                array('javascript', 'jquery/ui'),
                array('javascript-localized', 'jquery/ui'),
                array('javascript', 'underscore'),
                array('javascript', 'core/lightbox'),
                array('javascript', 'dropzone'),
                array('javascript-localized', 'dropzone'),
                array('javascript', 'bootstrap/dropdown'),
                array('javascript', 'core/events'),
                array('javascript', 'core/conversation'),
                array('javascript-localized', 'core/conversation'),
                array('css', 'core/conversation'),
                array('css', 'core/frontend/errors'),
                array('css', 'font-awesome'),
                array('css', 'bootstrap/dropdown'),
                array('css', 'core/lightbox'),
                array('css', 'jquery/ui')
            ),
            true
        ),
        'core/lightbox'            => array(
            array(
                array('javascript', 'jquery'),
                array('javascript', 'core/lightbox'),
                array('javascript', 'core/lightbox/launcher'),
                array('css', 'core/lightbox')
            )
        ),
        'core/account'             => array(
            array(
                array('javascript', 'core/account'),
                array('javascript', 'bootstrap/dropdown'),
                array('css', 'bootstrap/dropdown'),
                array('css', 'core/account')
            )
        ),
        'core/translator'          => array(
            array(
                array('javascript', 'core/translator'),
                array('javascript-localized', 'core/translator'),
                array('css', 'core/translator'),
            )
        ),
        /** @deprecated keeping this around because certain themes reference it and we don't want to break them. */
        'core/legacy'              => array(
            array(
            )
        )
    ),
    'curl' => array(
        'verifyPeer' => true
    )
);
