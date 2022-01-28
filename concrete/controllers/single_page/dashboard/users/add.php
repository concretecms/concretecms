<?php

namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\Group\GroupList;
use Imagine\Image\Box;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Attribute\Key\UserKey as UserAttributeKey;
use Imagine\Image\ImagineInterface;

class Add extends DashboardPageController
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return void
     */
    public function view()
    {
        $locales = Localization::getAvailableInterfaceLanguageDescriptions();
        $attribs = UserAttributeKey::getRegistrationList();
        $assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();
        $gl = new GroupList();
        $gArray = $gl->getPagination()->setMaxPerPage(10000)->getCurrentPageResults();

        $this->set('valc', $this->app->make('helper/concrete/validation'));
        $this->set('ih', $this->app->make('helper/concrete/ui'));
        $this->set('av', $this->app->make('helper/concrete/avatar'));
        $this->set('dtt', $this->app->make('helper/form/date_time'));
        $this->set('gArray', $gArray);
        $this->set('assignment', $assignment);
        $this->set('locales', $locales);
        $this->set('attribs', $attribs);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function submit()
    {
        $assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();
        $postRequest = $this->request->request;
        $username = trim($postRequest->get('uName'));
        $username = preg_replace("/\s+/", ' ', $username);
        $postRequest->set('uName', $username);

        $password = $postRequest->get('uPassword');

        $this->app->make('validator/user/name')->isValid($username, $this->error);
        $this->app->make('validator/password')->isValid($password, $this->error);
        $this->app->make('validator/user/email')->isValid($postRequest->get('uEmail'), $this->error);

        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        /** @var \Concrete\Core\Entity\Attribute\Key\UserKey[] $aks */
        $aks = UserAttributeKey::getRegistrationList();

        foreach ($aks as $uak) {
            $controller = $uak->getController();
            $validator = $controller->getValidator();
            $response = $validator->validateSaveValueRequest(
                $controller,
                $this->request,
                $uak->isAttributeKeyRequiredOnRegister()
            );
            if (!$response->isValid()) {
                $error = $response->getErrorObject();
                $this->error->add($error);
            }
        }

        if (!$this->error->has()) {
            // do the registration
            $data = ['uName' => $username, 'uPassword' => $password, 'uEmail' => $postRequest->get('uEmail'), 'uDefaultLanguage' => $postRequest->get('uDefaultLanguage',''), 'uHomeFileManagerFolderID' => ($postRequest->get('uHomeFileManagerFolderID', 0) == 0 ? '' : $postRequest->get('uHomeFileManagerFolderID'))];
            $uo = $this->app['user/registration']->create($data);
            if (is_object($uo)) {
                if ($assignment->allowEditAvatar()) {

                    if ($this->request->files->has('uAvatar') && is_uploaded_file($this->request->files->get('uAvatar',['tmp_name'=>null])['tmp_name'])) {
                        $config = $this->app->make('config');
                        $image = app(ImagineInterface::class)->open($this->request->files->get('uAvatar')['tmp_name']);
                        $image = $image->thumbnail(new Box(
                            $config->get('concrete.icons.user_avatar.width'),
                            $config->get('concrete.icons.user_avatar.height')
                        ));
                        $uo->updateUserAvatar($image);
                    }
                }

                $saveAttributes = [];
                foreach ($aks as $uak) {
                    if (in_array($uak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) {
                        $saveAttributes[] = $uak;
                    }
                }

                if (count($saveAttributes) > 0) {
                    $uo->saveUserAttributesForm($saveAttributes);
                }

                $groupRepo = $this->app->make(GroupRepository::class);

                $gIDs = [];
                if (!empty($postRequest->get('gID')) && is_array($postRequest->get('gID'))) {
                    foreach ($postRequest->get('gID') as $gID) {
                        $gx = $groupRepo->getGroupById($gID);
                        $gxp = new Checker($gx);
                        if ($gxp->canAssignGroup()) {
                            $gIDs[] = $gID;
                        }
                    }
                }

                $uo->updateGroups($gIDs);
                $uID = $uo->getUserID();

                return $this->buildRedirect(['/dashboard/users/search', 'edit', $uID, 'created']);
            }

            $this->error->add(t('An error occurred while trying to create the account.'));
            $this->set('error', $this->error);
        } else {
            $this->view();
        }
        return;
    }
}
