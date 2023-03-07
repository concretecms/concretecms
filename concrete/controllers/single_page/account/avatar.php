<?php
namespace Concrete\Controller\SinglePage\Account;

use Concrete\Controller\SinglePage\Account\EditProfile as AccountProfileEditPageController;
use Concrete\Core\User\Command\UpdateUserAvatarCommand;
use Concrete\Core\User\Component\AvatarCropperInstanceFactory;
use Concrete\Core\User\UserInfo;
use Symfony\Component\HttpFoundation\JsonResponse;

class Avatar extends AccountProfileEditPageController
{
    public function view()
    {
        parent::view();
        $instanceFactory = $this->app->make(AvatarCropperInstanceFactory::class);
        $instance = $instanceFactory->createInstance();
        $instance->setUploadUrl($this->action('save_avatar'));
        $this->set('avatarCropperInstance', $instance);
        $this->set('token', $this->app->make('token'));
        $this->set('width', $this->app->make('config')->get('concrete.icons.user_avatar.width'));
        $this->set('height', $this->app->make('config')->get('concrete.icons.user_avatar.height'));
    }

    public function save_avatar()
    {
        $result = [
            'success' => false,
            'avatar' => null,
        ];

        /**
         * @var $instanceFactory AvatarCropperInstanceFactory
         */
        $instanceFactory = $this->app->make(AvatarCropperInstanceFactory::class);
        $instance = $instanceFactory->createInstanceFromRequest($this->request);
        if (!$instanceFactory->instanceMatchesAccessToken($instance, $this->request->get('accessToken') ?? '')) {
            $result['error'] = true;
            $result['message'] = app('token')->getErrorMessage();
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

        $this->flash('success', t('Profile picture saved.'));
        return new JsonResponse($result, $result['success'] ? 200 : 400);
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
