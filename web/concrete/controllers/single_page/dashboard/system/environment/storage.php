<?
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use \Concrete\Core\File\StorageLocation\StorageLocation as FileStorageLocation;
use Concrete\Core\File\StorageLocation\Type\Type;

class Storage extends DashboardPageController {

	var $helpers = array('form','concrete/ui','validation/token', 'concrete/file');
	
	public function view($updated=false) {
        $this->set('locations', FileStorageLocation::getList());
        $types = array();
        $storageLocationTypes = Type::getList();
        foreach($storageLocationTypes as $type) {
            if ($type->getHandle() == 'default') {
                continue;
            }
            $types[$type->getID()] = $type->getName();
        }
        $this->set('types', $types);
	}

    public function select_type() {
        $fslTypeID = $this->request('fslTypeID');
        $type = Type::getByID($fslTypeID);
        $this->set('type', $type);
    }

    public function storage_location_added() {
		$this->set('message', t('File storage location added.'));
		$this->view();
	}

	public function storage_deleted() {
		$this->set('message', t('File storage location removed. Files using this location have been reset.'));
		$this->view();
	}

    public function add()
    {
        $request = \Request::getInstance();
        $val = Loader::helper('validation/strings');
        $type = Type::getByID($request->get('fslTypeID'));

        if (!is_object($type)) {
            $this->error->add(t('Invalid type object.'));
        } else {
            $e = $type->getConfigurationObject()->validateRequest($request);
            if (is_object($e)) {
                $this->error->add($e);
            }
        }
        if (!$this->error->has()) {
            if (!$val->notempty($request->request->get('fslName'))) {
                $this->error->add(t('Your file storage location must have a name.'));
            }
        }
        if (!Loader::helper('validation/token')->validate('add')) {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }


        if (!$this->error->has()) {
            $configuration = $type->getConfigurationObject();
            $configuration->loadFromRequest($request);
            $fsl = FileStorageLocation::add($configuration,
                $request->request->get('fslName'),
                $request->request->get('fslIsDefault')
            );

            $this->redirect('/dashboard/system/environment/storage', 'storage_location_added');
        }

        $this->select_type($request->get('fslTypeID'));
    }

}
