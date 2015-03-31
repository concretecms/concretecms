<?php
namespace Concrete\Controller\Dialog\File;

use \Concrete\Controller\Backend\UserInterface\File as BackendInterfaceFileController;

class UploadComplete extends BackendInterfaceFileController
{

	protected $viewPath = '/dialogs/file/upload_complete';

	protected function canAccess()
	{
		return $this->permissions->canViewFileInFileManager();
	}

	public function view()
	{

	}
}

