<?php
namespace Concrete\Core\Authentication;

use Concrete\Authentication\Concrete\Controller;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Database\Schema\Schema;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Filesystem\TemplateService;
use Concrete\Core\Filesystem\TemplateVariantLocator;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Support\Facade\Application;
use Core;
use Environment;
use Exception;
use Loader;
use Package;

class AuthenticationType extends ConcreteObject
{
    /** @var Controller */
    public $controller;
    protected $authTypeID;
    protected $authTypeName;
    protected $authTypeHandle;
    protected $authTypeDisplayOrder;
    protected $authTypeIsEnabled;
    protected $pkgID;

    /** @var FileLocator */
    protected $locator;

    /** @var TemplateService */
    protected $templateService;

    protected $addedPackageLocator = false;

    public function __construct(FileLocator $locator, TemplateService $templateService)
    {
        $this->locator = $locator;
        $this->templateService = $templateService;
    }

    public static function getListSorted()
    {
        return self::getList(true);
    }

    /**
     * Return a raw list of authentication types.
     *
     * @param bool $sorted true: Sort by display order, false: sort by install order
     * @param bool $activeOnly true: include only active types, false: include active and inactive types
     *
     * @return AuthenticationType[]
     */
    public static function getList($sorted = false, $activeOnly = false)
    {
        $list = [];
        $db = Loader::db();
        $q = $db->executeQuery('SELECT * FROM AuthenticationTypes'
            . ($activeOnly ? ' WHERE authTypeIsEnabled=1 ' : '')
            . ' ORDER BY ' . ($sorted ? 'authTypeDisplayOrder' : 'authTypeID'));
        while ($row = $q->fetch()) {
            $list[] = self::load($row);
        }

        return $list;
    }

    /**
     * Load an AuthenticationType from an array.
     *
     * @param array $arr should be an array of the following key/value pairs to create an object from:
     * <pre>
     * array(
     *     'authTypeID' => int,
     *     'authTypeHandle' => string,
     *     'authTypeName' => string,
     *     'authTypeDisplayOrder' => int,
     *     'authTypeIsEnabled' => tinyint,
     *     'pkgID' => int
     * )
     * </pre>
     *
     * @return bool|\Concrete\Core\Authentication\AuthenticationType
     */
    public static function load($arr)
    {
        $extract = [
            'authTypeID',
            'authTypeName',
            'authTypeHandle',
            'authTypeDisplayOrder',
            'authTypeIsEnabled',
            'pkgID',
        ];
        $obj = Application::make(self::class);
        foreach ($extract as $key) {
            if (!isset($arr[$key])) {
                return false;
            }
            $obj->{$key} = $arr[$key];
        }
        $obj->loadController();

        return $obj;
    }

    /**
     * Load the AuthenticationTypeController into the AuthenticationType.
     */
    protected function loadController()
    {
        $env = Environment::get();
        $r = $env->getRecord(DIRNAME_AUTHENTICATION . '/' . $this->authTypeHandle . '/' . FILENAME_CONTROLLER);
        $prefix = $r->override ? true : $this->getPackageHandle();
        $authTypeHandle = Core::make('helper/text')->camelcase($this->authTypeHandle);
        $class = core_class('Authentication\\' . $authTypeHandle . '\\Controller', $prefix);
        $this->controller = Core::make($class, ['type' => $this]);
    }

    /**
     * AuthenticationType::getPackageHandle
     * Return the package handle.
     */
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    /**
     * Return an array of AuthenticationTypes that are associated with a specific package.
     *
     * @param Package $pkg
     *
     * @return AuthenticationType[]
     */
    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $list = [];

        $q = $db->executeQuery('SELECT * FROM AuthenticationTypes WHERE pkgID=?', [$pkg->getPackageID()]);
        while ($row = $q->fetch()) {
            $list[] = self::load($row);
        }

