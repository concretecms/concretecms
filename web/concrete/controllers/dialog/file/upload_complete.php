<?
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Dialog\File\Bulk\Properties;

class UploadComplete extends Properties
{

	protected $viewPath = '/dialogs/file/upload_complete';

	public function view()
	{
		parent::view();
		$this->requireAsset('javascript', 'jquery/tristate');

		$sets = array();
		foreach($this->files as $file) {
			foreach($file->getFileSets() as $set) {
				if (!in_array($set, $sets)) {
					$sets[] = $set;
				}
			}
		}
		$this->set('filesets', $sets);
	}


}

