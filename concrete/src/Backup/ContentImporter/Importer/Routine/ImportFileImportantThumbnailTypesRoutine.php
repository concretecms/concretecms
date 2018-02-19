<?php

namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\File\Image\Thumbnail\Type\TypeFileSet;
use Concrete\Core\File\Set\Set as FileSet;

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
                if (isset($l['sizingMode'])) {
                    $type->setSizingMode((string) $l['sizingMode']);
                }
                if (isset($l['width'])) {
                    $type->setWidth((string) $l['width']);
                }
                if (isset($l['height'])) {
                    $type->setHeight((string) $l['height']);
                }
                if (isset($l['required'])) {
                    $required = (string) $l['required'];
                    if ($required) {
                        $type->requireType();
                    }
                }
                if (isset($l['limitedToFileSets'])) {
                    $type->setLimitedToFileSets((bool) (string) $l['limitedToFileSets']);
                }
                if (isset($l->filesets)) {
                    foreach ($l->filesets->fileset as $xFileSet) {
                        $name = isset($xFileSet['name']) ? trim((string) $xFileSet['name']) : '';
                        if ($name !== '') {
                            $fileSet = FileSet::getByName($name);
                            if ($fileSet === null) {
                                $fileSet = FileSet::create($name);
                            }
                            if ($fileSet->getFileSetType() == FileSet::TYPE_PUBLIC) {
                                $type->getAssociatedFileSets()->add(new TypeFileSet($type, $fileSet));
                            }
                        }
                    }
                }
                $type->save();
            }
        }
    }
}
