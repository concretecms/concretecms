<?
namespace Concrete\Controller\SinglePage\Dashboard\System\Files;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use PermissionKey;
use TaskPermission;
use PermissionAccess;
class ImageUploading extends DashboardPageController {

	var $helpers = array('form','concrete/ui','validation/token', 'concrete/file');

	public function view() {
		$restrict = Config::get('concrete.file_manager.restrict_uploaded_image_sizes');
		$this->set('restrict_uploaded_image_sizes', $restrict);

	}

	public function saved() {
		$this->set('message', t('Image uploading settings saved.'));
		$this->view();
	}

	public function save(){
		Config::save('concrete.file_manager.restrict_uploaded_image_sizes', (bool)$this->post('restrict_uploaded_image_sizes'));
		$this->redirect('/dashboard/system/files/image_uploading','saved');
	}
}
