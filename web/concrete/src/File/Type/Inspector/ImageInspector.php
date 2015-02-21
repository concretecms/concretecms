<?php
namespace Concrete\Core\File\Type\Inspector;
use Concrete\Core\File\Version;
use Image;
use FileAttributeKey;
use Core;

class ImageInspector extends Inspector {

	public function inspect(Version $fv) {

        $fr = $fv->getFileResource();
        $image = Image::load($fr->read());
        $data = $image->getSize();

		// sets an attribute - these file attribute keys should be added
		// by the system and "reserved"
		$at1 = FileAttributeKey::getByHandle('width');
		$at2 = FileAttributeKey::getByHandle('height');
		$fv->setAttribute($at1, $data->getWidth());
		$fv->setAttribute($at2, $data->getHeight());
    }
}
