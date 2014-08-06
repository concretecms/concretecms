<?
namespace Concrete\Controller\Backend;
use Controller;
use Permissions;
use Loader;
use User;
use Concrete\Core\Attribute\Key\UserKey;
use Concrete\Core\Attribute\Set;
use stdClass;
use Exception;

class UserAttributes extends Controller {

	public function sort() {
        $this->canAccess();
        $uats = $_REQUEST['akID'];
        $asID = $_REQUEST['asID'];


        if(is_array($uats)) {
            $uats = array_filter($uats,'is_numeric');
        }

        if(count($uats)) {
          if(is_numeric($asID) && $asID) {
              $as = Set::getByID($asID);
          }
          if($as instanceof Set) {
              $as->updateAttributesDisplayOrder($uats);
          } else {
              UserKey::updateAttributesDisplayOrder($uats);
          }
        }
	}

    public function sortInSet() {
        $this->canAccess();
        $as = Set::getByID($_REQUEST['asID']);

        $uats = $_REQUEST['akID'];
        if(is_array($uats)) {
            $uats = array_filter($uats,'is_numeric');
        }
        if(count($uats)) {

        }
    }

    protected function canAccess() {
        if (!Loader::helper('validation/token')->validate('user_attribute_sort')) {
            throw new Exception(t("Invalid Token"));
        }

        $tp = Loader::helper('concrete/user');
        if (!$tp->canAccessUserSearchInterface()) {
            throw new Exception(t("You have no access to users."));
        }
    }

}