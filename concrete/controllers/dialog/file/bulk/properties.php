<?php

namespace Concrete\Controller\Dialog\File\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait as KeySelectorControllerTrait;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\EditResponse as FileEditResponse;
use Concrete\Core\File\File;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;

class Properties extends BackendInterfaceController
{
    use KeySelectorControllerTrait;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/file/bulk/properties';

    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * List of files to edit.
     *
     * @var array
     */
    protected $files = [];

    /**
     * Define whether the user can edit file properties.
     *
     * @var bool
     */
    protected $canEdit = false;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(CategoryService $attributeCategoryService, Filesystem $filesystem)
    {
        parent::__construct();

        $categoryEntity = $attributeCategoryService->getByHandle('file');
        $this->category = $categoryEntity->getAttributeKeyCategory();
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::on_start()
     */
    public function on_start()
    {
        parent::on_start();

        $this->populateFiles();
    }

    public function view()
    {
        $keySelector = $this->app->make(ElementManager::class)->get('attribute/component/key_selector', [
            'category' => $this->getCategory()
        ]);
        /** @var \Concrete\Controller\Element\Attribute\Component\KeySelector $controller */
        $controller = $keySelector->getElementController();
        $controller->setSelectAttributeUrl($this->action('get_attribute'));
        $controller->setObjects($this->getObjects());

        $this->set('files', $this->files);
        $this->set('keySelector', $keySelector);
        $this->set('form', $this->app->make('helper/form'));
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $attributesResponse = $this->saveAttributes();
            $r = new FileEditResponse();
            $r->setFiles($this->files);
            if ($attributesResponse instanceof ErrorList) {
                $r->setError($attributesResponse);
            } else {
                $r->setMessage(t('Attributes updated successfully.'));
            }

            return new JsonResponse($r);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait::getObjects()
     */
    public function getObjects(): array
    {
        return $this->files;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait::getCategory()
     */
    public function getCategory(): CategoryInterface
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait::canEditAttributeKey()
     */
    public function canEditAttributeKey(int $akID): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        $folder = $this->filesystem->getRootFolder();
        $fp = new Checker($folder);
        if ($fp->canAccessFileManager()) {
            return $this->getAction() === 'getAttribute' || $this->canEdit;
        }

        return false;
    }

    protected function populateFiles(): void
    {
        $requestFID = $this->request->get('fID');
        if (is_array($requestFID)) {
            foreach ($requestFID as $fID) {
                $f = File::getByID($fID);
                if ($f !== null) {
                    $this->files[] = $f;
                }
            }
        }

        if (!empty($this->files)) {
            $this->canEdit = true;
            foreach ($this->files as $f) {
                $fp = new Checker($f);
                if (!$fp->canEditFileProperties()) {
                    $this->canEdit = false;
                    break;
                }
            }
        } else {
            $this->canEdit = false;
        }
    }
}
