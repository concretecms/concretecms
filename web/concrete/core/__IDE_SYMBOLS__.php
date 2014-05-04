<?php
namespace {
    die('Intended for use with IDE symbol matching only.');

    /**
     * An object that represents a particular request to the Concrete-powered website. The request object then determines what is being requested, based on the path, and presents itself to the rest of the dispatcher (which loads the page, etc...)
     * @package Core
     * @author Andrew Embler <andrew@concrete5.org>
     * @category Concrete
     * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
     * @license    http://www.concrete5.org/license/     MIT License
     *
     */
    class Request extends \Concrete\Core\Http\Request {}

    /**
     * Useful functions for getting paths for concrete5 items.
     * @package Core
     * @author Andrew Embler <andrew@concrete5.org>
     * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
     * @license    http://www.concrete5.org/license/     MIT License
     */
    class Environment extends \Concrete\Core\Foundation\Environment {}

    class Localization extends \Concrete\Core\Localization\Localization {}

    class Events extends \Concrete\Core\Support\Facade\Events {}

    class Response extends \Concrete\Core\Http\Response {}

    class Redirect extends \Concrete\Core\Routing\Redirect {}

    class Log extends \Concrete\Core\Logging\Log {}

    class URL extends \Concrete\Core\Routing\URL {}

    class Cookie extends \Concrete\Core\Cookie\Cookie {}

    class Cache extends \Concrete\Core\Cache\Cache {}

    class CacheLocal extends \Concrete\Core\Cache\CacheLocal {}

    class CollectionAttributeKey extends \Concrete\Core\Attribute\Key\CollectionKey {}

    class FileAttributeKey extends \Concrete\Core\Attribute\Key\FileKey {}

    class UserAttributeKey extends \Concrete\Core\Attribute\Key\UserKey {}

    class AttributeSet extends \Concrete\Core\Attribute\Set {}

    class AssetList extends \Concrete\Core\Asset\AssetList {}

    class Router extends \Concrete\Core\Routing\Router {}

    class RedirectResponse extends \Concrete\Core\Routing\RedirectResponse {}

    /**
     *
     * The page object in Concrete encapsulates all the functionality used by a typical page and their contents
     * including blocks, page metadata, page permissions.
     * @package Pages
     *
     */
    class Page extends \Concrete\Core\Page\Page {}

    class PageEditResponse extends \Concrete\Core\Page\EditResponse {}

    class Controller extends \Concrete\Core\Controller\Controller {}

    class PageController extends \Concrete\Core\Page\Controller\PageController {}

    /**
     *
     * SinglePage extends the page class for those instances of pages that have no type, and are special "single pages"
     * within the system.
     * @package Pages
     *
     */
    class SinglePage extends \Concrete\Core\Page\Single {}

    class Config extends \Concrete\Core\Config\Config {}

    class PageType extends \Concrete\Core\Page\Type\Type {}

    class PageTemplate extends \Concrete\Core\Page\Template {}

    /**
     *
     * A page's theme is a pointer to a directory containing templates, CSS files and optionally PHP includes, images and JavaScript files.
     * Themes inherit down the tree when a page is added, but can also be set at the site-wide level (thereby overriding any previous choices.)
     * @package Pages and Collections
     * @subpackages Themes
     */
    class PageTheme extends \Concrete\Core\Page\Theme\Theme {}

    /**
     *
     * An object that allows a filtered list of pages to be returned.
     * @package Pages
     *
     */
    class PageList extends \Concrete\Core\Page\PageList {}

    class PageCache extends \Concrete\Core\Cache\Page\PageCache {}

    class Conversation extends \Concrete\Core\Conversation\Conversation {}

    class ConversationFlagType extends \Concrete\Core\Conversation\FlagType\FlagType {}

    class Queue extends \Concrete\Core\Foundation\Queue {}

    class Block extends \Concrete\Core\Block\Block {}

    class Marketplace extends \Concrete\Core\Marketplace\Marketplace {}

    class BlockType extends \Concrete\Core\Block\BlockType\BlockType {}

    class BlockTypeList extends \Concrete\Core\Block\BlockType\BlockTypeList {}

