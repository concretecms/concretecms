<?php
namespace Concrete\Controller\Backend;

use Controller;
use Permissions;
use Loader;
use User;
use Concrete\Core\Attribute\Key\UserKey;
use Concrete\Core\Attribute\Set;
use stdClass;
use Exception;

class Attributes extends Controller
{

    public function sortUser()
    {
        $this->canAccess();
        $uats = $_REQUEST['akID'];


        if (is_array($uats)) {
            $uats = array_filter($uats, 'is_numeric');
        }

        if (count($uats)) {
            UserKey::updateAttributesDisplayOrder($uats);
        }
    }

    public function sortInSet()
    {
        $this->canAccess();
        $as = Set::getByID($_REQUEST['asID']);

        $uats = $_REQUEST['akID'];
        if (is_array($uats)) {
            $uats = array_filter($uats, 'is_numeric');
        }
        if (count($uats)) {
            $as->updateAttributesDisplayOrder($uats);
        }
    }

    protected function canAccess()
    {
        if (!Loader::helper('validation/token')->validate('attribute_sort')) {
            throw new Exception(t("Invalid Token"));
        }

        $tp = Loader::helper('concrete/user');
        if (!$tp->canAccessUserSearchInterface()) {
            throw new Exception(t("You have no access to users."));
        }
    }

}