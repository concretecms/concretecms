<?php
namespace Concrete\Controller\SinglePage\Account;

use Concrete\Controller\SinglePage\Account\EditProfile as AccountProfileEditPageController;
use Concrete\Core\User\Command\UpdateUserAvatarCommand;
use Concrete\Core\User\UserInfo;
use Symfony\Component\HttpFoundation\JsonResponse;

class Avatar extends AccountProfileEditPageController
{
    public function view()
    {
        parent::view();
        $this->set('token', $this->app->make('token'));
    }

    public function save_avatar()
    {
        $result = [
            'success' => false,
            'avatar' => null,
        ];

        $this->view();
        $token = $this->app->make('token');
        if (!$token->validate('avatar/save_avatar', $this->request->query->get('ccm_token'))) {
            $result['error'] = true;
            $result['message'] = $token->getErrorMessage();

            return new JsonResponse($result, 400);
        }

        /** @var UserInfo */
        $profile = $this->get('profile');

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile */
        $file = $this->request->files->get('file');

        if ($file) {
            $command = new UpdateUserAvatarCommand($profile, $file);
            $this->app->executeCommand($command);

            // Update the result
            $result['success'] = true;
            $result['avatar'] = $profile->getUserAvatar()->getPath() . '?' . time();
        } else {
            $result['error'] = true;
            $result['message'] = t('Error while uploading file.');
        }

        return new JsonResponse($result, $result['success'] ? 200 : 400);
    }

    public function save_thumb()
    {
        $this->view();
        $token = $this->app->make('token');

        if (!$token->validate('avatar/save_thumb')) {
            return false;
        }

        $profile = $this->get('profile');
        if (!is_object($profile) || $profile->getUserID() < 1) {
            return false;
        }
        $thumbnail = $this->request->request->get('thumbnail');

        if ($thumbnail) {
            $thumb = base64_decode($thumbnail);
            $image = $this->app->make(ImagineInterface::class)->load($thumb);
            $profile->updateUserAvatar($image);
        } else {
            return false;
        }

        $this->redirect('/account/avatar', 'saved');
    }

    public function saved()
    {
        $this->set('success', 'Avatar updated!');
        $this->view();
    }

    public function deleted()
    {
        $this->set('success', 'Avatar removed.');
        $this->view();
    }

    public function delete()
    {
        $this->view();
        if (!$this->token->validate('delete_avatar')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $profile = $this->get('profile');

            $service = $this->app->make('user/avatar');
            $service->removeAvatar($profile);
            $this->redirect('/account/avatar', 'deleted');
        }
    }
}
