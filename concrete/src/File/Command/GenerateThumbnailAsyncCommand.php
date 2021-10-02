<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Command\AsyncCommandInterface;

class GenerateThumbnailAsyncCommand extends GeneratedThumbnailCommand implements AsyncCommandInterface
{

    public static function getHandler(): string
    {
        return GenerateThumbnailCommandHandler::class;
    }

}
