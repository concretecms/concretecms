<?php

namespace Concrete\Controller\Dialog\SiteType;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait as KeySelectorControllerTrait;
use Concrete\Core\Entity\Site\Skeleton;
use Concrete\Core\Entity\Site\Type as SiteType;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Site\Type\Skeleton\Service as SkeletonService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Attributes extends BackendInterfaceController
{
    use KeySelectorControllerTrait;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/site_type/attributes';

    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * @var SiteType|null
     */
    protected $requestSiteType;

    /**
     * @var Skeleton
     */
    private $skeleton;

    public function __construct(CategoryService $attributeCategoryService)
    {
        parent::__construct();

        $categoryEntity = $attributeCategoryService->getByHandle('site_type');
        $this->category = $categoryEntity->getAttributeKeyCategory();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $p = Page::getByPath('/dashboard/system/multisite/types');
        $permissions = new Checker($p);
        $skeleton = $this->getTypeSkeleton();

        return $skeleton !== null && $permissions->canViewPage();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait::getObjects()
     */
    public function getObjects(): array
    {
        return [$this->getTypeSkeleton()];
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

    public function canEditAttributeKey(int $akID): bool
    {
        return true;
    }

    public function view($stID)
    {
        $keySelector = $this->app->make(ElementManager::class)->get('attribute/component/key_selector', [
            'category' => $this->getCategory()
        ]);
        $controller = $keySelector->getElementController();
        $controller->setSelectAttributeUrl($this->action('get_attribute'));
        $controller->setObjects($this->getObjects());
        $this->set('keySelector', $keySelector);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $attributesResponse = $this->saveAttributes();
            $message = new EditResponse();
            if ($attributesResponse instanceof ErrorList) {
                $message->setError($attributesResponse);
            } else {
                $message->setMessage(t('Attributes updated successfully.'));
            }

            return new JsonResponse($message);
        }
    }

    protected function getTypeSkeleton(): Skeleton
    {
        if (!$this->skeleton) {
            $skeletonService = $this->app->make(SkeletonService::class);
            $siteType = $this->getSiteTypeFromRequest();

            $this->skeleton = $skeletonService->getSkeleton($siteType);
        }

        return $this->skeleton;
    }

    protected function getSiteTypeFromRequest()
    {
        if (!isset($this->requestSiteType)) {
            if ($this->request->attributes->has('stID')) {
                $this->requestSiteType = $this->app['site/type']->getByID($this->request->attributes->get('stID'));
            }
        }

        if (!$this->requestSiteType) {
            throw new UserMessageException(t('Invalid site type id.'));
        }

        return $this->requestSiteType;
    }
}
