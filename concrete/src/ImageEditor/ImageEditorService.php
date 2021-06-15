<?php

namespace Concrete\Core\ImageEditor;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\Image\Editor;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Entity\Package;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ImageEditorService
{
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
        $this->addEditor("toast", t("Toast Image Editor"));
    }

    public function removeEditor(
        $editor
    )
    {
        $errorList = new ErrorList();

        if ($editor->getHandle() === "toast") {
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
            return $this->imageEditorRepository->findOneBy(["handle" => "toast"]);
        } catch (Exception $e) {
        }
    }

    /**
     * @param Version $fileVersion
     */
    public function renderActiveEditor(
        $fileVersion
    )
    {
        if ($fileVersion instanceof Version) {
            $activeEditor = $this->getActiveEditor();
            /** @var ElementManager $elementManager */
            $elementManager = $this->app->make(ElementManager::class);

            $element = $elementManager->get(
                'files/edit/image_editor/' . $activeEditor->getHandle(),
                null,
                null,
                $activeEditor->getPackageHandle()
            );

            if ($element instanceof Element) {
                $element->getElementController()->set("fileVersion", $fileVersion);
                $element->render();
            }
        }
    }
}
