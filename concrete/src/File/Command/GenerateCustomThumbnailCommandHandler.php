<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\File\File;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\Support\Facade\Application;

class GenerateCustomThumbnailCommandHandler
{

    public function __invoke(GenerateCustomThumbnailAsyncCommand $command)
    {
        $app = Application::getFacadeApplication();
        /** @var BasicThumbnailer $basicThumbnailer */
        $basicThumbnailer = $app->make(BasicThumbnailer::class);
        $file = File::getByID($command->getFileID());
        $basicThumbnailer->processThumbnail(false, $file, $command->getMaxWidth(), $command->getMaxHeight(), $command->isCrop());
    }

}