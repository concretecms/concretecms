<?php
namespace Concrete\Core\File;

use League\Fractal\TransformerAbstract;
use Concrete\Core\Entity\File\File as FileEntity;

/**
 * @since 8.5.2
 */
class FileTransformer extends TransformerAbstract
{
    /**
     * Basic transforming of a file entity into an array
     *
     * @param FileEntity $file
     * @return array
     */
    public function transform(FileEntity $file)
    {
        return (array) $file->getRecentVersion()->getJSONObject();
    }

}
