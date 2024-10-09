<?php

namespace Concrete\Core\Api\Controller;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Page\Command\DeletePageCommand;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Search\ColumnSet\Column\DateLastModifiedColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\SitemapDisplayOrderColumn;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Pagination\PagerPagination;
use Concrete\Core\User\User;
use Concrete\Core\Api\ApiController;
use Concrete\Core\Api\Attribute\AttributeValueMapFactory;
use Concrete\Core\Api\Fractal\Transformer\PageTransformer;
use Concrete\Core\Api\Resources;
use Concrete\Core\Api\Traits\SetListLimitFromQueryTrait;
use Concrete\Core\Api\Traits\SupportsCursorTrait;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\ResourceAbstract;

class Pages extends ApiController
{

    use SetListLimitFromQueryTrait;
    use SupportsCursorTrait;

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/pages/{pageID}",
     *     tags={"pages"},
     *     summary="Find a page by its ID",
     *     security={
     *         {"authorization": {"pages:read"}}
     *     },
     *     @OA\Parameter(
     *         name="pageID",
     *         in="path",
     *         description="ID of Page to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", enum={"custom_attributes","areas","files","content","areas.content"})
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="version",
     *         in="query",
     *         description="Return the active version (default) or the most recent version",
     *         @OA\Schema(
     *             type="string",
     *              enum={"active","recent"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful Page operation",
     *         @OA\JsonContent(ref="#/components/schemas/Page"),
     *     ),
     * )
     */
    public function read($pageID)
    {
        $pageID = (int)$pageID;
        $version = 'ACTIVE';
        if ($this->request->query->has('version') && $this->request->query->get('version') == 'recent') {
            $version = 'RECENT';
        }
        $page = Page::getByID($pageID, $version);
        if ($page && !$page->isError()) {
            if ($page->isInTrash()) {
                return $this->error(t('This page is pending deletion.'), 403);
            }
            $permissions = new Checker($page);
            if ($version === 'RECENT') {
                $canViewPage = $permissions->canViewPageVersions();
            } else {
                $canViewPage = $permissions->canViewPage();
            }
            if ($canViewPage) {
                $pageTransformer = new PageTransformer();
                return $this->transform($page, $pageTransformer, Resources::RESOURCE_PAGES);
            } else {
                if ($version === 'RECENT') {
                    return $this->error(t('You do not have access to read the most recent unapproved version of page.'), 401);
                } else {
                    return $this->error(t('You do not have access to read properties about this page.'), 401);
                }
            }
        } else {
            return $this->error(t('Page not found.'), 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/pages",
     *     tags={"pages"},
     *     summary="Returns a list of page objects, sorted by last updated descending. The most recent Page objects appear first.",
     *     security={
     *         {"authorization": {"pages:read"}}
     *     },
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="The number of objects to return. Must be 100 or less. Defaults to 10.",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="after",
     *         in="query",
     *         description="The ID of the current object to start at.",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", enum={"custom_attributes","areas","files","content","areas.content"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Page")
     *         ),
     *     ),
     * )
     */
    public function listPages()
    {
        $list = new PageList();

        // Ignore external links (note: this should be the default, it's dumb that it's not.)
        $list->getQueryObject()->andWhere('cPointerExternalLink is null');

        $list->setPermissionsChecker(
            function ($page) {
                $permissions = new Checker($page);
                return $permissions->canViewPage();
            }
        );

        $sortColumn = new DateLastModifiedColumn();
        $sortColumn->setColumnSortDirection('desc');

        $this->setupSortAndCursor(
            $this->request,
            $list,
            $sortColumn,
            function ($currentCursor) {
                $page = Page::getByID($currentCursor);
                return $page;
            }
        );

        $pagination = new PagerPagination($list);
        $this->addLimitToPaginationIfSpecified($pagination, $this->request);

        $results = $pagination->getCurrentPageResults();
        $resource = new Collection($results, new PageTransformer(), Resources::RESOURCE_PAGES);
        $this->addCursorToResource($results, $this->request, 'getCollectionID', $resource);

        return $resource;
    }


    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/pages/{pageID}/children",
     *     tags={"pages"},
     *     summary="Allows the traversal of the sitemap by showing child pages of a particular page.",
     *     security={
     *         {"authorization": {"pages:read"}}
     *     },
     *     @OA\Parameter(
     *         name="pageID",
     *         required=true,
     *         in="path",
     *         description="Display children beneath a particular parent.",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", enum={"custom_attributes","areas","files","content","areas.content"})
     *         )
     *     ),
     *     @OA\Response(response="200",
     *     description="An array of Page objects.")
     * )
     */
    public function sitemapPages($pageID)
    {
        $list = new PageList();

        $list->setPermissionsChecker(
            function ($page) {
                $permissions = new Checker($page);
                return $permissions->canViewPage();
            }
        );

        $sortColumn = new SitemapDisplayOrderColumn();
        $list->sortBySearchColumn($sortColumn);

        $parentPage = Page::getByID($pageID);
        if ($parentPage && !$parentPage->isError()) {
            $parentPagePermissions = new Checker($parentPage);
            if (!$parentPagePermissions->canViewPage()) {
                return $this->error(t('You do not have permission to view the specified parent page.'), 403);
            } else {
                $list->filterByParentID($parentPage->getCollectionID());
            }
        } else {
            unset($parentPage);
        }

        if (!isset($parentPage)) {
            return $this->error(t('Invalid parent page specified.'), 401);
        }

        $results = $list->getResults();
        $resource = new Collection($results, new PageTransformer(), Resources::RESOURCE_PAGES);
        $this->addCursorToResource($results, $this->request, 'getCollectionID', $resource);

        return $resource;
    }

    /**
     * @OA\Post(
     *     path="/ccm/api/1.0/pages",
     *     tags={"pages"},
     *     summary="Adds a page object.",
     *     security={
     *         {"authorization": {"pages:add"}}
     *     },
     *     @OA\RequestBody(ref="#/components/requestBodies/NewPage"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful files operation",
     *         @OA\JsonContent(ref="#/components/schemas/Page"),
     *     ),
     * )
     */
    public function add()
    {
        $e = $this->app->make('error');
        $data = json_decode($this->request->getContent(), true);
        $parent = (int)$data['parent'];
        $parentPage = null;

        if (!$data['name']) {
            $e->add(t('You must give your page a name.'));
        }

        if (!$parent) {
            $e->add(t('Invalid parent page'));
        } else {
            $parentPage = Page::getByID($parent, 'ACTIVE');
            if ($parentPage && $parentPage->isError()) {
                $e->add(t('Parent page not found.'));
            }
        }

        $type = Type::getByHandle($data['type']);
        if (!$type) {
            $e->add(t('Invalid page type'));
        }
        $template = Template::getByHandle($data['template']);
        if (!$template) {
            $e->add(t('Invalid page template'));
        }

        if ($e->has()) {
            return $this->error($e, 401);
        }

        $parentPagePermissions = new Checker($parentPage);
        if (!$parentPagePermissions->canAddSubCollection($type)) {
            return $this->error(
                t(
                    'You do not have permission to add pages of the type %s beneath this parent page',
                    $type->getPageTypeName()
                ),
                403
            );
        }

        $pageData['name'] = $data['name'];
        if (!empty($data['description'])) {
            $pageData['description'] = $data['description'];
        }

        $newPage = $parentPage->add($type, $pageData, $template);

        if (isset($data['attributes'])) {
            $category = $this->app->make(PageCategory::class);
            $attributeValueMapFactory = $this->app->make(AttributeValueMapFactory::class);
            $attributeMap = $attributeValueMapFactory->createFromRequestData($category, $data['attributes']);
            foreach ($attributeMap->getEntries() as $entry) {
                $newPage->setAttribute($entry->getAttributeKey(), $entry->getAttributeValue());
            }
        }

        return $this->transform($newPage, new PageTransformer(), Resources::RESOURCE_PAGES);
    }

    /**
     * @OA\Put(
     *     path="/ccm/api/1.0/pages/{pageID}",
     *     tags={"pages"},
     *     summary="Update a page by its ID",
     *     security={
     *         {"authorization": {"pages:update"}}
     *     },
     *     @OA\Parameter(
     *         name="pageID",
     *         in="path",
     *         description="ID of page to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/UpdatedPage"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Page"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="You do not have the proper permissions to update this resource."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     * )
     */
    public function update($cID)
    {
        $page = Page::getByID($cID, 'RECENT');
        if (!$page) {
            return $this->error(t('Page not found'), 404);
        }

        if ($page->isError()) {
            if ($page->getError() == COLLECTION_NOT_FOUND) {
                return $this->error(t('Page not found'), 404);
            }
        }

        $checker = new Checker($page);
        if (!$checker->canEditPageContents()) {
            return $this->error(t('You do not have access to edit this page.', 401));
        }

        $body = json_decode($this->request->getContent(), true);
        $e = $this->app->make('error');

        if (isset($body['type'])) {
            $type = Type::getByHandle($body['type']);
            if (!$type) {
                $e->add(t('Invalid page type'));
            }
        }
        if (isset($body['template'])) {
            $template = Template::getByHandle($body['template']);
            if (!$template) {
                $e->add(t('Invalid page template'));
            }
        }

        if ($e->has()) {
            return $this->error($e, 401);
        }

        $page = $page->getVersionToModify();

        $data = [];
        if (isset($body['name'])) {
            $data['cName'] = $body['name'];
        }
        if (isset($body['description'])) {
            $data['cDescription'] = $body['description'];
        }
        if (isset($template)) {
            $data['pTemplateID'] = $template->getPageTemplateID();
        }
        if (isset($type)) {
            $data['ptID'] = $type->getPageTypeID();
        }
        if (isset($body['attributes'])) {
            $category = $this->app->make(PageCategory::class);
            $attributeValueMapFactory = $this->app->make(AttributeValueMapFactory::class);
            $attributeMap = $attributeValueMapFactory->createFromRequestData($category, $body['attributes']);
            foreach ($attributeMap->getEntries() as $entry) {
                $page->setAttribute($entry->getAttributeKey(), $entry->getAttributeValue());
            }
        }

        $page->update($data);

        return $this->transform($page, new PageTransformer(), Resources::RESOURCE_PAGES);
    }

    /**
     * @OA\Delete(
     *     path="/ccm/api/1.0/pages/{pageID}",
     *     tags={"pages"},
     *     summary="Delete a page by its ID",
     *     security={
     *         {"authorization": {"pages:delete"}}
     *     },
     *     @OA\Parameter(
     *         name="pageID",
     *         in="path",
     *         description="ID of page to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/DeletedResponse"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="You do not have the proper permissions to delete this resource."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     * )
     */
    public function delete($pageID)
    {
        $pageID = (int)$pageID;
        $page = Page::getByID($pageID, 'ACTIVE');
        if ($page && !$page->isError()) {
            if ($page->isInTrash()) {
                return $this->error(t('This page is pending deletion.'), 403);
            }
            $permissions = new Checker($page);
            if ($permissions->canDeletePage()) {
                $command = new DeletePageCommand($page->getCollectionID(), $this->app->make(User::class)->getUserID());
                $this->app->executeCommand($command);
                return $this->deleted(Resources::RESOURCE_PAGES, $pageID);
            } else {
                return $this->error(t('You do not have access to delete this page.'), 401);
            }
        } else {
            return $this->error(t('Page not found.'), 404);
        }
    }


}
