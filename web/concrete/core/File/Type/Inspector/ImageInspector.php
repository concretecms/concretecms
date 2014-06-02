<?
namespace Concrete\Core\File\Type\Inspector;
use Loader;
use FileAttributeKey;
class ImageInspector extends Inspector {

	public function inspect($fv) {

        $fr = $fv->getFileResource();
        //$fs = $fv->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        //$stream = $fs->readStream($fr->getPath());

		$image = imagecreatefromstring($fr->read());
        $width = imagesx($image);
        $height = imagesx($image);

		
		// sets an attribute - these file attribute keys should be added 
		// by the system and "reserved"
		$at1 = FileAttributeKey::getByHandle('width');
		$at2 = FileAttributeKey::getByHandle('height');
		$fv->setAttribute($at1, $width);
		$fv->setAttribute($at2, $height);
		
		// create a level one and a level two thumbnail
		// load up image helper
        /*
		$hi = Loader::helper('image');
		
		// Use image helper to create thumbnail at the right size
		$hi->create($fv->getPath(), $fv->getThumbnailPath(1), AL_THUMBNAIL_WIDTH, AL_THUMBNAIL_HEIGHT);
		$hi->create($fv->getPath(), $fv->getThumbnailPath(2), AL_THUMBNAIL_WIDTH_LEVEL2, AL_THUMBNAIL_HEIGHT_LEVEL2);
		*/
	}
	

}