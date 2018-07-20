<?php
namespace Concrete\Controller\SinglePage\Account;

use Concrete\Controller\SinglePage\Account\EditProfile as AccountProfileEditPageController;
use Concrete\Core\User\UserInfo;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Point;
use Symfony\Component\HttpFoundation\JsonResponse;

class Avatar extends AccountProfileEditPageController
{
    public function view()
    {
        parent::view();
        $this->set('token', $this->app->make('token'));
        $this->requireAsset('core/avatar');
    }

    public function save_avatar()
    {
        $result = [
            'success' => false,
            'avatar' => null
        ];
        $this->view();
        $token = $this->app->make('token');
        if (!$token->validate('avatar/save_avatar')) {
            $this->redirect('/profile/avatar', 'token');
        }

        /** @var UserInfo $profile */
        $profile = $this->get('profile');

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $this->request->files->get('file');
        if ($file) {
            $image = \Image::open($file->getPathname());

            $palette = new \Imagine\Image\Palette\RGB();

            // Give our image a white background
            $canvas = \Image::create($image->getSize(), $palette->color('fff'));
            $canvas->paste($image, new Point(0, 0));

            // Update the avatar
            $profile->updateUserAvatar($canvas);

            // Update the result
            $result['success'] = true;
            $result['avatar'] = $profile->getUserAvatar()->getPath() . '?' . time();
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

        if (isset($_POST['thumbnail']) && strlen($_POST['thumbnail'])) {
            $thumb = base64_decode($_POST['thumbnail']);
            $image = \Image::load($thumb);
            $profile->updateUserAvatar($image);
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
            $av = $this->get('av');

            $service = \Core::make('user/avatar');
            $service->removeAvatar($profile);
            $this->redirect('/account/avatar', 'deleted');
        }
    }
}
