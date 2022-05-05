<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface\File as BackendInterfaceFileController;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\EditResponse;
use Concrete\Core\File\File;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;

class Properties extends BackendInterfaceFileController
{

    use ControllerTrait;

    protected $viewPath = '/dialogs/file/properties';

    /**
     * @var FileCategory
     */
    protected $category;

    public function __construct(
        CategoryService $attributeCategoryService,
        JsonSerializer $serializer
    )
    {
        parent::__construct();
        $this->serializer = $serializer;
        $categoryEntity = $attributeCategoryService->getByHandle('file');
        $this->category = $categoryEntity->getController();
    }

    public function canAccess()
    {
        $permissions = new Checker($this->file);
        return $permissions->canEditFileProperties();
    }

    public function getObjects(): array
    {
        return [$this->file->getVersionToModify()];
    }

    public function getCategory(): CategoryInterface
    {
        return $this->category;
    }

    public function canEditAttributeKey(int $akID): bool
    {
        return true;
    }

    public function view()
    {
        $version = $this->file->getRecentVersion();
        $keySelector = $this->app->make(ElementManager::class)
            ->get('attribute/component/key_selector', ['category' => $this->category]);
        $controller = $keySelector->getElementController();
        $controller->setSelectAttributeUrl($this->action('get_attribute'));
        $controller->setObjects([$version]);
        $this->set('keySelector', $keySelector);
        $this->set('file', $version);
        $this->set('form', $this->app->make('helper/form'));
        $this->set('token', $this->app->make('token'));
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $fv = $this->file->getVersionToModify();
            $fv->updateTitle($this->request->request->get('title'));
            $fv->updateDescription($this->request->request->get('description'));
            $fv->updateTags($this->request->request->get('tags'));
            $attributesResponse = $this->saveAttributes();
            $sr = new EditResponse();
            $sr->setFile($this->file);
            if ($attributesResponse instanceof ErrorList) {
                $sr->setError($attributesResponse);
            } else {
                $this->flash('success', t('File updated successfully.'));
            }
            return new JsonResponse($sr);
        } else {
            throw new \Exception(t('Access Denied.'));
        }
    }
}


