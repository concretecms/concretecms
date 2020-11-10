<?php
namespace Concrete\Core\File;

use Concrete\Core\File\ExternalFileProvider\ExternalFileEntry;
use League\Fractal\TransformerAbstract;

class ExternalFileTransformer extends TransformerAbstract
{
    /**
     * Basic transforming of an external file into an array
     *
     * @param ExternalFileEntry $file
     * @return array
     */
    public function transform(ExternalFileEntry $fileEntry)
    {
        return (array) $file->jsonSerialize();
    }

}
