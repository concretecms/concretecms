<?php
namespace Concrete\Core\Permission\Response;
class FileResponse extends Response {

	public function canRead() { return $this->validate('view_file'); }
	public function canWrite() { return $this->validate('edit_file_properties'); }
	public function canAdmin() { return $this->validate('edit_file_permissions'); }

}
