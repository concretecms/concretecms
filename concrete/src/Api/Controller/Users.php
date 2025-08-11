<?php

namespace Concrete\Core\Api\Controller;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\PagerPagination;
use Concrete\Core\User\RegistrationService;
use Concrete\Core\User\Search\ColumnSet\Column\DateAddedColumn;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\User\UserList;
use Concrete\Core\Api\ApiController;
use Concrete\Core\Api\Attribute\AttributeValueMapFactory;
use Concrete\Core\Api\Fractal\Transformer\UserTransformer;
use Concrete\Core\Api\Resources;
use Concrete\Core\Api\Traits\SetListLimitFromQueryTrait;
use Concrete\Core\Api\Traits\SupportsCursorTrait;
use League\Fractal\Resource\Collection;

class Users extends ApiController
{

    use SetListLimitFromQueryTrait;
    use SupportsCursorTrait;

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/users/{userID}",
     *     tags={"users"},
     *     summary="Find a user by its ID",
     *     security={
     *         {"authorization": {"users:read"}}
     *     },
     *     @OA\Parameter(
     *         name="userID",
     *         in="path",
     *         description="ID of user to return",
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
     *             @OA\Items(type="string", enum={"custom_attributes","groups"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful user operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     * )
     */
    public function read($uID)
    {
        $uID = (int)$uID;
        $repository = $this->app->make(UserInfoRepository::class);
        $user = $repository->getByID($uID);
        if (!$user) {
            return $this->error(t('User not found.'), 404);
        } else {
            $permissions = new Checker($user);
            if (!$permissions->canViewUser()) {
                return $this->error(t('You do not have access to read properties about this user.'), 401);
            }
        }

        // Include user groups if we're getting a single user.
        $userTransformer = $this->app->make(UserTransformer::class);
        $userTransformer->setDefaultIncludes([Resources::RESOURCE_GROUPS]);

        return $this->transform($user, $userTransformer, Resources::RESOURCE_USERS);
    }

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/users",
     *     tags={"users"},
     *     summary="Returns a list of user objects, sorted by date added descending. The most recent user objects appear first.",
     *     security={
     *         {"authorization": {"users:read"}}
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
     *             @OA\Items(type="string", enum={"custom_attributes","groups"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         ),
     *     ),
     * )
     */
    public function listUsers()
    {
        $list = new UserList();
        $list->setPermissionsChecker(
            function ($user) {
                $fp = new Checker($user);
                return $fp->canViewUser();
            }
        );

        $dateAddedColumn = new DateAddedColumn();
        $dateAddedColumn->setColumnSortDirection('desc');
        $this->setupSortAndCursor(
            $this->request,
            $list,
            $dateAddedColumn,
            function ($currentCursor) {
                $repository = $this->app->make(UserInfoRepository::class);
                $user = $repository->getByID($currentCursor);
                return $user;
            }
        );

        $pagination = new PagerPagination($list);
        $this->addLimitToPaginationIfSpecified($pagination, $this->request);

        $results = $pagination->getCurrentPageResults();
        $resource = new Collection($results, $this->app->make(UserTransformer::class), Resources::RESOURCE_USERS);
        $this->addCursorToResource($results, $this->request, 'getUserID', $resource);

        return $resource;
    }

    /**
     * @OA\Post(
     *     path="/ccm/api/1.0/users",
     *     tags={"users"},
     *     summary="Adds a user object.",
     *     security={
     *         {"authorization": {"users:add"}}
     *     },
     *     @OA\RequestBody(ref="#/components/requestBodies/NewUser"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful files operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     * )
     */
    public function add()
    {
        $content = json_decode($this->request->getContent(), true);

        $e = $this->app->make('error');
        $this->app->make('validator/user/name')->isValid($content['username'], $e);
        $this->app->make('validator/user/email')->isValid($content['email'], $e);
        $this->app->make('validator/password')->isValid($content['password'], $e);

        if ($e->has()) {
            return $this->error($e, 401);
        }

        $data = ['uName' => $content['username'], 'uEmail' => $content['email'], 'uPassword' => $content['password']];
        if (isset($content['language']) && $content['language'] !== '') {
            $data['uDefaultLanguage'] = $content['language'];
        }
        $registrationService = $this->app->make(RegistrationService::class);
        $user = $registrationService->create($data);
        return $this->transform($user, new UserTransformer(), Resources::RESOURCE_USERS);
    }


    /**
     * @OA\Delete(
     *     path="/ccm/api/1.0/users/{userID}",
     *     tags={"users"},
     *     summary="Delete a user by its ID",
     *     security={
     *         {"authorization": {"users:delete"}}
     *     },
     *     @OA\Parameter(
     *         name="userID",
     *         in="path",
     *         description="ID of user to delete",
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
     *         description="User not found"
     *     ),
     * )
     */
    public function delete($uID)
    {
        /**
         * @var $repository UserInfoRepository
         */
        $repository = $this->app->make(UserInfoRepository::class);
        $user = $repository->getByID($uID);
        if (!$user) {
            return $this->error(t('User not found'), 404);
        }

        $checker = new Checker($user);
        if (!$checker->canDeleteUser()) {
            return $this->error(t('You do not have access to delete this user.', 401));
        }

        $user->delete();

        return $this->deleted(Resources::RESOURCE_USERS, $uID);
    }

    /**
     * @OA\Put(
     *     path="/ccm/api/1.0/users/{userID}",
     *     tags={"users"},
     *     summary="Update a user by its ID",
     *     security={
     *         {"authorization": {"users:update"}}
     *     },
     *     @OA\Parameter(
     *         name="userID",
     *         in="path",
     *         description="ID of user to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/UpdatedUser"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="You do not have the proper permissions to update this resource."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     * )
     */
    public function update($uID)
    {
        /**
         * @var $repository UserInfoRepository
         */
        $repository = $this->app->make(UserInfoRepository::class);
        $user = $repository->getByID($uID);
        if (!$user) {
            return $this->error(t('User not found'), 404);
        }

        $checker = new Checker($user);
        if (!$checker->canEditUser()) {
            return $this->error(t('You do not have access to delete this user.', 401));
        }

        $content = json_decode($this->request->getContent(), true);
        $data = [];
        $e = $this->app->make('error');
        if (isset($content['username'])) {
            $this->app->make('validator/user/name')->isValid($content['username'], $e);
            $data['uName'] = $content['username'];
        }
        if (isset($content['email'])) {
            $this->app->make('validator/user/email')->isValid($content['email'], $e);
            $data['uEmail'] = $content['email'];
        }

        if (isset($content['language']) && $content['language'] !== '') {
            $data['uDefaultLanguage'] = $content['language'];
        }

        if ($e->has()) {
            return $this->error($e, 401);
        }

        if (count($data) > 0) {
            $user->update($data);
        }

        if (isset($content['attributes'])) {
            $category = $this->app->make(UserCategory::class);
            $attributeValueMapFactory = $this->app->make(AttributeValueMapFactory::class);
            $attributeMap = $attributeValueMapFactory->createFromRequestData($category, $content['attributes']);
            foreach ($attributeMap->getEntries() as $entry) {
                $user->setAttribute($entry->getAttributeKey(), $entry->getAttributeValue());
            }
        }

        return $this->transform($user, new UserTransformer(), Resources::RESOURCE_USERS);
   }

    /**
     * @OA\Post(
     *     path="/ccm/api/1.0/users/{userID}/change_password",
     *     tags={"users"},
     *     summary="Change a user's password",
     *     security={
     *         {"authorization": {"users:update"}}
     *     },
     *     @OA\Parameter(
     *         name="userID",
     *         in="path",
     *         description="ID of user to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/ChangeUserPassword"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="You do not have the proper permissions to update this resource."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     * )
     */
    public function changePassword($uID)
    {
        /**
         * @var $repository UserInfoRepository
         */
        $repository = $this->app->make(UserInfoRepository::class);
        $user = $repository->getByID($uID);
        if (!$user) {
            return $this->error(t('User not found'), 404);
        }

        $checker = new Checker($user);
        if (!$checker->canEditUser()) {
            return $this->error(t('You do not have access to change the password for this user.', 401));
        }

        $e = $this->app->make('error');
        $password = $this->request->request->get('password');
        $this->app->make('validator/password')->isValid($password, $e);

        if ($e->has()) {
            return $this->error($e, 401);
        }

        $user->changePassword($password);

        return $this->transform($user, new UserTransformer(), Resources::RESOURCE_USERS);
    }

}
