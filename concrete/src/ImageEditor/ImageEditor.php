<?php
namespace Concrete\Core\ImageEditor;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\View\View;

class ImageEditor
{
    const ImageEditorExtensionControl = 1;
    const ImageEditorExtensionFilter = 2;

    /** @var EditorExtensionInterface[] */
    protected $filterList = array();

    /** @var EditorExtensionInterface[] */
    protected $controlList = array();

    /**
     * @param $type [ ImageEditorExtensionFilter | ImageEditorExtensionControl ]
     * @param EditorExtensionInterface $extension
     */
    public function addExtension($type, EditorExtensionInterface $extension)
    {
        switch ($type) {
            case self::ImageEditorExtensionControl:
                $this->controlList[$extension->getHandle()] = $extension;
                break;

            case self::ImageEditorExtensionFilter:
                $this->filterList[$extension->getHandle()] = $extension;
                break;

            default:
                throw new \RuntimeException('Invalid extension type.');
        }
    }

    /**
     * @param EditorExtensionInterface $extension
     */
    public function addFilter(EditorExtensionInterface $extension)
    {
        $this->addExtension(self::ImageEditorExtensionFilter, $extension);
    }

    /**
     * @param EditorExtensionInterface $extension
     */
    public function addControl(EditorExtensionInterface $extension)
    {
        $this->addExtension(self::ImageEditorExtensionControl, $extension);
    }

    /**
     * @return EditorExtensionInterface[]
     */
    public function getControlList()
    {
        return $this->controlList;
    }

    /**
     * @return EditorExtensionInterface[]
     */
    public function getFilterList()
    {
        return $this->filterList;
    }

    /**
     * @param Version $version
     *
     * @return View
     */
    public function getView(Version $version)
    {
        $view = new View('image-editor/editor');
        $view->addScopeItems(array(
            'editor' => $this,
            'fv' => $version,
        ));

        return $view;
    }
}
