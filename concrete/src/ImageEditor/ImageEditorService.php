<?php

namespace Concrete\Core\ImageEditor;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\Image\Editor;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Entity\Package;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailTypeVersion;
class ImageEditorService
{

    const DEFAULT_EDITOR = 'default';

    /** @var EntityManagerInterface */
    protected $entityManager;
    protected $imageEditorRepository;
    /** @var Repository */
    protected $config;
    protected $app;

    public function __construct(
        EntityManagerInterface $entityManager,
        Application $app,
        Repository $config
    )
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->imageEditorRepository = $this->entityManager->getRepository(Editor::class);
        $this->checkDefaultEditor();
    }

    private function checkDefaultEditor()
    {
        $editor = $this->getDefaultEditor();

        if (!$editor instanceof Editor) {
            $this->setupDefaultEditor();
        }
    }

    private function setupDefaultEditor()
    {
        $this->addEditor(self::DEFAULT_EDITOR, t("Image Editor"));
    }

    public function removeEditor(
        $editor
    )
    {
        $errorList = new ErrorList();

        if ($editor->getHandle() === self::DEFAULT_EDITOR) {
            $errorList->add(t("You can not remove the default editor."));
        }

        try {
            $this->entityManager->remove($editor);
            $this->entityManager->flush();
        } catch (Exception $e) {
            $errorList->add(t("Error while removing the editor."));
        }

        return $errorList;
    }

    /**
     * Return an array of Types that are associated with a specific package.
     *
     * @param Package $package
     *
     * @return Editor[]
     */
    public function getListByPackage(
        $package
    )
    {
        return $this->imageEditorRepository->findBy(['package' => $package]);
    }

    /**
     * @param string $handle
     * @param string $name
     * @param Package $package
     * @return ErrorList
     */
    public function addEditor(
        $handle,
        $name,
        $package = null
    )
    {
        $errorList = new ErrorList();

        if (strlen($handle) === 0) {
            $errorList->add(t("The handle can not be empty."));
        }

        if (strlen($name) === 0) {
            $errorList->add(t("The name can not be empty."));
        }

        if ($this->getEditorByHandle($handle) instanceof Editor) {
            $errorList->add(t("The handle is already in use."));
        }

        if (!$errorList->has()) {
            $editor = new Editor();

            $editor->setHandle($handle);
            $editor->setName($name);
            $editor->setPackage($package);

            try {
                $this->entityManager->persist($editor);
                $this->entityManager->flush();
            } catch (Exception $e) {
                $errorList->add(t("Error while adding the editor."));
            }
        }

        return $errorList;
    }

    /**
     * @return Editor[]
     */
    public function getAllEditors()
    {
        return $this->imageEditorRepository->findAll();
    }

    /**
     * @return array
     */
    public function getEditorList()
    {
        $editorList = [];

        foreach ($this->getAllEditors() as $editor) {
            $editorList[$editor->getHandle()] = $editor->getName();
        }

        return $editorList;
    }

    /**
     * @param string $handle
     * @return Editor
     */
    public function getEditorByHandle($handle)
    {
        try {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $this->imageEditorRepository->findOneBy(["handle" => $handle]);
        } catch (Exception $e) {
        }
    }

    public function getActiveEditor()
    {
        $editor = $this->getEditorByHandle($this->config->get("image_editor.active_editor"));

        if (!$editor instanceof Editor) {
            $editor = $this->getDefaultEditor();
        }

        return $editor;
    }

    /**
     * @param Editor $editor
     */
    public function setActiveEditor(
        $editor
    )
    {
        if (!$editor instanceof Editor) {
            $editor = $this->getDefaultEditor();
        }

        if ($editor instanceof Editor) {
            $this->config->save("image_editor.active_editor", $editor->getHandle());
        }
    }

    /**
     * @return Editor
     */
    public function getDefaultEditor()
    {
        try {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $this->imageEditorRepository->findOneBy(["handle" => self::DEFAULT_EDITOR]);
        } catch (Exception $e) {
        }
    }

    /**
     * @param Version $fileVersion
     */
    public function renderActiveImageEditor(
        $fileVersion
    )
    {
        if ($fileVersion instanceof Version) {
            $activeEditor = $this->getActiveEditor();
            $element = $activeEditor->getImageEditorElement();
            if ($element instanceof Element) {
                $element->getElementController()->set("fileVersion", $fileVersion);
                $element->render();
            }
        }
    }

    /**
     * @param Version $fileVersion
     */
    public function renderActiveThumbnailEditor(
        $fileVersion,
        ThumbnailTypeVersion $thumbnail
    )
    {
        if ($fileVersion instanceof Version) {
            $activeEditor = $this->getActiveEditor();
            $element = $activeEditor->getThumbnailEditorHandle();
            if ($element instanceof Element) {
                $element->getElementController()->setThumbnail($thumbnail);
                $element->getElementController()->set("fileVersion", $fileVersion);
                $element->render();
            }
        }
    }
}
