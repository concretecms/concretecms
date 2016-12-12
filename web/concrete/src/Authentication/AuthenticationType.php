<?php
namespace Concrete\Core\Authentication;

use Concrete\Authentication\Concrete\Controller;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Package\PackageList;
use Core;
use Environment;
use Exception;
use Loader;
use Package;

class AuthenticationType extends Object
{
    /** @var Controller */
    public $controller;
    protected $authTypeID;
    protected $authTypeName;
    protected $authTypeHandle;
    protected $authTypeDisplayOrder;
    protected $authTypeIsEnabled;
    protected $pkgID;

    public static function getListSorted()
    {
        return AuthenticationType::getList(true);
    }

    /**
     * Return a raw list of authentication types
     * @param bool $sorted true: Sort by display order, false: sort by install order
     * @param bool $activeOnly true: include only active types, false: include active and inactive types
     * @return AuthenticationType[]
     */
    public static function getList($sorted = false, $activeOnly = false)
    {
        $list = array();
        $db = Loader::db();
        $q = $db->query("SELECT * FROM AuthenticationTypes"
            . ($activeOnly ? " WHERE authTypeIsEnabled=1 " : "")
            . " ORDER BY " . ($sorted ? "authTypeDisplayOrder" : "authTypeID"));
        while ($row = $q->fetchRow()) {
            $list[] = AuthenticationType::load($row);
        }
        return $list;
    }

