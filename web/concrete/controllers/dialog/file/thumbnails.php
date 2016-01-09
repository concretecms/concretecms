<?php
namespace Concrete\Controller\Dialog\File;
use \Concrete\Controller\Backend\UserInterface\File as BackendInterfaceFileController;
use \Concrete\Core\File\EditResponse as FileEditResponse;
use Concrete\Core\File\Type\Type;
use \Exception;
class Thumbnails extends BackendInterfaceFileController {

	protected $viewPath = '/dialogs/file/thumbnails';

	protected function canAccess() {
        $type = $this->file->getTypeObject();
		return $this->permissions->canEditFileContents() && $type->getGenericType() == Type::T_IMAGE;
	}

	public function view() {
        $types = \Concrete\Core\File\Image\Thumbnail\Type\Type::getVersionList();
        $this->set('types', $types);
        $version = $this->file->getVersion($this->request->request->get('fvID'));
        $this->set('version', $version);
	}

}

