<?php

namespace Concrete\Core\File\Image;

use File;
use Imagine\Gd\Image;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class IconGenerator {

    /**
     * @param File $fileObject
     * 
     * @return mixed
     */
    public function getRealIconURL($fileObject) {
        $retVal = false;

        if ($fileObject instanceof \Concrete\Core\Entity\File\File) {
            $filesystem = $fileObject->getFileStorageLocationObject()->getFileSystemObject();

            $configuration = $fileObject->getFileStorageLocationObject()->getConfigurationObject();

            $fID = $fileObject->getFileID();

            $timestamp = $fileObject->getFileResource()->getTimestamp();

            $icoFileName = '/cache/thumbnails/' . md5(implode(':', array($fID, $timestamp))) . ".ico";

            if ($filesystem->has($icoFileName) === false) {
                $fileData = $fileObject->getFileContents();

                $imagine = new Imagine();

                $resizedImage = $imagine->load($fileData)->resize(new Box(16, 16))->crop(new Point(0, 0), new Box(16, 16));

                if ($resizedImage instanceof Image) {
                    // https://msdn.microsoft.com/de-de/library/windows/desktop/dd183376(v=vs.85).aspx
                    $bitmapData = pack("VVVvvVVVVVV", 40, 16, 32, 1, 32, 0, 0, 0, 0, 0, 0);

                    for ($y = 15; $y >= 0; $y--) {
                        for ($x = 0; $x < 16; $x++) {
                            $bitmapData .= pack('V', imagecolorat($resizedImage->getGdResource(), $x, $y) & 0xFFFFFF);
                        }
                    }

                    // https://msdn.microsoft.com/en-us/library/ms997538.aspx
                    $icoHeader = pack("vvvCCCCvvVV", 0, 1, 1, 16, 16, 0, 0, 1, 32, strlen($bitmapData), 22);

                    // Merge ico header + bitmap data together
                    $icoFileContent = $icoHeader . $bitmapData;

                    // write to file
                    $filesystem->write($icoFileName, $icoFileContent);
                }
            }

            $retVal = $configuration->getPublicURLToFile($icoFileName);
        }

        return $retVal;
    }

}
