<?

defined('C5_EXECUTE') or die(_("Access Denied."));

class ImageFileTypeInspector extends FileTypeInspector {

	public function inspect($fv) {
		
		$path = $fv->getPath();
		$size = getimagesize($path);
		
		
		// sets an attribute - these file attribute keys should be added 
		// by the system and "reserved"
		$fv->setAttribute(FileAttributeKey::K_WIDTH, $size[0]);
		$fv->setAttribute(FileAttributeKey::K_HEIGHT, $size[1]);
		
		// create a level one and a level two thumbnail
		// load up image helper
		$hi = Loader::helper('image');
		
		// Use image helper to create thumbnail at the right size
		$fv->createThumbnailDirectories();
		$hi->create($fv->getPath(), $fv->getThumbnailPath(1), AL_THUMBNAIL_WIDTH, AL_THUMBNAIL_HEIGHT, true);
		$hi->create($fv->getPath(), $fv->getThumbnailPath(2), AL_THUMBNAIL_WIDTH_LEVEL2, AL_THUMBNAIL_HEIGHT_LEVEL2, true);
		
		// assign this file as the thumbnail (which will then copy it into the correct spot)
		$fv->refreshThumbnails();
		
	}
	

}