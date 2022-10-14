<?php

namespace Concrete\Core\Api\Controller;

use Concrete\Core\Http\Request;
use Concrete\Core\Site\Service;
use Concrete\Core\Site\SiteList;
use Concrete\Core\Api\ApiController;
use Concrete\Core\Api\Fractal\Transformer\SiteTransformer;
use Concrete\Core\Api\Resources;
use League\Fractal\Resource\Collection;

class Sites extends ApiController
{

    /**
     * @var Service
     */
    protected $siteService;

    public function __construct(Service $siteService, Request $request)
    {
        $this->siteService = $siteService;
        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/sites/{siteID}",
     *     tags={"sites"},
     *     summary="Find a site by its ID",
     *     security={
     *         {"clientCredentials": {"sites:read"}}
     *     },
     *     @OA\Parameter(
     *         name="siteID",
     *         in="path",
     *         description="ID of Site to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Site"),
     *     ),
     * )
     */
    public function read($siteID)
    {
        $siteID = (int) $siteID;
        $site = $this->siteService->getByID($siteID);
        if ($site) {
            $siteTransformer = $this->app->make(SiteTransformer::class);
            // Include everything if we're getting a single site.
            $siteTransformer->setDefaultIncludes([Resources::RESOURCE_LOCALES, Resources::RESOURCE_CUSTOM_ATTRIBUTES]);
            return $this->transform($site, $siteTransformer, Resources::RESOURCE_SITES);
        } else {
            return $this->error(t('Site not found.'), 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/sites",
     *     tags={"sites"},
     *     summary="Returns a list of site objects, sorted by date added ascending.",
     *     security={
     *         {"clientCredentials": {"sites:read"}}
     *     },
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", enum={"locales","custom_attributes"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Site")
     *         ),
     *     ),
     * )
     */
    public function listSites()
    {
        $list = new SiteList();
        $list->sortBy('siteID', 'asc');
        $results = $list->getResults();

        $resource = new Collection($results, new SiteTransformer(), Resources::RESOURCE_SITES);
        return $resource;
    }

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/sites/default",
     *     tags={"sites"},
     *     summary="Retrieve the default site for your Concrete installation",
     *     security={
     *         {"clientCredentials": {"sites:read"}}
     *     },
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", enum={"locales","custom_attributes"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Site"),
     *     ),
     * )
     */
    public function getDefault()
    {
        $site = $this->siteService->getDefault();
        $siteTransformer = $this->app->make(SiteTransformer::class);
        return $this->transform($site, $siteTransformer, Resources::RESOURCE_SITES);
    }


}
