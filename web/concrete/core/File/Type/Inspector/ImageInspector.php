<?
namespace Concrete\Core\File\Type\Inspector;
use Image;
use FileAttributeKey;
use Core;
use \Imagine\Image\Box;

class ImageInspector extends Inspector {

	public function inspect($fv) {

        $fr = $fv->getFileResource();
        $fo = $fv->getFile();
        $image = Image::load($fr->read());
        $data = $image->getSize();

		// sets an attribute - these file attribute keys should be added
		// by the system and "reserved"
		$at1 = FileAttributeKey::getByHandle('width');
		$at2 = FileAttributeKey::getByHandle('height');
		$fv->setAttribute($at1, $data->getWidth());
		$fv->setAttribute($at2, $data->getHeight());

        $fsl = $fo->getFileStorageLocationObject();
        $filesystem = $fsl->getFileSystemObject();


        $helper = Core::make('helper/concrete/file');
        $filesystem->write(
            $helper->getThumbnailFilePath($fv->getPrefix(), $fv->getFilename(), 1),
            $image->thumbnail(new Box(AL_THUMBNAIL_WIDTH, AL_THUMBNAIL_HEIGHT))
        );
        $filesystem->write(
            $helper->getThumbnailFilePath($fv->getPrefix(), $fv->getFilename(), 2),
            $image->thumbnail(new Box(AL_THUMBNAIL_WIDTH_LEVEL2, AL_THUMBNAIL_HEIGHT_LEVEL2))
        );
    }
}