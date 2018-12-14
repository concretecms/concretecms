<?php
/**
 * Created by PhpStorm.
 * User: derekcameron
 * Date: 2018/12/13
 * Time: 19:38
 */

namespace Concrete\Core\File;

use League\Fractal\TransformerAbstract;
use Concrete\Core\Entity\File\File as FileEntity;

class FileTransformer extends TransformerAbstract
{
    public function transform(FileEntity $file)
    {
        return $file->getRecentVersion()->getJSONObject();
    }

}