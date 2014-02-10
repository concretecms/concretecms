<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_ImageFileTypeInspector extends FileTypeInspector {

	public function inspect($fv) {
		
		$path = $fv->getPath();
		$size = getimagesize($path);
		
		
		// sets an attribute - these file attribute keys should be added 
		// by the system and "reserved"
		$at1 = FileAttributeKey::getByHandle('width');
		$at2 = FileAttributeKey::getByHandle('height');
		$fv->setAttribute($at1, $size[0]);
		$fv->setAttribute($at2, $size[1]);
		
		// create a level one and a level two thumbnail
		// load up image helper
		$hi = Loader::helper('image');
		
		// Use image helper to create thumbnail at the right size
		$fv->createThumbnailDirectories();
		$hi->create($fv->getPath(), $fv->getThumbnailPath(1), AL_THUMBNAIL_WIDTH, AL_THUMBNAIL_HEIGHT);
		$hi->create($fv->getPath(), $fv->getThumbnailPath(2), AL_THUMBNAIL_WIDTH_LEVEL2, AL_THUMBNAIL_HEIGHT_LEVEL2);
		
	}
	

}