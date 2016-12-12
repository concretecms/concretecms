<?php

namespace Concrete\Core\File\Type\Inspector;
use Concrete\Core\File\Version;
use Loader;
use Core;
use FileAttributeKey;

class FlvInspector extends Inspector {

	public function inspect(Version $fv) {

		$at1 = FileAttributeKey::getByHandle('duration');
		$at2 = FileAttributeKey::getByHandle('width');
		$at3 = FileAttributeKey::getByHandle('height');

        // we killed $path here because the file might be hosted remotely.
        // @TODO add in support for streams through the $filesystem object.

        $cf = Core::make('helper/concrete/file');
        $fs = $fv->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $fp = $fs->readStream($cf->prefix($fv->getPrefix(), $fv->getFileName()));

		@fseek($fp,27);
		$onMetaData = fread($fp,10);

		//if ($onMetaData != 'onMetaData') exit('No meta data available in this file! Fix it using this tool: http://www.buraks.com/flvmdi/');

		@fseek($fp,16,SEEK_CUR);
		$duration = array_shift(unpack('d',strrev(fread($fp,8))));

		@fseek($fp,8,SEEK_CUR);
		$width = array_shift(unpack('d',strrev(fread($fp,8))));

		@fseek($fp,9,SEEK_CUR);
		$height = array_shift(unpack('d',strrev(fread($fp,8))));

		$fv->setAttribute($at1, $duration);
		$fv->setAttribute($at2, $width);
		$fv->setAttribute($at3, $height);

	}


}
