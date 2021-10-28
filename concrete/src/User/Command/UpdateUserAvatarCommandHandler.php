<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\User\Command\UpdateUserAvatarCommand;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

class UpdateUserAvatarCommandHandler implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_USERS;
    }

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function __invoke(UpdateUserAvatarCommand $command)
    {
        /** @var ImageInterface $imagine */
        $imagine = $this->app->make(ImagineInterface::class);
        $image = $imagine->open($command->getAvatarFile()->getPathname());

        $palette = new RGB();

        // Give our image a white background
        $canvas = $imagine->create($image->getSize(), $palette->color('fff'));
        $canvas->paste($image, new Point(0, 0));

        // Update the avatar
        $command->getUser()->updateUserAvatar($canvas);

        $this->logger->notice(t('User %s (ID %s) updated user avatar to filename %s',
            $command->getUser()->getUserName(),
            $command->getUser()->getUserID(),
            $command->getAvatarFile()->getClientOriginalName()
        ));
    }


}