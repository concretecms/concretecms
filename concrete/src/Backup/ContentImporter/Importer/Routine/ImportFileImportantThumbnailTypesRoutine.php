<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Permission\Category;

class ImportFileImportantThumbnailTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'file_important_thumbnail_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->thumbnailtypes)) {
            foreach ($sx->thumbnailtypes->thumbnailtype as $l) {
                $type = new \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type();
                $type->setName((string) $l['name']);
                $type->setHandle((string) $l['handle']);
                $type->setSizingMode((string) $l['sizingMode']);
                $type->setWidth((string) $l['width']);
                $type->setHeight((string) $l['height']);
                $required = (string) $l['required'];
                if ($required) {
                    $type->requireType();
                }
                $type->save();
            }
        }
    }
}
