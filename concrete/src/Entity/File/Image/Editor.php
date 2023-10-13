<?php

namespace Concrete\Core\Entity\File\Image;

use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\ImageEditor\Controller\EditorControllerInterface;
use Concrete\Core\ImageEditor\Manager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="Editor"
 * )
 */
class Editor
{
    use PackageTrait;

    /**
     * @ORM\Column(type="string", options={"unsigned": true}, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", options={"unsigned": true})
     */
    protected $handle;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Editor
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     * @return Editor
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
        return $this;
    }

    public function getController(): EditorControllerInterface
    {
        return app(Manager::class)->driver($this->getHandle());
    }

    public function getImageEditorElement(): ?Element
    {
        $handle = $this->getController()->getImageEditorHandle();
        $elementManager = app(ElementManager::class);
        $element = $elementManager->get('files/edit/image_editor/' . $handle, null, null, $this->getPackageHandle());
        return $element;
    }

    public function getThumbnailEditorHandle(): ?Element
    {
        $handle = $this->getController()->getThumbnailEditorHandle();
        $elementManager = app(ElementManager::class);
        $element = $elementManager->get('files/edit/thumbnail_editor/' . $handle, null, null, $this->getPackageHandle());
        return $element;
    }



}