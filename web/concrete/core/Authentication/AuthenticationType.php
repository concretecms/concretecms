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

    public static function getListSorted()
    {
        return AuthenticationType::getList(true);
    }

    /**
     * AuthenticationType::getList
     * Return a raw list of authentication types, sorted by either installed order or display order.
     *
     * @param bool $sorted true: Sort by installed order, false: Sort by display order
     */
    public static function getList($sorted = false)
    {
        $list = array();
        $db = Loader::db();
        $q = $db->query("SELECT * FROM AuthenticationTypes" . ($sorted ? " ORDER BY authTypeDisplayOrder" : ""));
        while ($row = $q->fetchRow()) {
            $list[] = AuthenticationType::load($row);
        }
        return $list;
    }

    /**
     * AuthenticationType::load
     * Load an AuthenticationType from an array.
     *
     * @param array $arr Array of raw sql data.
     */
    public static function load($arr)
    {
        $extract = array(
            'authTypeID',
            'authTypeName',
            'authTypeHandle',
            'authTypeHandle',
            'authTypeName',
            'authTypeDisplayOrder',
            'authTypeIsEnabled',
            'pkgID');
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
     * AuthenticationType::loadController
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

    public static function getActiveListSorted()
    {
        return AuthenticationType::getActiveList(true);
    }

    /**
     * AuthenticationType::getActiveList
     * Return a raw list of /ACTIVE/ authentication types, sorted by either installed order or display order.
     *
     * @param bool $sorted true: Sort by installed order, false: Sort by display order
     */
    public static function getActiveList($sorted = false)
    {
        $list = array();
        $db = Loader::db();
        $q = $db->query(
                "SELECT * FROM AuthenticationTypes WHERE authTypeIsEnabled=1" . ($sorted ? " ORDER BY authTypeDisplayOrder" : ""));
        while ($row = $q->fetchRow()) {
            $list[] = AuthenticationType::load($row);
        }
        return $list;
    }

    /**
     * AuthenticationType::getListByPackage
     * Return a list of AuthenticationTypes that are associated with a specific package.
     *
     * @param Package $pkg
     */
    public static function getListByPackage(Package $pkg)
    {
        $db = Loader::db();
        $list = array();

        $q = $db->query('SELECT * FROM AuthenticationTypes WHERE pkgID=?', array($pkg->getPackageID()));
        while ($row = $q->FetchRow()) {
            $list[] = AuthenticationType::load($row);
        }
        $r->Close();
        return $list;
    }

    /**
     * AuthenticationType::add
     *
     * @param    string $atHandle New AuthenticationType handle
     * @param    string $atName   New AuthenticationType name, expect this to be presented with "%s Authentication Type"
     * @param    int $order       Order int, used to order the display of AuthenticationTypes
     * @param    Package $pkg     Package object to which this AuthenticationType is associated.
     * @return    AuthenticationType    Returns a loaded authentication type.
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
        $id = $db->Insert_ID();
        $est = AuthenticationType::getByID($id);
        $r = $est->mapAuthenticationTypeFilePath(FILENAME_AUTHENTICATION_DB);
        if ($r->exists()) {
            Package::installDB($r->file);
        }

        return $est;
    }

    /**
     * AuthenticationType::getByHandle
     * Return loaded AuthenticationType with the given handle.
     *
     * @param string $atHandle AuthenticationType handle.
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
     * @param int $authTypeID
     * @return Concrete5_Model_AuthenticationType
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
     * AuthenticationType::getAuthenticationTypeFilePath
     * Return the path to a file, this is always BASE_URL.DIR_REL.FILE
     *
     * @param string $_file the relative path to the file.
     */
    public function  getAuthenticationTypeFilePath($_file)
    {
        $f = $this->mapAuthenticationTypeFilePath($_file);
        if ($f->exists()) {
            return $r->url;
        }
        return false;
    }

    /**
     * AuthenticationType::mapAuthenticationTypeFilePath
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
     * AuthenticationType::renderTypeForm
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
     * AuthenticationType::renderForm
     * Render the login form for this authentication type.
     *
     * @param string $element
     * @param array  $params
     */
    public function renderForm($element = 'form', $params = array())
    {
        $this->controller->requireAsset('javascript', 'backstretch');

        $form = $this->mapAuthenticationTypeFilePath($element . '.php');
        if (!$form->exists()) {
            $form = $this->mapAuthenticationTypeFilePath('form.php');
        }
        ob_start();
        if (method_exists($this->controller, $element)) {
            call_user_func_array(array($this->controller, $element), $params);
        } else {
            $this->controller->view();
        }
        extract(array_merge($params, $this->controller->getSets()));
        require_once($form->file);
        $out = ob_get_contents();
        ob_end_clean();
        echo $out;
    }

    /**
     * AuthenticationType::renderHook
     * Render the hook form for saving the profile settings.
     * All settings are expected to be saved by each individual authentication type
     */
    public function renderHook()
    {
        $form = $this->mapAuthenticationTypeFilePath('hook.php');
        if ($form->exists()) {
            ob_start();
            $this->controller->hook();
            extract($this->controller->getSets());
            require_once($form->file);
            $out = ob_get_contents();
            ob_end_clean();
            echo $out;
        }
    }

}