        return $list;
    }

    /**
     * @param string $atHandle New AuthenticationType handle
     * @param string $atName New AuthenticationType name, expect this to be presented with "%s Authentication Type"
     * @param int $order Order int, used to order the display of AuthenticationTypes
     * @param bool|\Package $pkg package object to which this AuthenticationType is associated
     *
     * @throws \Exception
     *
     * @return AuthenticationType returns a loaded authentication type
     */
    public static function add($atHandle, $atName, $order = 0, $pkg = false)
    {
        $die = true;
        try {
            self::getByHandle($atHandle);
        } catch (exception $e) {
            $die = false;
        }
        if ($die) {
            throw new Exception(t('Authentication type with handle %s already exists!', $atHandle));
        }

        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $db = Loader::db();
        $db->executeStatement(
            'INSERT INTO AuthenticationTypes (authTypeHandle, authTypeName, authTypeIsEnabled, authTypeDisplayOrder, pkgID) values (?, ?, ?, ?, ?)',
            [$atHandle, $atName, 1, intval($order), $pkgID]);
        $est = self::getByHandle($atHandle);
        $r = $est->mapAuthenticationTypeFilePath(FILENAME_AUTHENTICATION_DB);
        if ($r->exists()) {
            Package::installDB($r->file, ContentImporter::IMPORT_MODE_INSTALL);
        }

        return $est;
    }

    /**
     * Return loaded AuthenticationType with the given handle.
     *
     * @param string $atHandle authenticationType handle
     *
     * @throws \Exception when an invalid handle is provided
     *
     * @return AuthenticationType
     */
    public static function getByHandle($atHandle)
    {
        $db = Loader::db();
        $row = $db->GetRow('SELECT * FROM AuthenticationTypes WHERE authTypeHandle=?', [$atHandle]);
        if (!$row) {
            throw new Exception(t('Invalid Authentication Type Handle'));
        }
        $at = self::load($row);

        return $at;
    }

    /**
     * Return loaded AuthenticationType with the given ID.
     *
     * @param int $authTypeID
     *
     * @throws \Exception
     *
     * @return AuthenticationType
     */
    public static function getByID($authTypeID)
    {
        $db = Loader::db();
        $row = $db->GetRow('SELECT * FROM AuthenticationTypes where authTypeID=?', [$authTypeID]);
        if (!$row) {
            throw new Exception(t('Invalid Authentication Type ID'));
        }
        $at = self::load($row);
        $at->loadController();

        return $at;
    }

    public function getAuthenticationTypeName()
    {
        return $this->authTypeName;
    }

    /**
     * Returns the display name for this instance (localized and escaped accordingly to $format)
     *
     * @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getAuthenticationTypeDisplayName($format = 'html')
    {
        $value = tc('AuthenticationType', $this->getAuthenticationTypeName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function getAuthenticationTypeDisplayOrder()
    {
        return $this->authTypeDisplayOrder;
    }

    public function getAuthenticationTypePackageID()
    {
        return $this->pkgID;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAuthenticationTypeIconHTML()
    {
        return $this->controller->getAuthenticationTypeIconHTML();
    }

    /**
     * Update the name.
     *
     * @param string $authTypeName
     */
    public function setAuthenticationTypeName($authTypeName)
    {
        $db = Loader::db();
        $db->executeStatement(
            'UPDATE AuthenticationTypes SET authTypeName=? WHERE authTypeID=?',
            [$authTypeName, $this->getAuthenticationTypeID()]);
    }

    /**
     * AuthenticationType::setAuthenticationTypeDisplayOrder
     * Update the order for display.
     *
     * @param int $order value from 0-n to signify order
     */
    public function setAuthenticationTypeDisplayOrder($order)
    {
        $db = Loader::db();
        $db->executeStatement(
            'UPDATE AuthenticationTypes SET authTypeDisplayOrder=? WHERE authTypeID=?',
            [$order, $this->getAuthenticationTypeID()]);
    }

    public function getAuthenticationTypeID()
    {
        return $this->authTypeID;
    }

    /**
     * AuthenticationType::toggle
     * Toggle the active state of an AuthenticationType.
     */
    public function toggle()
    {
        return $this->isEnabled() ? $this->disable() : $this->enable();
    }

    public function isEnabled()
    {
        return (bool) $this->getAuthenticationTypeStatus();
    }

    public function getAuthenticationTypeStatus()
    {
        return $this->authTypeIsEnabled;
    }

    /**
     * AuthenticationType::disable
     * Disable an authentication type.
     */
    public function disable()
    {
        if ($this->getAuthenticationTypeID() == 1) {
            throw new Exception(t('The core authentication cannot be disabled.'));
        }
        $db = Loader::db();
        $db->executeStatement(
            'UPDATE AuthenticationTypes SET authTypeIsEnabled=0 WHERE AuthTypeID=?',
            [$this->getAuthenticationTypeID()]);
    }

    /**
     * AuthenticationType::enable
     * Enable an authentication type.
     */
    public function enable()
    {
        $db = Loader::db();
        $db->executeStatement(
            'UPDATE AuthenticationTypes SET authTypeIsEnabled=1 WHERE AuthTypeID=?',
            [$this->getAuthenticationTypeID()]);
    }

    /**
     * AuthenticationType::delete
     * Remove an AuthenticationType, this should be used sparingly.
     */
    public function delete()
    {
        $db = Loader::db();
        if (method_exists($this->controller, 'deleteType')) {
            $this->controller->deleteType();
        }

        $db->executeStatement('DELETE FROM AuthenticationTypes WHERE authTypeID=?', [$this->authTypeID]);
    }

    /**
     * Return the path to a file.
     *
     * @param string $_file the relative path to the file
     *
     * @return bool|string
     */
    public function getAuthenticationTypeFilePath($_file)
    {
        $f = $this->mapAuthenticationTypeFilePath($_file);
        if ($f->exists()) {
            return $f->url;
        }

        return false;
    }

    /**
     * Return the first existing file path in this order:
     *  - /models/authentication/types/HANDLE
     *  - /packages/PKGHANDLE/authentication/types/HANDLE
     *  - /concrete/models/authentication/types/HANDLE
     *  - /concrete/core/models/authentication/types/HANDLE.
     *
     * @param string $_file the filename you want
     *
     * @return string this will return false if the file is not found
     */
    protected function mapAuthenticationTypeFilePath($_file)
    {
        $atHandle = $this->getAuthenticationTypeHandle();
        $env = Environment::get();
        $pkgHandle = PackageList::getHandle($this->pkgID);
        $r = $env->getRecord(implode('/', [DIRNAME_AUTHENTICATION, $atHandle, $_file]), $pkgHandle);

        return $r;
    }

    public function getAuthenticationTypeHandle()
    {
        return $this->authTypeHandle;
    }

    /**
     * Render the settings form for this type.
     * Settings forms are expected to handle their own submissions and redirect to the appropriate page.
     * Otherwise, if the method exists, all $_REQUEST variables with the arrangement: HANDLE[]
     * in an array to the AuthenticationTypeController::saveTypeForm.
     *
     * @return void
     */
    public function renderTypeForm()
    {
        if (!$this->hasTemplate('type_form')) {
            echo '<p>' . t('This authentication type does not require any customization.') . '</p>';
            return;
        }

        if (method_exists($this->controller, 'edit')) {
            $this->controller->edit();
        }

        echo $this->renderTemplate('type_form', []) ?? '';
    }

    /**
     * Render the login form for this authentication type.
     *
     * @param string $element
     * @param array  $params
     * @return void
     */
    public function renderForm($element = 'form', $params = [])
    {
        if (str_contains($element, '.')) {
            $element = explode('.', $element)[0];
        }
        if (in_array(strtolower($element), ['form', 'type_form', 'hook', 'hooked']) && !$this->hasTemplate($element)) {
            echo $this->renderTemplate('form', $params, true) ?? '';
            return;
        }
        // Preserve legacy callback routing: login/callback/<type>/<method>/... used to
        // invoke the auth controller method even when no matching template existed, then
        // render form.php as a fallback.
        if (!$this->hasTemplate($element) && method_exists($this->controller, $element)) {
            $params = array_values($params) === $params ? array_values($params) : [];
            call_user_func_array([$this->controller, $element], $params);

            $atHandle = $this->getAuthenticationTypeHandle();
            $path = implode('/', [DIRNAME_AUTHENTICATION, $atHandle, 'form.php']);
            $r = $this->getTemplateVariantLocator()->getRecord($path);
            if ($r && $r->exists()) {
                $sets = $this->controller->getSets();
                if (is_array($sets)) {
                    $params = array_merge($params, $sets);
                }

                echo $this->templateService->renderTemplate($r->getFile(), $params, $this);
                return;
            }
        }
        echo $this->renderTemplate($element, $params, true) ?? $this->renderTemplate('form', $params, true) ?? '';
    }

    /**
     * Render the hook form for saving the profile settings.
     * All settings are expected to be saved by each individual authentication type.
     *
     * @return void
     */
    public function renderHook()
    {
        echo $this->renderTemplate('hook') ?? '';
    }

    /**
     * @return bool
     */
    public function hasHook()
    {
        return $this->hasTemplate('hook');
    }

    /**
     * Render a form to be displayed when the authentication type is already hooked.
     * All settings are expected to be saved by each individual authentication type.
     *
     * @return void
     */
    public function renderHooked()
    {
        echo $this->renderTemplate('hooked') ?? '';
    }

    /**
     * Does this authentication type support rendering a form when it has already been hooked?
     *
     * @return bool
     */
    public function hasHooked()
    {
        return $this->hasTemplate('hooked');
    }

    /**
     * Is this authentication type already hooked for a specific user?
     *
     * @param \Concrete\Core\User\User|\Concrete\Core\User\UserInfo|\Concrete\Core\Entity\User\User|int $user
     *
     * @return bool|null returns null if the controller does not implement a way to determine if a user is already hooked or not
     */
    public function isHooked($user)
    {
        $result = null;
        if (is_callable([$this->controller, 'getBindingForUser'])) {
            $result = $this->controller->getBindingForUser($user) !== null;
        } else {
            $result = null;
        }

        return $result;
    }

    protected function hasTemplate(string $handle): bool
    {
        $atHandle = $this->getAuthenticationTypeHandle();
        $path = implode('/', [DIRNAME_AUTHENTICATION, $atHandle, $handle . '.php']);
        $r = $this->getTemplateVariantLocator()->getRecord($path);

        return $r->exists();
    }

    /**
     * Render the matching template for a given handle. The template can be either PHP or Twig
     *
     * @param string $handle
     * @param array<string, mixed> $data
     * @param bool $viewFallback
     * @return string|null
     */
    protected function renderTemplate(string $handle, array $data = [], bool $viewFallback = false): ?string
    {
        if ($handle === '') {
            return null;
        }

        $atHandle = $this->getAuthenticationTypeHandle();
        $path = implode('/', [DIRNAME_AUTHENTICATION, $atHandle, $handle . '.php']);
        $r = $this->getTemplateVariantLocator()->getRecord($path);
        if (!$r->exists()) {
            return null;
        }

        if (method_exists($this->controller, $handle)) {
            $params = array_values($data) === $data ? array_values($data) : [];
            call_user_func_array([$this->controller, $handle], $params);
        } elseif ($viewFallback && method_exists($this->controller, 'view')) {
            $this->controller->view();
        }

        $sets = $this->controller->getSets();
        if (is_array($sets)) {
            $data = array_merge($data, $this->controller->getSets());
        }

        return $this->templateService->renderTemplate($r->getFile(), $data, $this);
    }

    protected function getTemplateVariantLocator(): TemplateVariantLocator
    {
        return new TemplateVariantLocator($this->getLocator());
    }

    protected function getLocator(): FileLocator
    {
        if (!$this->addedPackageLocator) {
            $this->addedPackageLocator = true;
            $pkgHandle = $this->getPackageHandle();
            if ($pkgHandle) {
                $this->locator->addPackageLocation($pkgHandle);
            }
        }
        return $this->locator;
    }
}