    class BlockTypeSet extends \Concrete\Core\Block\BlockType\Set {}

    class Package extends \Concrete\Core\Package\Package {}

    class Collection extends \Concrete\Core\Page\Collection\Collection {}

    class CollectionVersion extends \Concrete\Core\Page\Collection\Version\Version {}

    class Area extends \Concrete\Core\Area\Area {}

    class GlobalArea extends \Concrete\Core\Area\GlobalArea {}

    class Stack extends \Concrete\Core\Page\Stack\Stack {}

    class StackList extends \Concrete\Core\Page\Stack\StackList {}

    class View extends \Concrete\Core\View\View {}

    class Job extends \Concrete\Core\Job\Job {}

    /**
     * @package Workflow
     * @author Andrew Embler <andrew@concrete5.org>
     * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
     * @license    http://www.concrete5.org/license/     MIT License
     *
     */
    class Workflow extends \Concrete\Core\Workflow\Workflow {}

    class JobSet extends \Concrete\Core\Job\Set {}

    class File extends \Concrete\Core\File\File {}

    class FileVersion extends \Concrete\Core\File\Version {}

    class FileSet extends \Concrete\Core\File\Set\Set {}

    class FileImporter extends \Concrete\Core\File\Importer {}

    class Group extends \Concrete\Core\User\Group\Group {}

    class GroupSet extends \Concrete\Core\User\Group\GroupSet {}

    class GroupSetList extends \Concrete\Core\User\Group\GroupSetList {}

    class GroupList extends \Concrete\Core\User\Group\GroupList {}

    /**
     *
     * An object that allows a filtered list of files to be returned.
     * @package Files
     *
     */
    class FileList extends \Concrete\Core\File\FileList {}

    /**
     *
     * The job class is essentially sub-dispatcher for certain maintenance tasks that need to be run at specified intervals. Examples include indexing a search engine or generating a sitemap page.
     * @package Utilities
     * @author Andrew Embler <andrew@concrete5.org>
     * @author Tony Trupp <tony@concrete5.org>
     * @link http://www.concrete5.org
     * @license http://www.opensource.org/licenses/mit-license.php MIT
     *
     */
    class QueueableJob extends \Concrete\Core\Job\QueueableJob {}

    class Permissions extends \Concrete\Core\Permission\Checker {}

    class PermissionKey extends \Concrete\Core\Permission\Key\Key {}

    class PermissionKeyCategory extends \Concrete\Core\Permission\Category {}

    class PermissionAccess extends \Concrete\Core\Permission\Access\Access {}

    class User extends \Concrete\Core\User\User {}

    class UserInfo extends \Concrete\Core\User\UserInfo {}

    /**
     * An object that allows a filtered list of users to be returned.
     * @package Files
     * @author Tony Trupp <tony@concrete5.org>
     * @category Concrete
     * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
     * @license    http://www.concrete5.org/license/     MIT License
     *
     */
    class UserList extends \Concrete\Core\User\UserList {}

    class StartingPointPackage extends \Concrete\Core\Package\StartingPointPackage {}

    class AuthenticationType extends \Concrete\Core\Authentication\AuthenticationType {}

    class GroupTree extends \Concrete\Core\Tree\Type\Group {}

    class GroupTreeNode extends \Concrete\Core\Tree\Node\Type\Group {}

    class Zend_Queue_Adapter_Concrete5 extends \Concrete\Core\Utility\ZendQueueAdapter {}

    /**
     * @deprecated
     */
    class Loader extends \Concrete\Core\Legacy\Loader {}

    /**
     * @deprecated
     */
    class TaskPermission extends \Concrete\Core\Legacy\TaskPermission {}

    /**
     * @deprecated
     */
    class FilePermissions extends \Concrete\Core\Legacy\FilePermissions {}

    class Core extends \Concrete\Core\Support\Facade\Application {}

    class Session extends \Concrete\Core\Support\Facade\Session {}

    class Database extends \Concrete\Core\Support\Facade\Database {}

    class Route extends \Concrete\Core\Routing\Router {}
}