    /**
     * Load an AuthenticationType from an array.
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
     * @return bool|\Concrete\Core\Authentication\AuthenticationType
     */
    public static function load($arr)
    {
        $extract = array(
            'authTypeID',
            'authTypeName',
            'authTypeHandle',
            'authTypeDisplayOrder',
            'authTypeIsEnabled',
            'pkgID'
        );
        $obj = new AuthenticationType;
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
     * Load the AuthenticationTypeController into the AuthenticationType
     */
    protected function loadController()
    {
        $env = Environment::get();
        $r = $env->getRecord(DIRNAME_AUTHENTICATION . '/' . $this->authTypeHandle . '/' . FILENAME_CONTROLLER);
        $prefix = $r->override ? true : $this->getPackageHandle();
        $authTypeHandle = Core::make('helper/text')->camelcase($this->authTypeHandle);
        $class = core_class('Authentication\\' . $authTypeHandle . '\\Controller', $prefix);
        $this->controller = Core::make($class, array($this));
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
     * @param Package $pkg
     * @return AuthenticationType[]
     */
    public static function getListByPackage(Package $pkg)
    {
        $db = Loader::db();
        $list = array();

        $q = $db->query('SELECT * FROM AuthenticationTypes WHERE pkgID=?', array($pkg->getPackageID()));
        while ($row = $q->FetchRow()) {
            $list[] = AuthenticationType::load($row);
        }
        return $list;
    }

    /**
     * @param string $atHandle New AuthenticationType handle
     * @param string $atName New AuthenticationType name, expect this to be presented with "%s Authentication Type"
     * @param int $order Order int, used to order the display of AuthenticationTypes
     * @param bool|\Package $pkg Package object to which this AuthenticationType is associated.
     * @throws \Exception
     * @return AuthenticationType Returns a loaded authentication type.
     */
    public static function add($atHandle, $atName, $order = 0, $pkg = false)
    {
        $die = true;
        try {
            AuthenticationType::getByHandle($atHandle);
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
        $db->Execute(
           'INSERT INTO AuthenticationTypes (authTypeHandle, authTypeName, authTypeIsEnabled, authTypeDisplayOrder, pkgID) values (?, ?, ?, ?, ?)',
           array($atHandle, $atName, 1, intval($order), $pkgID));
        $est = AuthenticationType::getByHandle($atHandle);
        $r = $est->mapAuthenticationTypeFilePath(FILENAME_AUTHENTICATION_DB);
        if ($r->exists()) {
            Package::installDB($r->file);
        }

        return $est;
    }

    /**
     * Return loaded AuthenticationType with the given handle.
     * @param string $atHandle AuthenticationType handle.
     * @throws \Exception when an invalid handle is provided
     * @return AuthenticationType
     */
    public static function getByHandle($atHandle)
    {
        $db = Loader::db();
        $row = $db->GetRow('SELECT * FROM AuthenticationTypes WHERE authTypeHandle=?', array($atHandle));
        if (!$row) {
            throw new Exception(t('Invalid Authentication Type Handle'));
        }
        $at = AuthenticationType::load($row);
        return $at;
    }

    /**
     * Return loaded AuthenticationType with the given ID.
     * @param int $authTypeID
     * @throws \Exception
     * @return AuthenticationType
     */
    public static function getByID($authTypeID)
    {
        $db = Loader::db();
        $row = $db->GetRow('SELECT * FROM AuthenticationTypes where authTypeID=?', array($authTypeID));
        if (!$row) {
            throw new Exception(t('Invalid Authentication Type ID'));
        }
        $at = AuthenticationType::load($row);
        $at->loadController();
        return $at;
    }

    public function getAuthenticationTypeName()
    {
        return $this->authTypeName;
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
     * Update the name
     * @param string $authTypeName
     */
    public function setAuthenticationTypeName($authTypeName)
    {
        $db = Loader::db();
        $db->Execute(
           'UPDATE AuthenticationTypes SET authTypeName=? WHERE authTypeID=?',
           array($authTypeName, $this->getAuthenticationTypeID()));
    }

    /**
     * AuthenticationType::setAuthenticationTypeDisplayOrder
     * Update the order for display.
     *
     * @param int $order value from 0-n to signify order.
     */
    public function setAuthenticationTypeDisplayOrder($order)
    {
        $db = Loader::db();
        $db->Execute(
           'UPDATE AuthenticationTypes SET authTypeDisplayOrder=? WHERE authTypeID=?',
           array($order, $this->getAuthenticationTypeID()));
    }

    public function getAuthenticationTypeID()
    {
        return $this->authTypeID;
    }

    /**
     * AuthenticationType::toggle
     * Toggle the active state of an AuthenticationType
     */
    public function toggle()
    {
        return ($this->isEnabled() ? $this->disable() : $this->enable());
    }

    public function isEnabled()
    {
        return !!$this->getAuthenticationTypeStatus();
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
            throw new Exception(t('The core concrete5 authentication cannot be disabled.'));
        }
        $db = Loader::db();
        $db->Execute(
           'UPDATE AuthenticationTypes SET authTypeIsEnabled=0 WHERE AuthTypeID=?',
           array($this->getAuthenticationTypeID()));
    }

    /**
     * AuthenticationType::enable
     * Enable an authentication type.
     */
    public function enable()
    {
        $db = Loader::db();
        $db->Execute(
           'UPDATE AuthenticationTypes SET authTypeIsEnabled=1 WHERE AuthTypeID=?',
           array($this->getAuthenticationTypeID()));
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

        $db->Execute("DELETE FROM AuthenticationTypes WHERE authTypeID=?", array($this->authTypeID));
    }

    /**
     * Return the path to a file
     * @param string $_file the relative path to the file.
     * @return bool|string
     */
    public function  getAuthenticationTypeFilePath($_file)
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
     *  - /concrete/core/models/authentication/types/HANDLE
     *
     * @param string $_file The filename you want.
     * @return string This will return false if the file is not found.
     */
    protected function mapAuthenticationTypeFilePath($_file)
    {
        $atHandle = $this->getAuthenticationTypeHandle();
        $env = Environment::get();
        $pkgHandle = PackageList::getHandle($this->pkgID);
        $r = $env->getRecord(implode('/', array(DIRNAME_AUTHENTICATION, $atHandle, $_file)), $pkgHandle);
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
     * in an array to the AuthenticationTypeController::saveTypeForm
     */
    public function renderTypeForm()
    {
        $type_form = $this->mapAuthenticationTypeFilePath('type_form.php');
        if ($type_form->exists()) {
            ob_start();
            $this->controller->edit();
            extract($this->controller->getSets());
            require_once($type_form->file); // We use the $this method to prevent extract overwrite.
            $out = ob_get_contents();
            ob_end_clean();
            echo $out;
        } else {
            echo "<p>" . t("This authentication type does not require any customization.") . "</p>";
        }
    }

    /**
     * Render the login form for this authentication type.
     *
     * @param string $element
     * @param array  $params
     */
    public function renderForm($element = 'form', $params = array())
    {
        $this->controller->requireAsset('javascript', 'backstretch');

        $form_element = $this->mapAuthenticationTypeFilePath($element . '.php');
        if (!$form_element->exists()) {
            $form_element = $this->mapAuthenticationTypeFilePath('form.php');
        }
        ob_start();
        if (method_exists($this->controller, $element)) {
            call_user_func_array(array($this->controller, $element), $params);
        } else {
            $this->controller->view();
        }
        extract(array_merge($params, $this->controller->getSets()));
        require($form_element->file);
        $out = ob_get_contents();
        ob_end_clean();
        echo $out;
    }

    /**
     * Render the hook form for saving the profile settings.
     * All settings are expected to be saved by each individual authentication type
     */
    public function renderHook()
    {
        $form_hook = $this->mapAuthenticationTypeFilePath('hook.php');
        if ($form_hook->exists()) {
            ob_start();
            if(method_exists($this->controller, 'hook'))
            {
                $this->controller->hook();
            }
            extract($this->controller->getSets());
            require_once($form_hook->file);
            $out = ob_get_contents();
            ob_end_clean();
            echo $out;
        }
    }

    public function hasHook() {
        $form_hook = $this->mapAuthenticationTypeFilePath('hook.php');

        return method_exists($this->controller, 'hook') || $form_hook->exists();
    }

}
