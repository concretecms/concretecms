<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\File\Image\Editor;
use Concrete\Core\Entity\Package;
use Concrete\Core\ImageEditor\ImageEditorService;
use Concrete\Core\Support\Facade\Application;

class ImageEditor extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Image Editors');
    }

    /**
     * @param Editor $editor
     * @return mixed
     */
    public function getItemName($editor)
    {
        return $editor->getName();
    }

    public function getPackageItems(Package $package)
    {
        $app = Application::getFacadeApplication();
        /** @var ImageEditorService $editorService */
        $editorService = $app->make(ImageEditorService::class);
        return $editorService->getListByPackage($package);
    }
}
